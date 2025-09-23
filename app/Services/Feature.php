<?php

namespace App\Services;

use App\Enum\PlanFeature;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Feature
{
    public static function for($actor): self
    {
        return new self($actor instanceof User ? $actor : null);
    }

    public function __construct(private ?User $user) {}

    protected function bypass(): bool
    {
        return !($this->user instanceof User);
    }

    protected function plan(): ?Plan
    {
        if ($this->bypass()) {
            return null;
        }

        $ownerId = $this->user->company_id ?: $this->user->id;
        $owner   = $ownerId === $this->user->id ? $this->user : User::find($ownerId);

        if (!$owner) {
            return null;
        }

        if (method_exists($owner, 'currentPlan')) {
            $cp = $owner->currentPlan();
            if ($cp instanceof Plan) {
                return $cp;
            }
            if (is_numeric($cp)) {
                return Plan::find((int) $cp);
            }
        }

        if (isset($owner->plan) && is_numeric($owner->plan)) {
            return Plan::find((int) $owner->plan);
        }

        if (method_exists($owner, 'plan')) {
            try {
                $related = $owner->plan()->getResults();
                if ($related instanceof Plan) {
                    return $related;
                }
            } catch (\Throwable) {
            }
        }

        return null;
    }

    public function enabled(string $feature): bool
    {
        if ($this->bypass()) {
            return true;
        }

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

            $val = data_get($plan, $feature);
            if ($val !== null) {
                return $this->boolish($val);
            }

            $alt = Str::endsWith($feature, '_enabled')
                ? Str::beforeLast($feature, '_enabled')
                : "{$feature}_enabled";

            $val = data_get($plan, $alt);
            if ($val !== null) {
                return $this->boolish($val);
            }

            $json = $this->normalizeFeaturesArray($plan->features ?? []);
            if (array_key_exists($feature, $json)) {
                return $this->boolish($json[$feature]);
            }
            if (array_key_exists($alt, $json)) {
                return $this->boolish($json[$alt]);
            }

            if (isset($json[0])) {
                return in_array($feature, $json, true) || in_array($alt, $json, true);
            }

            return false;
        });
    }

    public function quota(string $feature): ?int
    {
        if ($this->bypass()) {
            return null;
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
        return $value === null ? null : (int) $value;
    }

    public function canCreate(string $feature, int $currentUsage): bool
    {
        if (!$this->enabled($feature)) {
            return false;
        }

        $quota = $this->quota($feature);
        if ($quota === null || $quota < 0) {
            return true;
        }

        return $currentUsage < $quota;
    }

    public function enabledList(): array
    {
        if ($this->bypass()) {
            return [];
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
            $fromJson = collect($json)
                ->mapWithKeys(fn ($v, $k) => is_string($k) ? [$k => $this->boolish($v)] : [])
                ->filter()
                ->keys()
                ->all();

            if (isset($json[0])) {
                $fromJson = array_unique(array_merge($fromJson, array_values(array_filter($json, 'is_string'))));
            }
        }

        return array_values(array_unique(array_merge($fromColumns, $fromJson)));
    }

    public function invalidateCache(): void
    {
    }

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
