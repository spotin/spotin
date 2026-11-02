<?php

return [

    /*
    |--------------------------------------------------------------------------
    | QR Codes size
    |--------------------------------------------------------------------------
    |
    | The size of the QR codes, in pixels.
    |
    */

    'size' => 1024,

    /*
    |--------------------------------------------------------------------------
    | QR Codes margin
    |--------------------------------------------------------------------------
    |
    | The quiet zone around the QR codes, in modules: 4 is the standard.
    |
    */

    'margin' => 4,

    /*
    |--------------------------------------------------------------------------
    | QR Codes logo
    |--------------------------------------------------------------------------
    |
    | The logo in the middle of the QR codes: the SVG, and the same as PNG for
    | GD. After changing the SVG, export the PNG again with:
    |
    | rsvg-convert --width 1312 --height 1312 public/images/logo.svg -o public/images/logo.png
    |
    */

    'logo_svg' => public_path('images/logo.svg'),
    'logo_png' => public_path('images/logo.png'),

    /*
    |--------------------------------------------------------------------------
    | QR Codes logo width
    |--------------------------------------------------------------------------
    |
    | The width of the logo in the middle of the QR codes.
    |
    */

    'logo_width' => 220,

    /*
    |--------------------------------------------------------------------------
    | QR Codes logo padding
    |--------------------------------------------------------------------------
    |
    | The smallest white margin around the logo in the middle of the QR codes.
    |
    */

    'logo_padding' => 16,

];
