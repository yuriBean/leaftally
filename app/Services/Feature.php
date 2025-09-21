<?php

namespace App\Services;

use App\Enum\PlanFeature;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Feature
{
    /**
     * Accept any actor; if not a real User (e.g., Customer/Vender), go into bypass mode.
     */
    public static function for($actor): self
    {
        return new self($actor instanceof User ? $actor : null);
    }

    public function __construct(private ?User $user) {}

    /** If not a User, we bypass all enforcement */
    protected function bypass(): bool
    {
        return !($this->user instanceof User);
    }

    /**
     * Resolve the company owner's plan safely.
     * - Uses currentPlan() if present.
     * - Falls back to owner->plan (id) if your schema stores an id on users table.
     */
    protected function plan(): ?Plan
    {
        if ($this->bypass()) {
            return null;
        }

        // Prefer the company owner account (staff usually have company_id set)
        $ownerId = $this->user->company_id ?: $this->user->id;
        $owner   = $ownerId === $this->user->id ? $this->user : User::find($ownerId);

        if (!$owner) {
            return null;
        }

        // If your User model exposes currentPlan(), use it.
        if (method_exists($owner, 'currentPlan')) {
            $cp = $owner->currentPlan();
            if ($cp instanceof Plan) {
                return $cp;
            }
            if (is_numeric($cp)) {
                return Plan::find((int) $cp);
            }
            // If it returns an array/stdClass, try to cast to Plan (best-effort)
        }

        // Common pattern: users.plan contains the plan id.
        if (isset($owner->plan) && is_numeric($owner->plan)) {
            return Plan::find((int) $owner->plan);
        }

        // If owner has a relation named "plan", try that.
        if (method_exists($owner, 'plan')) {
            try {
                $related = $owner->plan()->getResults();
                if ($related instanceof Plan) {
                    return $related;
                }
            } catch (\Throwable) {
                // ignore
            }
        }

        return null;
    }

    /**
     * Returns true/false for a feature flag.
     * Accepts either "invoice_enabled" or the base key "invoice" (it will try both).
     */
    public function enabled(string $feature): bool
    {
        // Non-User (customer/vendor) → bypass checks
        if ($this->bypass()) {
            return true;
        }

        // Super admin always allowed regardless of plan presence
        if ($this->user->type === 'super admin') {
            return true;
        }

        $companyKey = $this->user->company_id ?: $this->user->id;
        $cacheKey   = "plan:{$companyKey}:enabled:{$feature}";

        return Cache::remember($cacheKey, 300, function () use ($feature) {
            $plan = $this->plan();
            if (!$plan) {
                return false;
            }

            // 1) direct attribute (e.g., invoice_enabled)
            $val = data_get($plan, $feature);
            if ($val !== null) {
                return $this->boolish($val);
            }

            // 2) tolerant key (e.g., "invoice" <-> "invoice_enabled")
            $alt = Str::endsWith($feature, '_enabled')
                ? Str::beforeLast($feature, '_enabled')
                : "{$feature}_enabled";

            $val = data_get($plan, $alt);
            if ($val !== null) {
                return $this->boolish($val);
            }

            // 3) JSON features (array of names OR map name=>bool)
            $json = $this->normalizeFeaturesArray($plan->features ?? []);
            if (array_key_exists($feature, $json)) {
                return $this->boolish($json[$feature]);
            }
            if (array_key_exists($alt, $json)) {
                return $this->boolish($json[$alt]);
            }

            // 4) JSON as a flat list of enabled names
            if (isset($json[0])) {
                // numeric keys → flat list
                return in_array($feature, $json, true) || in_array($alt, $json, true);
            }

            return false;
        });
    }

    public function quota(string $feature): ?int
    {
        if ($this->bypass()) {
            return null; // treat as unlimited
        }

        $plan = $this->plan();
        if (!$plan) {
            return null;
        }

        $quotaCol = PlanFeature::QUOTAS[$feature] ?? null;
        if (!$quotaCol) {
            return null;
        }

        $value = $plan->{$quotaCol};
        return $value === null ? null : (int) $value; // null or -1 => unlimited by convention
    }

    public function canCreate(string $feature, int $currentUsage): bool
    {
        if (!$this->enabled($feature)) {
            return false;
        }

        $quota = $this->quota($feature);
        if ($quota === null || $quota < 0) {
            return true; // unlimited
        }

        return $currentUsage < $quota;
    }

    /**
     * Convenience for debugging/menus: list of enabled flags.
     * It merges *_enabled columns and JSON features.
     */
    public function enabledList(): array
    {
        if ($this->bypass()) {
            return []; // keep it minimal for non-User actors
        }

        $plan = $this->plan();
        if (!$plan) {
            return [];
        }

        $attrs = method_exists($plan, 'getAttributes') ? $plan->getAttributes() : (array) $plan;

        $fromColumns = collect($attrs)
            ->filter(fn ($v, $k) => Str::endsWith($k, '_enabled'))
            ->mapWithKeys(fn ($v, $k) => [$k => $this->boolish($v)])
            ->filter()
            ->keys()
            ->all();

        $json = $this->normalizeFeaturesArray($plan->features ?? []);
        $fromJson = [];

        if ($json) {
            // Map shape (name => bool)
            $fromJson = collect($json)
                ->mapWithKeys(fn ($v, $k) => is_string($k) ? [$k => $this->boolish($v)] : [])
                ->filter()
                ->keys()
                ->all();

            // Flat list shape ([name, name, ...])
            if (isset($json[0])) {
                $fromJson = array_unique(array_merge($fromJson, array_values(array_filter($json, 'is_string'))));
            }
        }

        return array_values(array_unique(array_merge($fromColumns, $fromJson)));
    }

    public function invalidateCache(): void
    {
        // No-op for now (could use cache tags if desired)
    }

    /** ---------------- helpers ---------------- */

    protected function boolish(mixed $v): bool
    {
        if (is_bool($v)) return $v;
        if (is_numeric($v)) return (int) $v === 1;
        if (is_string($v)) {
            $v = strtolower(trim($v));
            return in_array($v, ['1', 'true', 'on', 'yes'], true);
        }
        return (bool) $v;
    }

    /**
     * Supports JSON stored as:
     * - associative map: {"invoice_enabled": true, ...}
     * - flat list: ["invoice_enabled","payroll_enabled", ...]
     */
    protected function normalizeFeaturesArray($raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $raw = $decoded;
            } else {
                return [];
            }
        }

        if (!is_array($raw)) {
            return [];
        }

        return $raw;
    }
}
