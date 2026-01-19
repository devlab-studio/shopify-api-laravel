<?php

namespace Devlab\ShopifyApiLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use stdClass;

class Autocomplete extends Model
{

    /**
     * Get autocomplete.
     *
     * @param string $vcModel
     * @param string $vcSearch
     * @param int $iMaxRecords
     * @param $InitParameters
     * @param string $vcParameter1
     * @param string $vcParameter2
     * @param string $vcParameter3
     * @param string $vcParameter4
     * @return mixed Colletion
     *
     */
    public static function dlGet(
        string $ac_query,
        ?string $search = '',
        int $maxRecords = 0,
        $initOptions = null,
        ?string $parameter1 = '',
        ?string $parameter2 = '',
        ?string $parameter3 = '',
        ?string $parameter4 = ''
    ) {

        $oRecords = null;
        $oInitialRecords = null;
        $maxRecords = ($maxRecords == 0) ? config('constants.pagination.DEFAULT_PAGE_RECORDS') : $maxRecords;
        if (!empty($initOptions)) {
            if (is_array($initOptions)) {
                $initOptions = $initOptions;
            } else {
                $initOptions = explode(',', $initOptions);
                if (empty($initOptions)) {
                    $initOptions = [];
                }
            }
        } else {
            $initOptions = [];
        }

        switch ($ac_query) {
            case "users":
                $query = User::select('users.id as value', 'users.name as text')
                    ->selectRaw('null as `data`');

                $oRecords = $query->clone();
                $oRecords = $oRecords->when(!empty($search), function ($query) use ($search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('users.name', 'like', '%' . $search . '%');
                    });
                })
                ->when(!empty($parameter1), function ($query) use ($parameter1) {
                    $model_ids = [];
                    if (is_array($parameter1)) {
                        $model_ids = $parameter1;
                    } else {
                        $model_ids = explode(',', $parameter1);
                    }
                    $query->whereIn('users.id', $model_ids);
                })
                ->whereNotIn('users.id', $initOptions)
                ->orderBy('users.name', 'asc')
                ->paginate($maxRecords);

                if (!empty($initOptions)) {
                    $oInitialRecords = $query
                        ->whereIn('users.id', $initOptions)
                        ->orderBy('users.name', 'asc')
                        ->paginate($maxRecords);
                }
                break;
            default:
                $options = config('autocomplete.' . $ac_query, []);
                $oRecords = [
                    'total' => length($options),
                    'count' => length($options),
                    'records' => $options,
                    'initial_records' => []
                ];
                break;
        }

        if (is_array($oRecords)) {
            $result = $oRecords;
        } else {
            $result = [
                'total' => $oRecords->total(),
                'count' => length($oRecords->items()),
                'records' => $oRecords->keyBy('value')->toArray(),
                'initial_records' => ($oInitialRecords) ? $oInitialRecords->keyBy('value')->toArray() : [],
            ];
        }

        return $result;
    }

    private static function json_data(&$element, $key)
    {
        $element['data'] = json_decode($element['data'], true);
    }
}
