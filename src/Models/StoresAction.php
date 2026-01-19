<?php

namespace Devlab\ShopifyApiLaravel\Models;

use Devlab\ShopifyApiLaravel\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoresAction extends Model
{
    use HasFactory;
    use WithExtensions;

    public static function dlGet(
        ?int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = [],
        array $with = []
    ) {

        $query = static::select('stores_actions.*')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('stores_actions.id', $model_id);
        })
        ;

        $query = static::dlApplyFilters($query, $filters);

        foreach ($sort as $key => $value) {
            $query->orderBy($key, $value);
        }

        return static::getModelData($query, $model_id, $records_in_page, $with);
    }

    /**
     * Apply filters.
     *
     * @param $query
     * @param array $filters
     * @return mixed Query
     *
     */
    public static function dlApplyFilters(
        $query,
        ?array $filters = []
    ) {
        $query->when(isset($filters['stores_actions_ids']) && !empty($filters['stores_actions_ids']), function ($query) use ($filters) {
            return $query->whereIn('stores_actions.id', $filters['stores_actions_ids']);
        })
            ->when(isset($filters['store_id']) && !empty($filters['store_id']), function ($query) use ($filters) {
                return $query->where('stores_actions.store_id', $filters['store_id']);
            })
            ->when(isset($filters['event']) && !empty($filters['event']), function ($query) use ($filters) {
                return $query->where('stores_actions.event', $filters['event']);
            })
            ;
        return $query;
    }

}
