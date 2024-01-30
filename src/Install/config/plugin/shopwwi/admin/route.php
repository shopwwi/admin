<?php

use Webman\Route;
\Shopwwi\Admin\Libraries\Router::resourcePath(config('plugin.shopwwi.admin.app.prefix.admin','/admin'),DIRECTORY_SEPARATOR .'Admin'. DIRECTORY_SEPARATOR .'Controllers');
\Shopwwi\Admin\Libraries\Router::resourcePath(config('plugin.shopwwi.admin.app.prefix.user','/user'),DIRECTORY_SEPARATOR .'User'. DIRECTORY_SEPARATOR .'Controllers');
Route::get('/admin[/]',[Shopwwi\Admin\App\Admin\Controllers\IndexController::class,'index']);