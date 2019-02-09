# Box

WIP - not yet ready for production

A Laravel package for working with Box, this includes authentication use Oauth2. In order to use this package you must have a Box application created at http://developer.box.com/ and set to use `Standard OAuth 2.0 (User Authentication)`

## Installation

Via Composer

``` bash
$ composer require daveismyname/laravel-box
```

In Laravel 5.5 the service provider will automatically get registered. In older versions of the framework just add the service provider in config/app.php file:

```
providers' => [
    // ...
    Daveismyname\Box\BoxServiceProvider::class,
];
```

```
'aliases' => [
	// ...
    'Box' => Daveismyname\Box\Facades\Box::class
]
```

Publish the migration with:

```
php artisan vendor:publish --provider="Daveismyname\Box\BoxServiceProvider" --tag="migrations"
```

After the migration has been published you can create the box token table by running the migration:

```
php artisan migrate
```

You can publish the box config file with:

```
php artisan vendor:publish --provider="Daveismyname\Box\BoxServiceProvider" --tag="config"
```

When published, the config/box.php config file contains:

```
return [
    'clientId'       => env('BOX_CLIENT_ID'),
    'clientSecret'   => env('BOX_SECRET_ID'),
    'redirectUri'    => env('BOX_REDIRECT_URI'),
    'boxLandingUri'  => env('BOX_LANDING_URI'),
    'urlAuthorize'   => 'https://account.box.com/api/oauth2/authorize',
    'urlAccessToken' => 'https://www.box.com/api/oauth2/token',
];
```

You should add the env variables to your .env file, this allows you to use a different box application on different servers, each box application requires a unique callback uri.

The follow are required when using OAuth 2.0 credentials:

* clientId
* clientSecret

The `redirectUri` is where Box should redirect to for authentication with Box, upon successful authentication the `boxLandingUri` is used to direct a user to a desired page.

> Note BOX_REDIRECT_URI needs to be the full URI ie https://domain.com/box/

## Usage

A routes example:

```
Route::get('box', function() {

    //if no box token exists then redirect
    if (!is_string(Box::getAccessToken())) {
        return redirect('box/oauth');
    } else {
        //box authenticated now box:: can be used freely.

        //example of getting the authenticated users details
        return Box::get('/users/me');
    }
});

Route::get('box/oauth', function() {
    return Box::connect();
});
```

Box API documenation can be found at https://developer.box.com/reference

Calls can be made by referencing Box:: then the verb get,post,put,patch or delete followed by the end point to call. An array can be passed as a second option.

The end points are relative paths after https://api.box.com/2.0/

Example GET request

```
Box::get('users/me');
```

Example POST request

```
Box::post('folders', [
    'name' => 'name of the folder',
    'parent' => [
        'id' => 0
    ]
]);
```

The formula is:

```
Box::get('path', $array);
Box::post('path', $array);
Box::put('path', $array);
Box::patch('path', $array);
Box::delete('path', $array);
```



## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email [dave@daveismyname.com](dave@daveismyname.com) instead of using the issue tracker.

## License

license. Please see the [license file](license.md) for more information.
