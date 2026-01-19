<?php
namespace Devlab\ShopifyApiLaravel\Models;


use Carbon\Carbon;
use Devlab\ShopifyApiLaravel\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use WithExtensions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get users applying filter.
     *
     * @param int $iUsers_id
     * @param int $iRecordsInPage
     * @param array $aSort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function dlGet(
        ?int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = [],
        array $with = []
    ) {

        $model_id = ($model_id) ? $model_id : 0;

        $oQuery = static::select('users.*');

        $oQuery->when($model_id > 0, function ($query)  use ($model_id) {
            return $query->where('users.id', $model_id);
        });

        $oQuery = static::dlApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        return static::getModelData($oQuery, $model_id, $records_in_page, $with);
    }
    /**
     * Get users summary.
     *
     * @param array $filters
     * @return mixed Colletion
     *
     */
    public static function dlGetSummary(
        ?array $filters = []
    ) {
        $oQuery = static::selectRaw('
            count(users.id) as users,
            sum(users.active) as users_active
        ');
        $oQuery = static::dlApplyFilters($oQuery, $filters);

        return $oQuery->get()->first();
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
        $query->when(isset($filters['users_ids']) && !empty($filters['users_ids']), function ($query) use ($filters) {
            return $query->whereIn('users.id', $filters['users_ids']);
        })
            ->when(isset($filters['name']) && !empty($filters['name']), function ($query) use ($filters) {
                return $query->where('users.name', 'like', '%' . $filters['email'] . '%');
            })
            ->when(isset($filters['email']) && !empty($filters['email']), function ($query) use ($filters) {
                return $query->where('users.email', 'like', '%' . $filters['email'] . '%');
            })
            ->when(isset($filters['active']) && !empty($filters['active']), function ($query) use ($filters) {
                return $query->whereIn('users.active', $filters['active']);
            })
            ->when(isset($filters['date']) && !empty($filters['date']), function ($query) use ($filters) {
                return $query->whereBetween('users.' . $filters['date'][0], [$filters['date'][1], $filters['date'][2]]);
            })
            ->when(isset($filters['search']) && !empty($filters['search']), function ($query) use ($filters) {
                return $query->where(function ($query) use ($filters) {
                    $query->where('users.name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('users.email', 'like', '%' . $filters['search'] . '%');
                });
            })
            ;
        return $query;
    }
    /**
     * Get user filters.
     *
     * @return mixed filters array()
     *
     */
    public static function dlGetFilters($key = '')
    {
        $user = User::find(auth()->user()->id);
        $ufilters = $user->filters;
        $templates = config('filters');
        $flist = [
            'FilterOn' => 0,
            //'ModalName' => 'modal_Filter',
            'values' => [],
        ];
        $filters = [];
        $filters_defaults = [];

        if (!empty($key)) { // Si la clave del filtro no está vacía
            if (!isset($ufilters[$key]) || empty($ufilters[$key])) { // Si el usuario no tiene filtros para esa clave
                if (isset($templates[$key][0])) { // Si existe plantilla para esa clave
                    $ufilters[$key] = $templates[$key][0];
                    $user->filters = $ufilters;
                    $user->save();
                } else {
                    return array(); // No hay plantilla para esa clave
                }
            }

            $filters = $ufilters;
            foreach ($templates[$key][1] as $key1 => $value) {
                $values = [];
                $label = '';
                if ($ufilters[$key][$key1]) {
                    switch ($value[0]) {
                        case 'text':
                            $values = [$ufilters[$key][$key1]];
                            $label = $value[2];
                            break;
                        case 'array':
                            $aValuesF = (is_array($ufilters[$key][$key1])) ? $ufilters[$key][$key1] : explode(',', $ufilters[$key][$key1]);
                            if (is_array($value[1])) {
                                $values = array_intersect_key($value[1], array_combine(array_values($aValuesF), array_values($aValuesF)));
                            } else {
                                $aTable = explode(":", $value[1]);
                                $model = $aTable[0];
                                $attrib = $aTable[1];
                                if ($model == 'Autocomplete') {
                                    $tmp_values = Autocomplete::dlGet($attrib, '', 0, $ufilters[$key][$key1]);
                                    $values = [];
                                    foreach ($tmp_values['records'] as $key2 => $value2) {
                                        $values[$value2['value']] = $value2['text'];
                                    }
                                    $values = array_intersect_key($values, array_combine(array_values($aValuesF), array_values($aValuesF)));
                                } else {
                                    $model = "App\\Models\\" . $model;
                                    $tmp_values = $model::selectRaw('id,`' . $attrib . '` `value`')->get()->toArray();
                                    $values = [];
                                    foreach ($tmp_values as $key2 => $value2) {
                                        $values[$value2['id']] = $value2['value'];
                                    }
                                    $values = array_intersect_key($values, array_combine(array_values($aValuesF), array_values($aValuesF)));
                                }
                            }
                            $label = $value[2];
                            break;
                        case 'date':
                            $date_from = new Carbon($ufilters[$key][$key1][1]);
                            $date_to = new Carbon($ufilters[$key][$key1][2]);
                            $label = config('constants.date_type.' . $ufilters[$key][$key1][0]);
                            $values = [$label . ' de ' . $date_from->format('d-m-Y') . ' a ' . $date_to->format('d-m-Y')];
                            break;
                    }
                    if (!empty($values)) {
                        $flist['FilterOn'] = 1;
                    }
                    $flist['values'][$key1] = array(
                        'values' => $values,
                        'label' => $label
                    );
                }
            }
            // Valor por defecto para los filtros
            if (isset($templates[$key][2])) {
                foreach ($templates[$key][2] as $key1 => $value) {
                    if (isset($templates[$key][1][$key1])) {
                        if ($templates[$key][1][$key1][0] == 'date') {
                            $filters_defaults[$key][$key1]['values'] = $value;

                            $date_from = new Carbon($value[1]);
                            $date_to = new Carbon($value[2]);
                            $label = config('constants.date_type.' . $value[0]);
                            $filters_defaults[$key][$key1]['label'] = [$label . ' de ' . $date_from->format('d-m-Y') . ' a ' . $date_to->format('d-m-Y')];
                        }
                    }
                }
            }

            $result = [
                'flist' => $flist,
                'ufilters' => $ufilters[$key],
                'filters' => $filters,
                'filters_defaults' => $filters_defaults,
            ];
            return $result;
        } else {
            return []; // Se ha pasado una clave de filtro vacía
        }
    }
    /**
     * Save user filters.
     *
     * @return mixed aResult(iResult, vcMessage)
     *
     */
    public static function dlSaveFilters($key, $filters)
    {
        $user = User::find(auth()->user()->id);
        $ufilters = $user->filters;
        $templates = config('filters');
        $clean = array();

        if (!empty($filters) && !empty($key)) {
            foreach ($filters as $key1 => $value) {
                if ($key1 != '_token' && $key1 != 'page' && $key1 != 'signature') {
                    //$key = substr($key,strpos($key,'_')+1);
                    $clean[$key1] = $value;
                }
            }
            $diff = array_diff_key($templates[$key][0], $clean);
            $merge = array_merge($clean, $diff);
            $ufilters[$key] = $merge;
            $user->filters = $ufilters;
            $user->save();
            return ['iResult' => $user->id];
        } else {
            return ['iResult' => -1, 'vcMessage' => 'Clave o valores de filtro vacíos'];
        }
    }
    /**
     * Reset user filters.
     *
     * @param int $iUsers_id
     * @param array $aAttributes array (attribute => value)
     * @return mixed aResult(iResult, vcMessage)
     *
     */
    public static function dlResetFilters($ids = [], $prefix = '')
    {
        $aResult = ['iResult' => 0, 'vcMessage' => ''];
        if (empty($prefix)) {
            User::where('id', '>', 0)
                ->when(!empty($ids), function ($query) use ($ids) {
                    return $query->whereIn('id', $ids);
                })
                ->update(['filters' => null]);
        } else {
            if (empty($ids)) {
                $ids = User::all()->keyBy('id')->keys()->all();
            }
            foreach ($ids as $id) {
                $user = User::find($id);
                $filters = $user->filters;
                if (!empty($filters)) {
                    $filters = array_filter($filters, function ($key) use ($prefix) {
                        return !str_starts_with($key, $prefix);
                    }, ARRAY_FILTER_USE_KEY);
                    User::where('id', $id)->update(['filters' => $filters]);
                }
            }
        }
        return $aResult;
    }
}
