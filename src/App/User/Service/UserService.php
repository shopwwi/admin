<?php

namespace Shopwwi\Admin\App\User\Service;

use Illuminate\Support\Facades\Date;
use Shopwwi\Admin\App\User\Models\Users;

class UserService
{
    /**
     * 判断24小时是否新会员
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public static function checkIsNewUser($userId)
    {
        $isNewUser = false;
        $user = Users::where('id',$userId)->first();
        if($user == null) throw new \Exception('会员不存在');
        if($user->created_at != null){
            $lastTime = Date::parse($user->created_at)->days(1);
            if(Date::parse($user->login_time)->lt($lastTime)){
                $isNewUser = true;
            }
        }
        return $isNewUser;
    }
}