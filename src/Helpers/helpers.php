<?php

use App\Classes\dlSign;
use Devlab\ShopifyApiLaravel\Classes\dlURL;

if (!function_exists('config_dl')) {
	/**
	 * Get / set the specified configuration value.
	 *
	 * If an array is passed as the key, we will assume you want to set an array of values.
	 *
	 * @param  array|string|null  $key
	 * @param  mixed  $default
	 * @return mixed|\Illuminate\Config\Repository
	 */
	function config_dl($key = null, $default = null)
	{
		if (is_null($key)) {
			return app('config-dl');
		}

		if (is_array($key)) {
			return app('config-dl')->set($key);
		}

		return app('config-dl')->get($key, $default);
	}
}

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



if (!function_exists('dl_stringSVClean')) {
	function dl_stringSVClean(string $vcString, string $vcSeparator = ',', string $vcDefault = '')
	{
		$aSearch = [" ", ",", ".", "\n\r", "\r\n", "\n", "\r"];
		$vcString = str_replace($aSearch, $vcSeparator, $vcString);
		$vcString = preg_replace("/\\" . $vcSeparator . "+/", $vcSeparator, $vcString);
		$vcString = trim($vcString, $vcSeparator);
		$vcString = ($vcString == '') ? $vcDefault : $vcString;
		return $vcString;
	}
}

if (!function_exists('getBase64File')) {
	function getBase64File(string $vcFilePath)
	{
		if (!file_exists($vcFilePath)) {
			return null;
		}

		$fSource = fopen($vcFilePath, 'r');
		$content = stream_get_contents($fSource);
		fclose($fSource);

		return base64_encode($content);
	}
}

if (!function_exists('array_recursive_search_key_map')) {
	function array_recursive_search_key_map($needle, $haystack)
	{
		$needle = str_replace('\\', '', $needle);
		foreach ($haystack as $first_level_key => $value) {
			if ($needle === $value) {
				return array($first_level_key);
			} elseif (is_array($value)) {
				$callback = array_recursive_search_key_map($needle, $value);
				if ($callback) {
					//return array_merge(array($first_level_key), $callback);
					return $first_level_key;
				}
			}
		}
		return false;
	}
}

if (!function_exists('dl_file_size')) {
	function dl_file_size($iSize)
	{
		$aSizes = array('bytes', 'KB', 'MB', 'GB', 'TB');
		// Calcular vcSize
		$iEscala = intval(log($iSize, 2));
		$iEscala = ($iEscala < 1) ? 0 : $iEscala;
		$iOrdenB10 = intdiv($iEscala, 10);
		$iOrdenB10 = ($iOrdenB10 < 1) ? 0 : $iOrdenB10;
		return number_format($iSize / pow(1024, $iOrdenB10), 1, ',', '.') . ' ' . $aSizes[$iOrdenB10];
	}
}

if (!function_exists('dl_file_type')) {
	function dl_file_type($mime)
	{
		if (str_contains($mime, 'excel')) {
			return ['EXCEL', 'text-green'];
		}
		if (str_contains($mime, 'spreadsheet')) {
			return ['EXCEL', 'text-green'];
		}
		if (str_contains($mime, 'word')) {
			return ['WORD', 'text-blue'];
		}
		if (str_contains($mime, 'powerpoint')) {
			return ['POWERPOINT', 'text-red'];
		}
		if (str_contains($mime, 'pdf')) {
			return ['PDF', 'text-red'];
		}
		if (str_contains($mime, 'image')) {
			return ['IMAGE', 'text-yellow'];
		}
		return ['OTHER', 'text-black'];
	}
}

if (!function_exists('dl_decimalTime')) {
	function dl_decimalTime(string $vcValue)
	{
		$hms = explode(":", $vcValue);
		return ($hms[0] + (isset($hms[1]) ? ($hms[1] / 60) : 0));
	}
}

