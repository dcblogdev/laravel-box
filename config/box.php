<?php

return [
    'clientId'       => env('BOX_CLIENT_ID', 'w1fmmn8yd6lbx8y956rsgrb2s01ul4w4'),
    'clientSecret'   => env('BOX_SECRET_ID', 'aFZNWExesb2GsCQxWy2w03c3WtW2udKN'),
    'redirectUri'    => url('box/oauth'),
    'boxLandingUri'  => url('box'),
    'urlAuthorize'   => 'https://account.box.com/api/oauth2/authorize',
    'urlAccessToken' => 'https://www.box.com/api/oauth2/token',
];
