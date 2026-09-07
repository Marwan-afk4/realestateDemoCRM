<?php

use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Contracts\Debug\ExceptionHandler;

it('includes session keepalive on the login page', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('heartbeatUrl', false)
        ->assertSee('csrf-token', false)
        ->assertDontSee('Your session expired');
});

it('returns a fresh csrf token from the session heartbeat', function () {
    $this->getJson(route('session.heartbeat'))
        ->assertOk()
        ->assertJsonStructure(['csrf', 'authenticated'])
        ->assertJson(['authenticated' => false]);

    expect(session()->token())->not->toBeEmpty();
});

it('keeps an authenticated user signed in through the heartbeat', function () {
    $user = new \App\Models\User(['id' => 1, 'role' => 'admin']);
    $user->exists = true;

    $this->actingAs($user)
        ->getJson(route('session.heartbeat'))
        ->assertOk()
        ->assertJson(['authenticated' => true])
        ->assertJsonStructure(['csrf']);
});

it('redirects expired csrf form posts to login instead of showing 419', function () {
    $request = Request::create(url('/admin/leads'), 'POST', ['_token' => 'stale']);
    $request->headers->set('Accept', 'text/html');
    $request->setLaravelSession($this->app['session']->driver());

    $response = app(ExceptionHandler::class)->render($request, new TokenMismatchException);

    expect($response->isRedirect(route('login')))->toBeTrue();
});

it('shows the login form with an expired message instead of skipping login', function () {
    $this->get('/login?expired=1')
        ->assertOk()
        ->assertSee('Your session expired. Please log in again.', false)
        ->assertSee('name="password"', false);
});
