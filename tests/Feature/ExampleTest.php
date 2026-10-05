<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee("<title>{{__('ui.welcome.title')}} - {{config('app.name')}}</title>", false)
        ->assertSee('<meta name="description" content="'.e(__('ui.welcome.description')).'">', false);
});
