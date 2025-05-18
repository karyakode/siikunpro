<?php

if (extension_loaded('mbstring')) {
    mb_internal_encoding('UTF-8');

    $functionMap = [
        'utf8_strlen'    => 'mb_strlen',
        'utf8_strpos'    => 'mb_strpos',
        'utf8_strrpos'   => 'mb_strrpos',
        'utf8_substr'    => 'mb_substr',
        'utf8_strtoupper'=> 'mb_strtoupper',
        'utf8_strtolower'=> 'mb_strtolower'
    ];

} elseif (extension_loaded('iconv')) {

    $functionMap = [
        'utf8_strlen'    => function($string) { return iconv_strlen($string, 'UTF-8'); },
        'utf8_strpos'    => function($string, $needle, $offset = 0) { return iconv_strpos($string, $needle, $offset, 'UTF-8'); },
        'utf8_strrpos'   => function($string, $needle, $offset = 0) { return iconv_strrpos($string, $needle, 'UTF-8'); },
        'utf8_substr'    => function($string, $offset, $length = null) {
            return $length === null ? iconv_substr($string, $offset, iconv_strlen($string, 'UTF-8'), 'UTF-8') : iconv_substr($string, $offset, $length, 'UTF-8');
        },
        'utf8_strtoupper'=> function($string) {
            return iconv('UTF-8', 'UTF-8//IGNORE', strtoupper(iconv('UTF-8', 'ISO-8859-1//IGNORE', $string)));
        },
        'utf8_strtolower'=> function($string) {
            return iconv('UTF-8', 'UTF-8//IGNORE', strtolower(iconv('UTF-8', 'ISO-8859-1//IGNORE', $string)));
        }
    ];

} else {

    $functionMap = [
        'utf8_strlen'    => 'strlen',
        'utf8_strpos'    => 'strpos',
        'utf8_strrpos'   => 'strrpos',
        'utf8_substr'    => 'substr',
        'utf8_strtoupper'=> 'strtoupper',
        'utf8_strtolower'=> 'strtolower'
    ];
}

// Mengaktifkan setiap fungsi dalam $functionMap ke dalam ruang lingkup global
foreach ($functionMap as $name => $implementation) {
    if (!function_exists($name)) {
        if (is_callable($implementation)) {
            // Jika implementasi berupa closure (untuk iconv)
            eval("function $name(...\$args) { return call_user_func_array(\$implementation, \$args); }");
        } else {
            // Untuk fungsi yang dipetakan langsung (mbstring atau fungsi bawaan PHP)
            eval("function $name(...\$args) { return $implementation(...\$args); }");
        }
    }
}
