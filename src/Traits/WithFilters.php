<?php

namespace Devlab\ShopifyApiLaravel\Traits;

use Devlab\ShopifyApiLaravel\Models\User;
use Devlab\ShopifyApiLaravel\Traits\WithSorting;
use Carbon\Carbon;

trait WithFilters
{
	use WithSorting;

	public $filter;
	public $filter_component; // Son filtros que no se graban en base de datos, que sólo actuan en el componente activo.
	public $filter_labels;
	public $filter_defaults;
	public $filter_name = '';


	public function searchRecords()
	{
		$this->validate();
		if (isset($this->filter['date']) && length($this->filter['date']) == 3) {
			$this->filter['date'][1] = Carbon::createFromFormat('d-m-Y', $this->filter['date'][1])->format('Y-m-d');
			$this->filter['date'][2] = Carbon::createFromFormat('d-m-Y', $this->filter['date'][2])->format('Y-m-d');
		}
		User::dlSaveFilters($this->filter_name, $this->filter);
		$this->emit('searchRecords');
		$this->closeModal();
	}

	public function deleteLabelFilter($field)
	{
		$this->filter[$field] = config('filters.' . $this->filter_name . '.0.' . $field);
		User::dlSaveFilters($this->filter_name, $this->filter);
		$this->emit('searchRecords');
	}

	public function deleteFilter()
	{
		$this->filter = config('filters.' . $this->filter_name . '.0');
		User::dlSaveFilters($this->filter_name, $this->filter);
		$this->emit('searchRecords');
		$this->closeModal();
	}

	public function getMergeFilters()
	{
		return array_merge($this->filter, $this->filter_component ?? []);
	}

	public function getFilters()
	{
		$errors = $this->getErrorBag();
		if (length($errors) == 0) {

			$filter = User::dlGetFilters($this->filter_name);
			$this->filter = $filter['ufilters'];
			$this->filter_labels = $filter['flist'];
			$this->sortBy = $this->filter['sort'];
			$this->sortDirection = $this->filter['order'];
			if (!empty($this->filter['date'])) {
				$this->filter['date'][1] = (new Carbon($this->filter['date'][1]))->format('d-m-Y');
				$this->filter['date'][2] = (new Carbon($this->filter['date'][2]))->format('d-m-Y');
			}
			if (!empty($filter['filters_defaults'][$this->filter_name])) {
				$this->filters_defaults = $filter['filters_defaults'][$this->filter_name];
			} else {
				$this->filters_defaults = [];
			}
		}
	}
}
