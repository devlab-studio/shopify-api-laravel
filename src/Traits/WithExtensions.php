<?php

namespace Devlab\ShopifyApiLaravel\Traits;

use Illuminate\Database\Eloquent\Builder;

trait WithExtensions
{
    public function scopeWhereInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->whereIn($field, $value);
        } else {
            $value = explode(",", $value);
            return $query->whereIn($field, $value);
        }
    }
    public function scopeOrWhereInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->orWhereIn($field, $value);
        } else {
            $value = explode(",", $value);
            return $query->orWhereIn($field, $value);
        }
    }
    public function scopeWhereNotInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->whereNotIn($field, $value);
        } else {
            $value = explode(",", $value);
            return $query->whereNotIn($field, $value);
        }
    }
    public function scopeOrWhereNotInto(Builder $query, $field, $value)
    {
        if (is_array($value)) {
            return $query->orWhereNotIn($field, $value);
        } else {
            $value = explode(",", $value);
            return $query->orWhereNotIn($field, $value);
        }
    }

    public static function getModelData($oQuery, $iModel_id, $records_in_page = 0, $aWithDerived = [], $keyBy = 'id', $pageNumber = null)
    {
        if (!empty($aWithDerived)) {
            $oQuery->with($aWithDerived);
        }
        if ($iModel_id == 0) {
            $records_in_page = ($records_in_page == 0) ? config('constants.pagination.DEFAULT_PAGE_RECORDS') : $records_in_page;
            if ($records_in_page > 0) {
                $oRecords = $oQuery->paginate($records_in_page, ['*'], 'page', $pageNumber)->withQueryString();
                $oRecordsC = $keyBy ? $oRecords->getCollection()->keyBy($keyBy) : $oRecords->getCollection();
                $oRecords->setCollection($oRecordsC);
            } else {
                $oRecords = $keyBy ? $oQuery->get()->keyBy($keyBy) : $oQuery->get();
            }
        } else {
            $oRecords = $oQuery->get()->first();
        }
        return $oRecords;
    }
}
