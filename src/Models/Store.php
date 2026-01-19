<?php

namespace Devlab\ShopifyApiLaravel\Models;
use Devlab\ShopifyApiLaravel\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'store_urls' => 'array',
        'store_data' => 'array',
        'api_credentials' => 'array',
        'sso_data' => 'array',
    ];

    public static function dlGet(
        ?int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = [],
        array $with = []
    ) {

        $query = static::select('stores.*')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('stores.id', $model_id);
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
        $query->when(isset($filters['expires_in']), function ($query) {
            return $query->whereNotNull('api_credentials->expires_in');
        });
        return $query;
    }
}
