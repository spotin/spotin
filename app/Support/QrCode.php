<?php

namespace App\Support;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererInterface;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * A QR code to print, such as the one of a spot or of an organization: it leads to the address it holds, usually the
 * one of their scans, with the logo of the app in its middle. The same layout is drawn as SVG and as PNG.
 *
 * The logo hides the middle of the code, on a white area made of whole modules, so that none is cut: the high error
 * correction lets the code be read all the same. Its sizes and logo are configured in config/qr_codes.php.
 */
final class QrCode
{
    /**
     * The QR code of the address as a file to download, as SVG or PNG, named after what it leads to:
     * `harbour-qr-code.png`.
     */
    public static function download(string $content, string $name, string $format): Response
    {
        [$image, $type] = $format === 'png' ? [self::png($content), 'image/png'] : [self::svg($content), 'image/svg+xml'];

        return response($image, headers: [
            'Content-Type' => $type,
            'Content-Disposition' => 'attachment; filename="'.Str::slug($name).'-qr-code.'.$format.'"',
        ]);
    }

    /**
     * The QR code of the address as SVG.
     */
    public static function svg(string $content): string
    {
        $size = self::config('size');
        $code = self::write($content, new ImageRenderer(new RendererStyle($size, self::config('margin')), new SvgImageBackEnd));
        [$area, $logo] = self::logo($content);

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
     * The QR code of the address as PNG, drawn by GD.
     */
    public static function png(string $content): string
    {
        $image = imagecreatefromstring(self::write($content, new GDLibRenderer(self::config('size'), self::config('margin'))));
        $white = imagecolorallocate($image, 255, 255, 255);

        // Rounded as GD rounds the modules, so that the area ends where they do
        [[$x, $y, $width, $height], [$logoX, $logoY, $logoWidth, $logoHeight]] = self::logo($content);
        imagefilledrectangle($image, (int) round($x), (int) round($y), (int) round($x + $width) - 1, (int) round($y + $height) - 1, $white);
        $logo = imagecreatefrompng(self::config('logo_png'));
        imagecopyresampled($image, $logo, (int) round($logoX), (int) round($logoY), 0, 0, (int) round($logoWidth), (int) round($logoHeight), imagesx($logo), imagesy($logo));

        ob_start();
        imagepng($image, null, 9);

        return ob_get_clean();
    }

    /**
     * A setting of the QR codes, from config/qr_codes.php.
     */
    private static function config(string $key): mixed
    {
        return config("qr_codes.{$key}");
    }

    private static function write(string $content, RendererInterface $renderer): string
    {
        return (new Writer($renderer))->writeString($content, ecLevel: ErrorCorrectionLevel::H());
    }

    /**
     * The number of modules on a side of the code, and the size of one, in pixels.
     *
     * @return array{int, float}
     */
    private static function modules(string $content): array
    {
        $modules = Encoder::encode($content, ErrorCorrectionLevel::H())->getMatrix()->getWidth();

        return [$modules, self::config('size') / ($modules + 2 * self::config('margin'))];
    }

    /**
     * The white area in the middle of the code, made of whole modules, and the logo centered in it, each as its
     * position, width and height, in pixels. The area is an odd number of modules wide and high, as the code is, so
     * that it is centered on it.
     *
     * @return array{array{float, float, float, float}, array{float, float, float, float}}
     */
    private static function logo(string $content): array
    {
        [$modules, $module] = self::modules($content);
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
