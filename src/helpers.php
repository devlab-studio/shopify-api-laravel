<?php

if (! function_exists('is_code')) {
    function is_code($message)
    {
        // Si quieres detectar un patrón específico para considerarlo código:
        $is_code = preg_match('/[{};$]/', $message);

        if ($is_code) {
            return true;
        } else {
            return false;
        }
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
