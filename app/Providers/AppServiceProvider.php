<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Head\Enums\ImageType;
use Laravel\Head\Enums\OgType;
use Laravel\Head\Facades\Head;
use Laravel\Head\HeadBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Head::defaults(function (HeadBuilder $head) {
            $head
                ->title(config('app.name'), suffix: ' - '.config('app.name'))
                ->description(config('app.description'))
                ->canonical()
                ->viewport('width=device-width, initial-scale=1.0')
                ->icon(asset('icons/favicon.ico'), ImageType::Ico)
                ->icon(asset('icons/favicon.png'), ImageType::Png)
                ->icon(asset('icons/favicon.svg'), ImageType::Svg)
                ->appleTouchIcon(asset('icons/apple-touch-icon.png'))
                ->og(siteName: config('app.name'), type: OgType::Website)
                ->manifest(asset('manifest.json'));

            if (config('app.env') === 'production') {
                $head->searchableByRobots();
            } else {
                $head->hiddenFromRobots();
            }
        });
    }
}
