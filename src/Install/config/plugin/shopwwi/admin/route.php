<?php

use Webman\Route;
\Shopwwi\Admin\Libraries\Router::resourcePath(config('plugin.shopwwi.admin.app.prefix.admin','/admin'),DIRECTORY_SEPARATOR .'Admin'. DIRECTORY_SEPARATOR .'Controllers');
\Shopwwi\Admin\Libraries\Router::resourcePath(config('plugin.shopwwi.admin.app.prefix.user','/user'),DIRECTORY_SEPARATOR .'User'. DIRECTORY_SEPARATOR .'Controllers');
Route::get(config('plugin.shopwwi.admin.app.prefix.admin','/admin').'[/]',[Shopwwi\Admin\App\Admin\Controllers\IndexController::class,'index']);
Route::get(config('plugin.shopwwi.admin.app.prefix.user','/user').'[/]',[Shopwwi\Admin\App\User\Controllers\SecurityController::class,'index']);