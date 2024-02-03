<?php

namespace Shopwwi\Admin\App\User\Controllers\Auth;

use Shopwwi\Admin\App\User\Controllers\Controllers;
use Shopwwi\Admin\App\User\Models\UserOpens;
use Shopwwi\Admin\App\User\Models\Users;
use Shopwwi\Admin\App\User\Service\AuthService;
use Shopwwi\WebmanAuth\Facade\Auth;
use Shopwwi\WebmanSocialite\Facade\Socialite;
use support\Request;

class TaobaoController extends Controllers
{
    /**
     * 将用户重定向到 taobao 的授权页面
     */
    public function redirect(Request $request)
    {
        try {
            $redirect = Socialite::driver('taobao')->redirect();
            return redirect($redirect);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 从taobao获取用户信息
     */
    public function callback(Request $request)
    {
        $code = $request->input('code');
        $backUser = Socialite::driver('taobao')->setGuzzleOptions(['verify'=>false])->userFromCode($code);
        $openUser = UserOpens::where('open_type','taobao')->where('open_id', $backUser->id)->first();
        if($openUser != null){
            $openUser->open_info = $backUser->raw;
            $openUser->save();
            $user =Users::where('id',$openUser->user_id)->first();
        }else{
            $user = AuthService::addUser(['nickname'=>$backUser->nickname,'avatar'=>$backUser->avatar]);
            UserOpens::create([
                'user_id' => $user->id,
                'open_type' => 'taobao',
                'open_id' => $backUser->id,
                'open_info' => $backUser->raw,
                'open_unionid' => $backUser->unionid,
            ]);
        }
        Auth::guard($this->guard)->fail()->login($user);
        return redirect(shopwwiUserUrl(''));
    }
}