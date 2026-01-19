<?php

namespace Devlab\ShopifyApiLaravel\Models;

use Devlab\ShopifyApiLaravel\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiddlewareSession extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'shopify_data' => 'array',
        'sso_data' => 'array',
    ];

    public static function dlGet(
        ?int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = [],
        array $with = []
    ) {

        $query = static::select('middleware_sessions.*')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('middleware_sessions.id', $model_id);
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
        $query->when(isset($filters['middleware_sessions_ids']) && !empty($filters['middleware_sessions_ids']), function ($query) use ($filters) {
            return $query->whereIn('middleware_sessions.id', $filters['middleware_sessions_ids']);
        })
        ->when(isset($filters['session_id']) && !empty($filters['session_id']), function ($query) use ($filters) {
            return $query->where('middleware_sessions.session_id', $filters['session_id']);
        })
        ;
        return $query;
    }

}
