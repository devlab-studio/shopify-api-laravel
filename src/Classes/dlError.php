<?php
namespace Devlab\ShopifyApiLaravel\Classes;

use InvalidArgumentException;

class dlError {

    /**
     * Create a sign for the value.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function insert($title, $code) {
        session()->flash('error', ['title' => $title, 'code' => $code]);
    }

}
