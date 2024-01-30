<?php

namespace Shopwwi\Admin\App\User\Controllers;

use Shopwwi\Admin\Logic\PayLogic;
use support\Request;
use Yansongda\Pay\Pay;

class BackController
{
    public function alipay(Request $request){
        try {
            Pay::config(PayLogic::config());
            $result = Pay::alipay()->callback();

            return Pay::alipay()->success();
        }catch (\Exception $e){
            return "fail";
        }
    }
}