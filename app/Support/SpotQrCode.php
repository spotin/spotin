<?php

namespace App\Support;

use App\Models\Spot;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererInterface;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;

/**
 * The QR code of a spot, to print: it leads to the address of its scans, with the logo of the app in its middle. The
 * same layout is drawn as SVG and as PNG.
 *
 * The logo hides the middle of the code, on a white area made of whole modules, so that none is cut: the high error
 * correction lets the code be read all the same. Its sizes and logo are configured in config/qr_codes.php.
 */
final class SpotQrCode
{
    /**
     * The QR code as SVG.
     */
    public static function svg(Spot $spot): string
    {
        $size = self::config('size');
        $code = self::write($spot, new ImageRenderer(new RendererStyle($size, self::config('margin')), new SvgImageBackEnd));
        [$area, $logo] = self::logo($spot);

        // The paths of the logo, in black: drawn in the color of the text otherwise
        $paths = str_replace('currentColor', '#000000', preg_replace('/^.*?<svg[^>]*>|<\/svg>\s*$/s', '', self::logoSvg()));
        $box = fn (array $box) => 'x="'.$box[0].'" y="'.$box[1].'" width="'.$box[2].'" height="'.$box[3].'"';

        return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 '.$size.' '.$size.'">'
            .'<rect width="100%" height="100%" fill="#ffffff"/>'
            .preg_replace('/^<\?xml[^>]*>\s*/', '', $code)
            .'<rect '.$box($area).' fill="#ffffff"/>'
            .'<svg '.$box($logo).' viewBox="'.implode(' ', self::logoViewBox()).'">'.$paths.'</svg>'
            .'</svg>';
    }

    /**
     * The QR code as PNG, drawn by GD.
     */
    public static function png(Spot $spot): string
    {
        $image = imagecreatefromstring(self::write($spot, new GDLibRenderer(self::config('size'), self::config('margin'))));
        $white = imagecolorallocate($image, 255, 255, 255);

        // Rounded as GD rounds the modules, so that the area ends where they do
        [[$x, $y, $width, $height], [$logoX, $logoY, $logoWidth, $logoHeight]] = self::logo($spot);
        imagefilledrectangle($image, (int) round($x), (int) round($y), (int) round($x + $width) - 1, (int) round($y + $height) - 1, $white);
        $logo = imagecreatefrompng(self::config('logo_png'));
        imagecopyresampled($image, $logo, (int) round($logoX), (int) round($logoY), 0, 0, (int) round($logoWidth), (int) round($logoHeight), imagesx($logo), imagesy($logo));

        ob_start();
        imagepng($image, null, 9);

        return ob_get_clean();
    }

    /**
     * The name of the file of the QR code, after the spot: `harbour-qr-code.png`.
     */
    public static function filename(Spot $spot, string $extension): string
    {
        return Str::slug($spot->displayName()).'-qr-code.'.$extension;
    }

    /**
     * A setting of the QR codes, from config/qr_codes.php.
     */
    private static function config(string $key): mixed
    {
        return config("qr_codes.{$key}");
    }

    private static function write(Spot $spot, RendererInterface $renderer): string
    {
        return (new Writer($renderer))->writeString(self::content($spot), ecLevel: ErrorCorrectionLevel::H());
    }

    /**
     * What the code holds: the address of the scans of the spot.
     */
    private static function content(Spot $spot): string
    {
        return route('spots.redirect', ['spot' => $spot->uuid]);
    }

    /**
     * The number of modules on a side of the code, and the size of one, in pixels.
     *
     * @return array{int, float}
     */
    private static function modules(Spot $spot): array
    {
        $modules = Encoder::encode(self::content($spot), ErrorCorrectionLevel::H())->getMatrix()->getWidth();

        return [$modules, self::config('size') / ($modules + 2 * self::config('margin'))];
    }

    /**
     * The white area in the middle of the code, made of whole modules, and the logo centered in it, each as its
     * position, width and height, in pixels. The area is an odd number of modules wide and high, as the code is, so
     * that it is centered on it.
     *
     * @return array{array{float, float, float, float}, array{float, float, float, float}}
     */
    private static function logo(Spot $spot): array
    {
        [$modules, $module] = self::modules($spot);
        [, , $viewWidth, $viewHeight] = self::logoViewBox();
        $width = self::config('logo_width');
        $height = $width * $viewHeight / $viewWidth;

        // The smallest odd number of modules that holds the logo and its margin, across and down
        $span = fn (float $size) => (int) ceil(($size + 2 * self::config('logo_padding')) / $module / 2) * 2 + 1;
        [$across, $down] = [$span($width), $span($height)];
        $x = (self::config('margin') + ($modules - $across) / 2) * $module;
        $y = (self::config('margin') + ($modules - $down) / 2) * $module;

        return [
            [$x, $y, $across * $module, $down * $module],
            [$x + ($across * $module - $width) / 2, $y + ($down * $module - $height) / 2, $width, $height],
        ];
    }

    private static function logoSvg(): string
    {
        return file_get_contents(self::config('logo_svg'));
    }

    /**
     * The view box of the logo, which gives its proportions to both formats.
     *
     * @return list<float>
     */
    private static function logoViewBox(): array
    {
        preg_match('/viewBox="([^"]+)"/', self::logoSvg(), $matches);

        return array_map(floatval(...), preg_split('/[\s,]+/', trim($matches[1])));
    }
}
