<?php

use App\Libs\Statics\Url;
use App\Libs\Statics\View;

function view($path, $args = []) {
    return View::show($path, $args);
}

function twig($path, $args = []) {
    return View::twig($path, $args);
}

function route($path, $args = []) {
    return Url::route($path, $args);
}

function redirect($location, $with = [], $after = 0) {
    App\Libs\Statics\Response::redirectTo($location, $with, $after);
}

function goBack($with = [], $after = 0) {
    App\Libs\Statics\Response::redirectBack($with, $after);
}

function refresh($after = 0) {
    App\Libs\Statics\Response::refresh($after);
}

function escape($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

/**
 * explode the the given string according to the specified array of delimiters
 * @param array $delimiters 
 * @param string $string 
 * @return array
 */
function multiexplode(array $delimiters, $string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return $launch;
}

/**
  Check whether the input is an array whose keys are all integers.
  @param[in] $InputArray          (array) Input array.
  @return                         (bool) \b true iff the input is an array whose keys are all integers.
 */
function isKeyIntArray($InputArray) {
    if (!is_array($InputArray)) {
        return false;
    }

    if (count($InputArray) <= 0) {
        return true;
    }

    return array_unique(array_map("is_int", array_keys($InputArray))) === array(true);
}

/**
  Check whether the input is an array whose keys are all strings.
  @param[in] $InputArray          (array) Input array.
  @return                         (bool) \b true iff the input is an array whose keys are all strings.
 */
function isKeyStringArray($InputArray) {
    if (!is_array($InputArray)) {
        return false;
    }

    if (count($InputArray) <= 0) {
        return true;
    }

    return array_unique(array_map("is_string", array_keys($InputArray))) === array(true);
}

/**
  Check whether the input is an array with at least one key being an integer and at least one key being a string.
  @param $InputArray          (array) Input array.
  @return                         (bool) \b true iff the input is an array with at least one key being an integer and at least one key being a string.
 */
function isKeyMixedArray($InputArray) {
    if (!is_array($InputArray)) {
        return false;
    }

    if (count($InputArray) <= 0) {
        return true;
    }

    return count(array_unique(array_map("is_string", array_keys($InputArray)))) >= 2;
}

/**
  Check whether the input is an array whose keys are numeric, sequential, and zero-based.
  @param[in] $InputArray          (array) Input array.
  @return                         (bool) \b true iff the input is an array whose keys are numeric, sequential, and zero-based.
 */
function isKeyNumZeroBasedArray($InputArray) {
    if (!is_array($InputArray)) {
        return false;
    }

    if (count($InputArray) <= 0) {
        return true;
    }

    return array_keys($InputArray) === range(0, count($InputArray) - 1);
}

function getClassBaseName($cls) {
    if (is_object($cls)) {
        $cls = get_class($cls);
    }
    $cls = explode('\\', $cls);
    return $cls[count($cls) - 1];
}

function loop(array $arr, Closure $callable) {
    foreach ($arr as $key => $value) {
        $arr[$key] = call_user_func_array($callable, [$key, $value]);
    }
    return $arr;
}

function arr2obg(array $arr) {
    return json_decode(json_encode($arr));
}

function obj2arr(object $obj) {
    return json_decode(json_encode($obj), true);
    ;
}

function scanImageToPng($source, $target = 'php://output') {
    $sourceImg = @imagecreatefromstring(@file_get_contents($source));
    if ($sourceImg === false) {
        return FALSE;
    }
    $width = imagesx($sourceImg);
    $height = imagesy($sourceImg);
    $targetImg = imagecreatetruecolor($width, $height);
    imagecopy($targetImg, $sourceImg, 0, 0, 0, 0, $width, $height);
    imagedestroy($sourceImg);
    imagepng($targetImg, $target);
    imagedestroy($targetImg);
    return TRUE;
}

function slugify($text, $translate = true) {
    $replace = [
        '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
        '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae',
        '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
        'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
        'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
        'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
        'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
        'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
        'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
        'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
        'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
        'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
        'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
        'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
        'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
        '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
        'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
        'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
        'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
        'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
        'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
        'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
        'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
        'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
        'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
        'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
        'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
        'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
        '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
        'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
        'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
        'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
        'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
        'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
        'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
        'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
        'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
        'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
        'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
        'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
        'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
        'ю' => 'yu', 'я' => 'ya'
    ];
    // make a human readable string
    $text = strtr($text, $replace);

    $text = preg_replace('/[^A-Za-z0-9-\pL]+/u', '-', $text);

    if ($translate) {
        $text = ar2en($text);
    }

    $text = trim($text, ' -');

    $text = preg_replace_callback('/([A-Za-z0-9]+)/', function ($match) {
        return strtolower($match[0]);
    }, $text);

    return $text;
}

function en2ar($text) {
    $obj = new I18N_Arabic('Transliteration');
    return $obj->en2ar($text);
}

function ar2en($text) {
    $obj = new I18N_Arabic('Transliteration');
    return $obj->ar2en($text);
}

/**
 * dump the variables and kill the rest of page
 * @param  mixed $args string to be displayed after killing the page
 */
if (!function_exists('dd')) {

    function dd() {
        $args = func_get_args();
        call_user_func_array('dump', $args);
        die();
    }

}

    
    