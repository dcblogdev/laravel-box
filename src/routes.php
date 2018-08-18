<?php

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
