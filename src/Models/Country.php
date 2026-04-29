<?php

namespace  Devlab\ShopifyApiLaravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasFactory;
    use HasTranslations;

    public $timestamps = false;

    public $translatable = ['name'];

}
