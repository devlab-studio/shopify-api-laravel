<?php

namespace Devlab\ShopifyApiLaravel\Models;

use Devlab\ShopifyApiLaravel\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class OrdersLabel extends Model
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

        $query = static::select('orders_labels.*')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('orders_labels.id', $model_id);
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
        $query->when(isset($filters['orders_labels_ids']) && !empty($filters['orders_labels_ids']), function ($query) use ($filters) {
            return $query->whereIn('orders_labels.id', $filters['orders_labels_ids']);
        })
            ->when(isset($filters['store_id']) && !empty($filters['store_id']), function ($query) use ($filters) {
                return $query->where('orders_labels.store_id', $filters['store_id']);
            })
            ->when(isset($filters['shopify_order_id']) && !empty($filters['shopify_order_id']), function ($query) use ($filters) {
                return $query->where('orders_labels.shopify_order_id', $filters['shopify_order_id']);
            })
            ;
        return $query;
    }

    public function delete($do_log = true)
    {
        Storage::delete($this->path);
        parent::delete();
    }

}
