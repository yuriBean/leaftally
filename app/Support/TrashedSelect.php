<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TrashedSelect
{
    /**
     * Build a base query scoped by creator and optional where filters.
     * - Supports scalar equality: ['col' => 'val']
     * - Supports whereIn when value is an array: ['col' => ['a','b']]
     * - Automatically applies ProductService material_type filter (finished,both) unless caller already passed a material_type condition.
     */
    protected static function baseQuery(string $modelClass, int $createdBy, array $where = []): Builder
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $modelClass;

        $q = $modelClass::query()->where('created_by', $createdBy);

        // If this is ProductService, auto-limit to finished/both unless caller specifies material_type
        $isProductService = Str::lower(ltrim($modelClass, '\\')) === 'app\\models\\productservice';
        $callerSetMaterial = array_key_exists('material_type', $where);

        if ($isProductService && !$callerSetMaterial && self::hasColumn($model, 'material_type')) {
            $q->whereIn('material_type', ['finished', 'both']);
        }

        // Apply provided where filters
        foreach ($where as $col => $val) {
            if (!self::hasColumn($model, $col)) {
                continue; // skip unknown columns to be safe
            }
            if (is_array($val)) {
                $q->whereIn($col, $val);
            } else {
                $q->where($col, $val);
            }
        }

        return $q;
    }

    public static function activeOptions(string $modelClass, int $createdBy, string $label = 'name', array $where = []): Collection
    {
        $q = self::baseQuery($modelClass, $createdBy, $where);

        // Ensure we're not accidentally including trashed
        if (self::usesSoftDeletes($modelClass)) {
            $model = new $modelClass;
            $q->whereNull($model->getQualifiedDeletedAtColumn());
        }

        return $q->orderBy($label)->pluck($label, 'id');
    }

    public static function optionsWithUsed(
        string $modelClass,
        int $createdBy,
        array $usedIds,
        string $label = 'name',
        array $where = []
    ): Collection {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $modelClass;

        // 1) FIRST: apply product selection (filters) to get ACTIVE options
        $active = self::activeOptions($modelClass, $createdBy, $label, $where);

        // 2) THEN: add TRASHED that are "used" (even if they wouldn't pass the filter),
        //    so existing relationships keep showing
        $trashed = collect();

        if (self::usesSoftDeletes($modelClass) && !empty($usedIds)) {
            $trashed = $modelClass::onlyTrashed()
                ->where('created_by', $createdBy)
                ->whereIn($model->getKeyName(), array_filter($usedIds))
                ->get([$model->getKeyName(), $label])
                ->mapWithKeys(function ($m) use ($label) {
                    $name = (string) data_get($m, $label, '');
                    $display = trim($name) !== '' ? $name . ' (' . __('deleted') . ')' : __('(deleted)');
                    return [$m->getKey() => $display];
                });
        }

        // Union: trashed first (so "deleted" label versions don't get overwritten), then active
        // NOTE: Collection::union keeps first value for duplicate keys.
        return $trashed->union($active);
    }

    public static function findWithTrashed(string $modelClass, $id): ?Model
    {
        return $modelClass::withTrashed()->find($id);
    }

    /** ---------- Helpers ---------- */

    protected static function usesSoftDeletes(string $modelClass): bool
    {
        $model = new $modelClass;
        return method_exists($model, 'getDeletedAtColumn') && self::hasColumn($model, $model->getDeletedAtColumn());
    }

    protected static function hasColumn(Model $model, string $column): bool
    {
        try {
            return \Schema::hasColumn($model->getTable(), $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
