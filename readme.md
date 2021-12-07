[![Latest Version on Packagist](https://img.shields.io/packagist/v/dcblogdev/laravel-box.svg?style=flat-square)](https://packagist.org/packages/dcblogdev/laravel-box)
[![Total Downloads](https://img.shields.io/packagist/dt/dcblogdev/laravel-box.svg?style=flat-square)](https://packagist.org/packages/dcblogdev/laravel-box)

![Logo](https://repository-images.githubusercontent.com/145237952/11826600-494d-11eb-88fa-264ef0affd6b)

A Laravel package for working with box API.

Box API documentation can be found at:
https://developer.box.com/

# Application Register

To use Box API an application needs creating at https://app.box.com/developers/console/newapp

Select the type of application. Select custom if you need to access the API abilities.

Select the Oauth2 app type after selecting custom.

# Install

## Via Composer

```
composer require dcblogdev/laravel-box
```

## Config

You can publish the config file with:

```
php artisan vendor:publish --provider="Dcblogdev\Box\BoxServiceProvider" --tag="config"
```

When published, the config/box.php config file contains:

```php
return [
    'clientId'       => env('BOX_CLIENT_ID'),
    'clientSecret'   => env('BOX_SECRET_ID'),
    'redirectUri'    => env('BOX_REDIRECT_URI'),
    'boxLandingUri'  => env('BOX_LANDING_URI'),
    'urlAuthorize'   => 'https://account.box.com/api/oauth2/authorize',
    'urlAccessToken' => 'https://www.box.com/api/oauth2/token',
];
```

## Migration

You can publish the migration with:

```
php artisan vendor:publish --provider="Dcblogdev\Box\BoxServiceProvider" --tag="migrations"
```

After the migration has been published you can create the tokens tables by running the migration:

```
php artisan migrate
```

## .ENV Configuration

You should add the env variables to your .env file, this allows you to use a different box application on different servers, each box application requires a unique callback URL.

The following are required when using OAuth 2.0 credentials:

The `BOX_REDIRECT_URI` is where Box should redirect to for authentication with Box, upon successful authentication the `BOX_LANDING_URI` is used to direct a user to the desired page. > Note BOX_REDIRECT_URI needs to be the full URI ie https://domain.com/box/


```
BOX_CLIENT_ID=
BOX_SECRET_ID=
BOX_REDIRECT_URI=https://domain.com/box/oauth
BOX_LANDING_URI=https://domain.com/box
```

## Usage


Import Namespace

```php
use Dcblogdev\Box\Facades\Box;
```

A routes example:

```php
Route::get('box', function() {

    //if no box token exists then redirect
    Box::getAccessToken(); 
    
    //box authenticated now box:: can be used freely.

    //example of getting the authenticated users details
    return Box::get('/users/me');
    
});

Route::get('box/oauth', function() {
    return Box::connect();
});
```

Calls can be made by referencing Box:: then the verb get,post,put,patch or delete followed by the end point to call. An array can be passed as a second option.

The end points are relative paths after https://api.box.com/2.0/

Example GET request

```php
Box::get('users/me');
```

Example POST request

```php
Box::post('folders', [
    'name' => 'name of the folder',
    'parent' => [
        'id' => 0
    ]
]);
```

The formula is:

```php
Box::get('path', $array);
Box::post('path', $array);
Box::put('path', $array);
Box::patch('path', $array);
Box::delete('path', $array);
```
## Working with Files

This package provides a clean way of working with files.

To work with files first call ->files() followed by a method.

Get file
Accepts a file id, returns an array.

```php
Box::files()->file($id);
```

Download file
Accepts a file id Optionally a path can be used when $storeDownload is set to true. To download the file only an id is required.

```php
Box::files()->download($id, $path = '', $storeDownload = false);
```

Upload file
Accepts a file path and filename. Optionally specificy the parent, defaults to 0 when ommited.

```php
Box::files()->upload($path, $name, $parent = 0);
```

Upload revision
Accepts the file id, file path and filename. Optionally specificy a new name.

```php
Box::files()->uploadRevision($file_id, $filepath, $name, $newname = null);
```

Delete file
Accepts a file id, returns no output.

```php
Box::files()->destroy($id);
```

## Change log

Please see the [changelog][3] for more information on what has changed recently.

## Contributing

Contributions are welcome and will be fully credited.

Contributions are accepted via Pull Requests on [Github][4].

## Pull Requests

- **Document any change in behaviour** - Make sure the `readme.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0][5]. Randomly breaking public APIs is not an option.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

## Security

If you discover any security related issues, please email dave@dcblog.dev email instead of using the issue tracker.

## License

license. Please see the [license file][6] for more information.

[3]:    changelog.md
[4]:    https://github.com/dcblogdev/laravel-box
[5]:    http://semver.org/
[6]:    license.md
