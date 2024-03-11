<?php

use Dcblogdev\Box\Facades\Box as BoxFacade;
use Dcblogdev\Box\Box;
use Illuminate\Http\RedirectResponse;

beforeEach(function () {
    $this->boxMock = Mockery::mock(Box::class);
});

test('can initialise', function () {
    $this->assertInstanceOf(Box::class, $this->boxMock);
});

test('redirected when connect is called', function () {
    $connect = BoxFacade::connect();

    $this->assertInstanceOf(RedirectResponse::class, $connect);
});

test('is connected returns false when no valid token exists', function () {
    $connect = BoxFacade::isConnected();

    expect($connect)->toBeFalse();
});

/*
test('is connected returns true when a valid token exists', function () {
    $userId = 1;
    BoxToken::create([
        'user_id'      => $userId,
        'access_token' => 'ghgh4h22',
        'refresh_token' => 'ghhhy52!3s22',
        'expires'      => strtotime('+1 day'),
    ]);

    $connect = BoxFacade::isConnected();

    expect($connect)->toBeTrue();
});

test('get null token when token has expired and returnNullNoAccessToken is false', function () {
    $response = BoxFacade::getAccessToken(1, false);

    $this->assertSame(null, $response);
});

test('redirected token when token has expired and redirectWhenNotConnected is true', function () {
    $response = BoxFacade::getAccessToken(1, true);

    $this->assertInstanceOf(RedirectResponse::class, $response);
});

test('returns null when token has expired and redirectWhenNotConnected is false', function () {
    $userId   = 1;
    $response = BoxFacade::getAccessToken($userId, false);

    $this->assertSame(null, $response);
});
*/

