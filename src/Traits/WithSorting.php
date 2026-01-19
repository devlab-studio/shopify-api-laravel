<?php

namespace Devlab\ShopifyApiLaravel\Traits;

use Devlab\ShopifyApiLaravel\Models\User;


trait WithSorting
{
    public $sortBy = '';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        $this->selected = [];
        $this->sortDirection = $this->sortBy === $field
            ? $this->reverseSort()
            : 'asc';

        $this->sortBy = $field;
        if (isset($this->filter_name) && !empty($this->filter_name)) {
            $this->filter['sort'] = $this->sortBy;
            $this->filter['order'] = $this->sortDirection;
            User::dlSaveFilters($this->filter_name, $this->filter);
        }
    }

    public function reverseSort()
    {
        return $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
    }
}
