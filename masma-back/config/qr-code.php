<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QR Code Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default QR code driver that will be used to
    | generate QR codes. You may set this to either "imagick" or "gd".
    |
    */

    'driver' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | QR Code Format
    |--------------------------------------------------------------------------
    |
    | This option controls the default format of generated QR codes.
    | Supported formats: "png", "eps", "svg"
    |
    */

    'format' => 'png',

    /*
    |--------------------------------------------------------------------------
    | QR Code Size
    |--------------------------------------------------------------------------
    |
    | This option controls the default size of generated QR codes in pixels.
    |
    */

    'size' => 300,

    /*
    |--------------------------------------------------------------------------
    | QR Code Margin
    |--------------------------------------------------------------------------
    |
    | This option controls the default margin around the QR code.
    |
    */

    'margin' => 2,

    /*
    |--------------------------------------------------------------------------
    | QR Code Error Correction
    |--------------------------------------------------------------------------
    |
    | This option controls the default error correction level for QR codes.
    | Supported levels: "L", "M", "Q", "H"
    |
    */

    'error_correction' => 'H',
];