<?php

if (!function_exists('length')) {
	function length($element)
	{
		$count = 0;
		if (is_array($element) || is_object(($element))) {
			$count = count($element);
		} elseif (is_string($element)) {
			$count = strlen($element);
		} else {
			$count = 0;
		}
		return ($count);
	}
}

