<?php
/**
 *-------------------------------------------------------------------------s*
 * 中间件
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author      8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 */

namespace Shopwwi\Admin\Middleware;

use Shopwwi\Admin\App\Admin\Service\AdminService;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Cors implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        $controller = $request->controller;
        $action = $request->action;
        $code = 0;
        $msg = '';
        if (strpos(strtolower($controller), '\\app\\admin\\')) {
            $response = $handler($request);
            if (!AdminService::loginSuccess($controller, $action, $code, $msg)) {
                if ($request->expectsJson()) {
                    $response = json(['code' => $code, 'msg' => $msg, 'message' => $msg, 'type' => 'error']);
                } else {
                    $response = view('admin/error',['code' => $code,'msg' => $msg],'');
                }
            }
        }else{
            $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        }

        return $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => config('plugin.shopwwi.admin.app.cross.origin','*'),
            'Access-Control-Allow-Methods' => config('plugin.shopwwi.admin.app.cross.methods','GET,POST,PUT,DELETE,OPTIONS'),
            'Access-Control-Allow-Headers' => config('plugin.shopwwi.admin.app.cross.headers','Real-Host,Content-Type,Authorization,X-Requested-With,Accept,Origin')
        ]);
    }
}