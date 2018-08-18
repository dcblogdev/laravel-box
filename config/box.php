<?php

return [
    'clientId'       => env('BOX_CLIENT_ID'),
    'clientSecret'   => env('BOX_SECRET_ID'),
    'redirectUri'    => url('box/oauth'),
    'boxLandingUri'  => url('box'),
    'urlAuthorize'   => 'https://account.box.com/api/oauth2/authorize',
    'urlAccessToken' => 'https://www.box.com/api/oauth2/token',
];
