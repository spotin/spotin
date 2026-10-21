<?php

use Illuminate\Support\Facades\Route;

test('the documentation is public, at /api and /api.json', function () {
    $this->get('/api')->assertOk()->assertSee('Spot in® API', false);
    $this->get('/api.json')->assertOk()->assertJsonPath('info.title', 'Spot in® API');
});

test('the documentation is rendered by Scalar, loaded from a pinned version, without a proxy that would see the token', function () {
    $html = $this->get('/api')->assertOk()->getContent();

    expect($html)->toMatch('/<script src="https:\/\/cdn\.jsdelivr\.net\/npm\/@scalar\/api-reference@\d+\.\d+\.\d+"><\/script>/')
        ->and($html)->toContain('Scalar.createApiReference')
        ->and($html)->not->toContain('proxy.scalar.com')
        ->and($html)->toContain('"persistAuth":true')
        // The title of the renderer, not the one of the spec, names the downloaded document.
        ->and($html)->toMatch('/\.\.\.\{[^\n]*"title":"Spot in\\\\u00ae API"/');
});

test('every route of the API is documented, behind a bearer token', function () {
    $document = $this->getJson('/api.json')->assertOk();

    $routes = collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($route) => str_starts_with($route->uri(), 'api/v1/'))
        ->flatMap(fn ($route) => collect($route->methods())
            ->reject(fn ($method) => $method === 'HEAD')
            ->map(fn ($method) => strtolower($method).' '.preg_replace('/\{(\w+):\w+\}/', '{$1}', substr($route->uri(), strlen('api/v1'))))
        )
        ->sort()
        ->values()
        ->all();

    $documented = collect($document->json('paths'))
        ->flatMap(fn ($operations, $path) => collect(array_keys($operations))->map(fn ($method) => $method.' '.$path))
        ->sort()
        ->values()
        ->all();

    expect($documented)->toBe($routes)
        ->and($document->json('components.securitySchemes.http.scheme'))->toBe('bearer')
        ->and($document->json('security'))->toBe([['http' => []]]);
});

test('each operation says which token ability it needs and who can do it', function () {
    $document = $this->getJson('/api.json')->assertOk();

    $description = fn (string $method, string $path) => $document->json("paths.$path.$method.description");

    expect($description('get', '/spots'))->toContain('`spots:read`')
        ->and($description('post', '/organizations/{organization}/spots'))->toContain('`spots:create`')->toContain('members of the organization')
        ->and($description('patch', '/spots/{spot}'))->toContain('`spots:update`')
        ->and($description('delete', '/spots/{spot}'))->toContain('`spots:delete`')
        ->and($description('post', '/organizations'))->toContain('`organizations:create`')->toContain('administrators')
        ->and($description('delete', '/organizations/{organization}'))->toContain('`organizations:delete`')->toContain('administrators')
        ->and($description('get', '/organizations'))->toContain('`organizations:read`')->not->toContain('administrators')
        ->and($description('get', '/me'))->toContain('whatever its abilities');
});

test('the lists document their filters and the creation of a spot its body', function () {
    $document = $this->getJson('/api.json')->assertOk();

    $parameters = fn (string $path) => collect($document->json("paths.$path.get.parameters"))->pluck('name')->all();

    expect($parameters('/spots'))->toContain('filter[query]', 'filter[mine]', 'include')
        ->and($parameters('/organizations'))->toContain('filter[query]', 'filter[mine]')
        ->and($parameters('/organizations/{organization}/spots'))->toContain('filter[query]', 'filter[mine]', 'include')
        ->and($document->json('components.schemas.StoreSpotRequest.required'))->toBe(['name', 'latitude', 'longitude']);
});

test('the resources are documented in the JSON:API format, with their relationships', function () {
    $document = $this->getJson('/api.json')->assertOk();

    expect($document->json('paths./spots.get.responses.200.content'))->toHaveKey('application/vnd.api+json')
        ->and($document->json('components.schemas.SpotResource.properties.type.const'))->toBe('spots')
        ->and(array_keys($document->json('components.schemas.SpotResource.properties.relationships.properties')))->toBe(['organization', 'creator'])
        ->and(array_keys($document->json('components.schemas.UserResource.properties.attributes.properties')))->toBe(['name'])
        ->and(array_keys($document->json('components.schemas.MeResource.properties.meta.properties')))->toBe(['token']);
});
