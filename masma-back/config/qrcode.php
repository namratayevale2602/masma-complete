<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Backend
    |--------------------------------------------------------------------------
    |
    | Simple QrCode allows you to create QR codes with multiple different
    | backends, such as "imagick", "svg", or "eps". The default is "png".
    |
    */
    'format' => 'png',

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | This option specifies which image driver to use. By default, Imagick
    | is used, but you can also use "gd" if Imagick is not available.
    |
    */
    'imageDriver' => 'gd', // Change to 'gd' from 'imagick'

    /*
    |--------------------------------------------------------------------------
    | Size
    |--------------------------------------------------------------------------
    |
    | This is the height and width of the image generated, in pixels.
    |
    */
    'size' => 300,

    /*
    |--------------------------------------------------------------------------
    | Margin
    |--------------------------------------------------------------------------
    |
    | This is the margin around the QR code, in pixels.
    |
    */
    'margin' => 2,

    /*
    |--------------------------------------------------------------------------
    | Error Correction
    |--------------------------------------------------------------------------
    |
    | This specifies the level of error correction to use. 
    | Available options are: "L", "M", "Q", "H"
    |
    */
    'errorCorrection' => 'H',

    /*
    |--------------------------------------------------------------------------
    | Colors
    |--------------------------------------------------------------------------
    |
    | These are the foreground and background colors of the QR code.
    |
    */
    'color' => [0, 0, 0], // Black
    'backgroundColor' => [255, 255, 255], // White

    /*
    |--------------------------------------------------------------------------
    | Encoding
    |--------------------------------------------------------------------------
    |
    | This is the character encoding used to generate the QR code.
    |
    */
    'encoding' => 'UTF-8',

    /*
    |--------------------------------------------------------------------------
    | Style
    |--------------------------------------------------------------------------
    |
    | This is the style of the QR code blocks.
    |
    */
    'style' => 'square',

    /*
    |--------------------------------------------------------------------------
    | Eye Style
    |--------------------------------------------------------------------------
    |
    | This is the style of the QR code eyes.
    |
    */
    'eye' => 'square',
];