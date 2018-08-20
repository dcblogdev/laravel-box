# Box

A Laravel package for working with Box, this includes authentication use Oauth2. In order to use this package you must have a Box application created at http://developer.box.com/ and set to use `Standard OAuth 2.0 (User Authentication)`

## Installation

Via Composer

``` bash
$ composer require daveismynamelaravel/box
```

In Laravel 5.5 the service provider will automatically get registered. In older versions of the framework just add the service provider in config/app.php file:

```
providers' => [
    // ...
    DaveismynameLaravel\Box\BoxServiceProvider::class,
];
```

```
'aliases' => [
	// ...
	'Box' => DaveismynameLaravel\Box\Facades\Box::class
]
```

You can publish the migration with:

```
php artisan vendor:publish --provider="DaveismynameLaravel\Box\BoxServiceProvider" --tag="migrations"
```

After the migration has been published you can create the box token table by running the migration:

```
php artisan migrate
```

You can publish the box config file with:

```
php artisan vendor:publish --provider="DaveismynameLaravel\Box\BoxServiceProvider" --tag="config"
```

Or

```
php artisan vendor:publish
```

When published, the config/box.php config file contains:

```
return [
    'clientId'       => env('BOX_CLIENT_ID'),
    'clientSecret'   => env('BOX_SECRET_ID'),
    'redirectUri'    => env('BOX_REDIRECT_URI', url('box/oauth')),
    'boxLandingUri'  => env('BOX_LANDING_URI', url('box')),
    'urlAuthorize'   => 'https://account.box.com/api/oauth2/authorize',
    'urlAccessToken' => 'https://www.box.com/api/oauth2/token',
];
```

You should add the env variables to your .env file, this allows you to use a different box application on different servers, each box application requires a unique callback uri.

The follow are required when using OAuth 2.0 credentials:

* clientId
* clientSecret

The `redirectUri` is where Box should redirect to for authentication with Box, upon successful authentication the `boxLandingUri` is used to direct a user to a desired page.

## Usage

A routes example:

```
Route::group(['middleware' => ['web', 'auth']], function(){
    Route::get('box', function(){

        if (!is_string(Box::getAccessToken())) {
            return redirect('box/oauth');
        } else {
            //box folders and file list
            return Box::folders();
        }
    });

    Route::get('box/oauth', function(){
        return Box::connect();
    });
});
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [David Carr][dave@daveismyname.com]

## License

license. Please see the [license file](license.md) for more information.
