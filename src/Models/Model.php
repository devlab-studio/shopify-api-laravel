<?php

namespace Devlab\ShopifyApiLaravel\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;
use Devlab\ShopifyApiLaravel\Classes\Panel;

class Model extends LaravelModel
{

    /**
	 * Overwrite model save.
	 */
    public function save (array $options = array(), $do_log = true) {
        $user_id = auth()->user()->id ?? config('constants.users.system', 0);
        if ($user_id==0) { // There are no login
            if (empty($this->id)) {
                if (array_key_exists('created_user', $this->attributes) && !empty($this->created_user)) {
                    $user_id = $this->created_user;
                }
            } else {
                if (array_key_exists('updated_user', $this->attributes) && !empty($this->updated_user)) {
                    $user_id = $this->updated_user;
                }
            }
        } else {
            if (empty($this->id)) {
                if (array_key_exists('created_user', $this->attributes)) {
                    $this->created_user = $user_id;
                }
            } else {
                if (array_key_exists('updated_user', $this->attributes)) {
                    $this->updated_user = $user_id;
                }
            }
        }
        // if ($do_log && $this->isDirty()) {
        //     Panel::saveLog($user_id, get_class($this).'::save', [
        //         'original' => $this->getOriginal(),
        //         'changes' => $this->getDirty(),
        //     ]);
        // }
        parent::save($options); // Calls Default Save
    }
    /**
	 * Overwrite model delete.
	 */
    public function delete ($do_log = true)
    {
        // if ($do_log) {
        //     Panel::saveLog(auth()->user()->id??0, get_class($this).'::delete', [
        //         'id' => $this->id
        //     ]);
        // }

        if (array_key_exists('updated_user', $this->attributes) && empty($this->updated_user)) {
            $this->updated_user = auth()->user()->id ?? $this->updated_user;
        }

        parent::delete(); // Calls Default Save
    }
}
