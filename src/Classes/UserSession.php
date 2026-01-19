<?php

namespace Devlab\ShopifyApiLaravel\Classes;

use App\Models\CompaniesGroup;
use App\Models\Company;
use Devlab\ShopifyApiLaravel\Models\User;

class UserSession {

    public static function session_update($update_login = false)
    {
        $bd_user = User::dlGet(auth()->user()->id);

        if ($update_login) {
            $bd_user->last_login = now();
            $bd_user->save();
        }
    }
}
