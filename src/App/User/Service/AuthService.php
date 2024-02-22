<?php
/**
 *-------------------------------------------------------------------------s*
 *
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
namespace Shopwwi\Admin\App\User\Service;

use Shopwwi\Admin\App\Admin\Service\User\GrowthService;
use Shopwwi\Admin\App\Admin\Service\User\PointService;
use Shopwwi\Admin\App\User\Models\Users;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Db;

class AuthService
{
    /**
     * 密码登入
     * @param $name
     * @param $password
     * @return mixed
     * @throws \Exception
     */
    public static function login($name,$password)
    {
        $is_mobile = shopwwiIsPhoneNumber($name);
        $is_email = shopwwiIsEmailText($name);
        $user = Users::where('username',$name);
        if ($is_mobile){
            $user->orWhere('phone',$name);
        }
        if($is_email){
            $user->orWhere('email',$name);
        }
        $info = $user->first();
        if($info == null || !password_verify($password,$info->password)){
            throw new \Exception('账号或密码错误');
        }

        // 更新ip及时间
        $info->last_login_ip = $info->login_ip;
        $info->last_login_time = $info->login_time;
        $info->login_ip = request()->getRealIp();
        $info->login_num = Db::raw('login_num + 1');
        $info->login_time = now();
        $info->save();
        // 发放积分
        PointService::optionsPoints('LOGIN',$info->id);
        // 发放成长值
        GrowthService::optionsPoints('LOGIN',$info->id);
        return Auth::guard('user')->fail()->login($info);
    }

    /**
     * 通过短信登入/注册
     * @param $name
     * @param $code
     * @return mixed
     * @throws \Exception
     */
    public static function smsLogin($name,$code)
    {
        $is_mobile = shopwwiIsPhoneNumber($name);
        $is_email = shopwwiIsEmailText($name);
        $user = new Users();
        if(!$is_mobile && !$is_email){
            throw new \Exception('数据异常');
        }
        if ($is_mobile){
            SmsCodeService::checkCode($name,$code,'login');
            $info = $user->where('phone',$name)->first();
        }
        if($is_email){
            EmailCodeService::checkCode($name,$code,'login');
            $info = $user->where('email',$name)->first();
        }

        if($info == null){
            if($is_mobile){
                $info = self::addUser(['phone'=>$name,'password'=>mt_rand(1000000,9999999)]);
            }else if($is_email){
                $info = self::addUser(['email'=>$name,'password'=>mt_rand(1000000,9999999)]);
            }
        }
        // 更新ip及时间
        $info->last_login_ip = $info->login_ip;
        $info->last_login_time = $info->login_time;
        $info->login_ip = request()->getRealIp();
        $info->login_num = Db::raw('login_num + 1');
        $info->login_time = now();
        $info->save();
        // 发放积分
        PointService::optionsPoints('LOGIN',$info->id);
        // 发放成长值
        GrowthService::optionsPoints('LOGIN',$info->id);
        return Auth::guard('user')->fail()->login($info);
    }

    /**
     * 发送验证码
     * @param $account
     * @param $type
     * @param $ip
     * @param $userId
     * @return void
     * @throws \Exception
     */
    public static function sendCode($account,$type,$ip,$userId)
    {
        $is_mobile = shopwwiIsPhoneNumber($account);
        $is_email = shopwwiIsEmailText($account);
        if($is_mobile){
            SmsCodeService::sendCode($account,$type,$ip,$userId);
        }else if($is_email){
            EmailCodeService::sendCode($account,$type,$ip,$userId);
        }else{
            throw new \Exception(trans('phoneOrEmail',[],'auth'));
        }
    }

    /**
     * 找回密码
     * @param $params
     * @return void
     * @throws \Exception
     */
    public static function forget($params)
    {
        $is_mobile = shopwwiIsPhoneNumber($params['account']);
        $is_email = shopwwiIsEmailText($params['account']);
        if($is_mobile){
            SmsCodeService::checkCode($params['account'],$params['code'],'forget');
            $user = Users::where('phone',$params['account'])->first();
            if ($user == null){
                throw new \Exception('用户不存在');
            }
            $user->password = $params['password'];
            $user->save();
        }else if($is_email){
            EmailCodeService::checkCode($params['account'],$params['code'],'forget');
            $user = Users::where('email',$params['account'])->first();
            if ($user == null){
                throw new \Exception('用户不存在');
            }
            $user->password = $params['password'];
            $user->save();
        }else{
            throw new \Exception(trans('errorData',[],'messages'));
        }
    }

    /**
     * 注册
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public static function register($params)
    {
        $is_mobile = shopwwiIsPhoneNumber($params['account']);
        $is_email = shopwwiIsEmailText($params['account']);

        if($is_mobile){
            SmsCodeService::checkCode($params['account'],$params['code'],'register');
            $has = Users::where('phone',$params['account'])->first();
            if ($has != null){
                throw new \Exception('手机号已存在');
            }
            $user = self::addUser(['phone'=>$params['account'],'password'=>$params['password']]);
        }else if($is_email){
            EmailCodeService::checkCode($params['account'],$params['code'],'register');
            $has = Users::where('email',$params['account'])->first();
            if ($has != null){
                throw new \Exception('邮箱已存在');
            }
            $user = self::addUser(['email'=>$params['account'],'password'=>$params['password']]);
        }else{
            throw new \Exception(trans('errorData',[],'messages'));
        }

        return Auth::guard('user')->fail()->login($user);
    }

    /**
     * 写入会员
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public static function addUser($params)
    {
        // 手机号默认为绑定状态
        if(!empty($params['phone'])){
            $params['phone_bind'] = 1;
        }
        // 邮箱默认为绑定状态
        if(!empty($params['email'])){
            $params['email_bind'] = 1;
        }
        // 随机用户名
        if(empty($params['username'])){
            $params['username'] = self::getRandName('u',8);
        }
        // 随机昵称
        if(empty($params['nickname'])){
            $params['nickname'] = self::getRandName('U_',6);
        }
        $user = Users::create($params);
        // 发放积分
        PointService::optionsPoints('REGISTER',$user->id);
        // 发放成长值
        GrowthService::optionsPoints('REGISTER',$user->id);
        return $user;
    }
    /**
     * 生成随机名称
     * @param string $prefix
     * @param int $num
     * @param string $name
     * @return string
     */
    public static function getRandName($prefix = 'user_', $num = 6,$name ='username'){
        $user_name = '';
        $chars = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        for ( $i = 0; $i < $num; $i++ )
        {
            $user_name .= $chars[mt_rand(0, count($chars)-1)];
        }
        $user_name = $prefix.strtoupper(base_convert(time() - 1420070400, 10, 36)).$user_name;
        $user = Users::where( $name , $user_name)->first();
        if(!empty($user)) {
            for ($i = 1;$i < 3;$i++) {
                $user_name .= $chars[mt_rand(0, count($chars)-1)];
                $user = Users::where( $name , $user_name)->first();
                if(empty($user)) {//查询为空表示当前会员名可用
                    break;
                }
            }
        }
        return $user_name;
    }

    /**
     * 设置支付密码
     * @param $user
     * @param $params
     * @return void
     * @throws \Exception
     */
    public static function setPayword($user,$params)
    {
        if($user->email_bind == 1 || $user->phone_bind == 1){
            if($params['auth'] == 1){
                SmsCodeService::checkCode($user->phone,$params['code'],'auth');
            }else{
                EmailCodeService::checkCode($user->email,$params['code'],'auth');
            }
            $user->pay_pwd = $params['payword'];
            $user->save();
            return;
        }else{
            if(!password_verify($params['old_password'],$user->password)){
                throw new \Exception('原密码错误');
            }
            $user->pay_pwd = $params['payword'];
            $user->save();
            return;
        }
    }

    /**
     * 设置密码
     * @param $user
     * @param $params
     * @return void
     * @throws \Exception
     */
    public static function setPassword($user,$params)
    {
        if($user->email_bind == 1 || $user->phone_bind == 1){
            if($params['auth'] == 1){
                SmsCodeService::checkCode($user->phone,$params['code'],'auth');
            }else{
                EmailCodeService::checkCode($user->email,$params['code'],'auth');
            }
            $user->password = $params['password'];
            $user->save();
            return;
        }else{
            if(!password_verify($params['old_password'],$user->password)){
                throw new \Exception('原密码错误');
            }
            $user->password = $params['password'];
            $user->save();
            return;
        }
    }

    /**
     * 设置手机号
     * @param $user
     * @param $params
     * @return void
     * @throws \Exception
     */
    public static function setPhone($user,$params)
    {
        if($user->email_bind == 1 || $user->phone_bind == 1){
            if($params['auth'] == 1){
                SmsCodeService::checkCode($user->phone,$params['code'],'auth');
            }else{
                EmailCodeService::checkCode($user->email,$params['code'],'auth');
            }
            SmsCodeService::checkCode($params['account'],$params['account_code'],'bind');
            $user->phone = $params['account'];
            $user->phone_bind = 1;
            $user->save();
            return;
        }else{
            if(!password_verify($params['password'],$user->password)){
                throw new \Exception('登入密码错误');
            }
            SmsCodeService::checkCode($params['account'],$params['account_code'],'bind');
            $user->phone = $params['account'];
            $user->phone_bind = 1;
            $user->save();
            return;
        }
    }

    /**
     * 设置邮件
     * @param $user
     * @param $params
     * @return void
     * @throws \Exception
     */
    public static function setEmail($user,$params)
    {
        if($user->email_bind == 1 || $user->phone_bind == 1){
            if($params['auth'] == 1){
                SmsCodeService::checkCode($user->phone,$params['code'],'auth');
            }else{
                EmailCodeService::checkCode($user->email,$params['code'],'auth');
            }
            EmailCodeService::checkCode($params['account'],$params['account_code'],'bind');
            $user->email = $params['account'];
            $user->email_bind = 1;
            $user->save();
            return;
        }else{
            if(!password_verify($params['password'],$user->password)){
                throw new \Exception('登入密码错误');
            }
            EmailCodeService::checkCode($params['account'],$params['account_code'],'bind');
            $user->email = $params['account'];
            $user->email_bind = 1;
            $user->save();
            return;
        }
    }
}