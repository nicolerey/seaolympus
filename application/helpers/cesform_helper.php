<?php

if (!function_exists('preset')) {

    function preset($arr, $key, $default = FALSE) {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }

}


if (!function_exists('leave_type')) {

    function leave_type($type) {
        switch ($type) {
        	case 'o': return 'Others';
        	case 'sl': return 'Sick Leave';
        	case 'matpat': return 'Maternal / Paternal Leave';
        	case 'vl': return 'Vacation Leave';
			case 'wml': return 'Menstruation Leave';
        }
    }

}
