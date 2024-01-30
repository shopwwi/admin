<?php

namespace Shopwwi\Admin\App\User\Controllers\Auth;

use Shopwwi\Admin\App\User\Controllers\Controllers;
use Shopwwi\Admin\App\User\Models\UserOpens;
use Shopwwi\Admin\App\User\Models\Users;
use Shopwwi\Admin\App\User\Service\AuthService;
use Shopwwi\WebmanAuth\Facade\Auth;
use Shopwwi\WebmanSocialite\Facade\Socialite;
use support\Request;

class QqController extends Controllers
{
    /**
     * 将用户重定向到 QQ 的授权页面
     */
    public function redirect(Request $request)
    {
        try {
            $redirect = Socialite::driver('qq')->redirect();
            return redirect($redirect);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 从 QQ 获取用户信息
     *
     */
    public function callback(Request $request)
    {
        $code = $request->input('code');
        $qqUser = Socialite::driver('qq')->setGuzzleOptions(['verify'=>false])->userFromCode($code);
        $openUser = UserOpens::where('open_type','QQ')->where('open_id', $qqUser->id)->first();
        if($openUser != null){
            $openUser->open_info = $qqUser->raw;
            $openUser->save();
            $user =Users::where('id',$openUser->user_id)->first();
        }else{
            $user = AuthService::addUser(['nickname'=>$qqUser->nickname,'avatar'=>$qqUser->avatar]);
            UserOpens::create([
                'user_id' => $user->id,
                'open_type' => 'QQ',
                'open_id' => $qqUser->id,
                'open_info' => $qqUser->raw,
                'open_unionid' => $qqUser->unionid,
            ]);
        }
        Auth::guard($this->guard)->fail()->login($user);
        return redirect(shopwwiUserUrl(''));
    }
}