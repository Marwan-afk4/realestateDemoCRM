<?php

it('serves the swagger ui', function () {
    $this->get(route('docs.api'))
        ->assertSuccessful()
        ->assertSee('swagger-ui', false)
        ->assertSee('/docs/openapi.yaml', false);
});

it('serves the openapi specification using APP_URL', function () {
    $apiBaseUrl = rtrim((string) config('app.url'), '/').'/api';

    $this->get(route('docs.openapi'))
        ->assertSuccessful()
        ->assertSee('CRM API', false)
        ->assertSee($apiBaseUrl, false)
        ->assertDontSee('{{API_BASE_URL}}', false)
        ->assertDontSee('/admin/homepage', false)
        ->assertDontSee('/admin/users', false)
        ->assertSee('/register', false)
        ->assertSee('/login', false)
        ->assertSee('/user/profile', false)
        ->assertSee('/user/homepage', false)
        ->assertSee('/user/sell-requests', false)
        ->assertSee('/crm/contacts', false)
        ->assertSee('/crm/pipeline', false)
        ->assertSee('/crm/deals', false)
        ->assertSee('/crm/lookups', false);
});
