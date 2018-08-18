# Box

A Laravel package for working with Box, this includes authentication use Oauth2.

## Installation

Via Composer

``` bash
$ composer require daveismynamelaravel/box
```

TO Document:
* run migration,
* Publish config
* Edit config
* Add ENV variables

## Usage

A routes example:

```
use DaveismynameLaravel\Box\Facades\Box;

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
