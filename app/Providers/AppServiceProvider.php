<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
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
        Paginator::defaultView('vendor.pagination.daisyui');

        Scramble::configure()
            ->expose(ui: '/api', document: '/api.json')
            ->withOperationTransformers(function (Operation $operation, RouteInfo $routeInfo): void {
                $abilities = collect($routeInfo->route->gatherMiddleware())
                    ->filter(fn ($middleware) => is_string($middleware) && Str::startsWith($middleware, 'abilities:'))
                    ->map(fn (string $middleware) => Str::after($middleware, 'abilities:'));

                $note = $abilities->isEmpty()
                    ? 'Needs a valid token, whatever its abilities.'
                    : 'Needs a token with the '.$abilities->map(fn (string $ability) => "`$ability`")->join(', ', ' and ').' ability.';

                $writes = fn (string $resource) => $abilities->contains(fn (string $ability) => Str::startsWith($ability, $resource.':') && ! Str::endsWith($ability, ':read'));

                if ($writes('organizations')) {
                    $note .= ' Only administrators can do it.';
                } elseif ($writes('spots')) {
                    $note .= ' Only the members of the organization and the administrators can do it.';
                }

                $operation->description(trim($operation->description."\n\n".$note));
            });

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        Head::defaults(function (HeadBuilder $head) {
            $head
                ->title(config('app.name'), suffix: ' - '.config('app.name'))
                ->description(config('app.description'))
                ->canonical()
                ->viewport('width=device-width, initial-scale=1.0')
                ->icon(asset('favicon.ico'), ImageType::Ico)
                ->icon(asset('favicon.png'), ImageType::Png)
                ->icon(asset('favicon.svg'), ImageType::Svg)
                ->appleTouchIcon(asset('apple-touch-icon.png'))
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
