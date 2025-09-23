<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TrashedSelect
{
    protected static function baseQuery(string $modelClass, int $createdBy, array $where = []): Builder
    {
        $model = new $modelClass;

        $q = $modelClass::query()->where('created_by', $createdBy);

        $isProductService = Str::lower(ltrim($modelClass, '\\')) === 'app\\models\\productservice';
        $callerSetMaterial = array_key_exists('material_type', $where);

        if ($isProductService && !$callerSetMaterial && self::hasColumn($model, 'material_type')) {
            $q->whereIn('material_type', ['finished', 'both']);
        }

        foreach ($where as $col => $val) {
            if (!self::hasColumn($model, $col)) {
                continue;
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
        $model = new $modelClass;

        $active = self::activeOptions($modelClass, $createdBy, $label, $where);

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

        return $trashed->union($active);
    }

    public static function findWithTrashed(string $modelClass, $id): ?Model
    {
        return $modelClass::withTrashed()->find($id);
    }

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