if (!function_exists('dl_stringTime')) {
	function dl_stringTime(float $dValue)
	{
		$hours = floor($dValue);
		$decimal = $dValue - $hours;
		$minutes = round($decimal * 60);
		return $hours . ":" . sprintf("%02d", $minutes);
	}
}

if (!function_exists('string2decimal')) {
	function string2decimal($value)
	{
		//Log::debug('string2decimal: '.$value);
		if ($value == null) $value = 0;
		$number = str_replace(',', '.', str_replace('.', '', $value));
		$number = str_replace('%', '', $number);
		$number = str_replace('€', '', $number);
		$number = trim($number);
		return (is_numeric($number) ? $number * 1 : $value);
	}
}

if (!function_exists('decimal2string')) {
	function decimal2string($value, $decimals = 'money2')
	{
		//Log::debug('decimal2string: '.$value);
		if ($value == null) $value = 0;
		if (!is_numeric($value)) {
			return $value;
		}
		$sufix = '';
		$ndecimals = 2;
		if ($decimals == 'money0') {
			$ndecimals = 0;
			$sufix = ' €';
		}
		if ($decimals == 'money2') {
			$ndecimals = 2;
			$sufix = ' €';
		}
		if ($decimals == 'money3') {
			$ndecimals = 3;
			$sufix = ' €';
		}
		if ($decimals == 'number0') {
			$ndecimals = 0;
			$sufix = '';
		}
		if ($decimals == 'number2') {
			$ndecimals = 2;
			$sufix = '';
		}
		if ($decimals == 'percent2') {
			$value = $value * 100;
			$ndecimals = 2;
			$sufix = ' %';
		}
		if ($decimals == 'percent1') {
			$value = $value * 100;
			$ndecimals = 1;
			$sufix = ' %';
		}
		if ($decimals == 'percent0') {
			$value = $value * 100;
			$ndecimals = 0;
			$sufix = ' %';
		}
		return number_format($value, $ndecimals, ',', '.') . $sufix;
	}
}

if (!function_exists('dl_file_type')) {
	function dl_file_type($mime)
	{
		if (str_contains($mime, 'excel')) {
			return ['EXCEL', 'text-green'];
		}
		if (str_contains($mime, 'spreadsheet')) {
			return ['EXCEL', 'text-green'];
		}
		if (str_contains($mime, 'word')) {
			return ['WORD', 'text-blue'];
		}
		if (str_contains($mime, 'powerpoint')) {
			return ['POWERPOINT', 'text-red'];
		}
		if (str_contains($mime, 'pdf')) {
			return ['PDF', 'text-red'];
		}
		if (str_contains($mime, 'image')) {
			return ['IMAGE', 'text-yellow'];
		}
		return ['OTHER', 'text-black'];
	}
}

if (!function_exists('dl_variable_get')) {
	function dl_variable_get($path, $array)
	{
		$path = explode('.', $path); //if needed
		$temp = $array;

		foreach ($path as $key) {
			if (is_array($temp)) {
				$temp = $temp[$key];
			} else {
				$temp = $temp->{$key};
			}
		}
		return $temp;
	}
}

if (!function_exists('dl_var_export')) {
	function dl_var_export($expression, $return = FALSE)
	{
		if (!is_array($expression)) return var_export($expression, $return);
		$export = var_export($expression, TRUE);
		$export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
		$array = preg_split("/\r\n|\n|\r/", $export);
		$array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ']$1', ' => ['], $array);
		$export = join(PHP_EOL, array_filter(["["] + $array));
		if ((bool)$return) return $export;
		else echo $export;
	}
}

if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {
	function mb_ucfirst($string)
	{
		$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
		return $string;
	}
}

if (!function_exists('dl_get_procedure')) {
	function dl_get_procedure($object, $function)
	{
        if (is_string($object)) {
            return $object . '::' . $function;
        }

		return get_class($object) . '::' . $function;
	}
}
