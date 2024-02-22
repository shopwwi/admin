<?php

namespace Shopwwi\Admin\App\User\Controllers;

use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\User\Service\AuthService;
use Shopwwi\Admin\App\User\Service\GradeService;
use Shopwwi\Admin\Libraries\Validator;
use Shopwwi\WebmanAuth\Facade\Auth;
use support\Request;
use support\Response;

class SecurityController extends Controllers
{
    protected $activeKey = 'authSecurity';
    /**
     * 账户安全
     * @param Request $request
     * @return \support\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try {
            $user= $this->user();
            $gradeList = GradeService::getGradeList();
            $user->grade = $gradeList->where('id',$user->grade_id)->first();
            $page =$this->basePage()->body([
                shopwwiAmis('card')->header(['title'=> $user->nickname , 'subTitle' =>"绑定邮箱: $user->email, 手机号码: $user->mobile",'avatar' => $user->avatarUrl,'avatarClassName'=>'w-14' ])
                    ->body([
                        ['label'=>'会员等级','type'=>'tpl','tpl'=>"<span class='label label-danger'>".($user->grade == null ? '': $user->grade->name).'</span>'],
                        ['label'=> '上次登录', 'value' => "$user->last_login_time 　|　IP地址: $user->last_login_ip"]
                    ])->toolbar([
                        shopwwiAmis('button')->actionType('dialog')->label('修改信息')->dialog([
                            'title' => '修改信息',
                            'body' => [
                                shopwwiAmis('form')->body([
                                    shopwwiAmis('input-image')->crop(['aspectRatio'=>1])->name('avatarUrl')->label('头像')->autoFill(['avatar'=>'${file_name}'])->receiver(shopwwiUserUrl('asset/avatar')),
                                    shopwwiAmis('hidden')->name('address_area_id')->value('${city.districtCode}'),
                                    shopwwiAmis('hidden')->name('avatar'),
                                    shopwwiAmis('hidden')->name('address_area_info')->value('${city.province} ${city.city} ${city.district}'),
                                    shopwwiAmis('hidden')->name('address_city_id')->value('${city.cityCode}'),
                                    shopwwiAmis('hidden')->name('address_province_id')->value('${city.provinceCode}'),
                                    shopwwiAmis('input-text')->name('nickname')->label('昵称'),
                                    shopwwiAmis('radios')->name('sex')->options(DictTypeService::getAmisDictType('sex'))->label('性别'),
                                    shopwwiAmis('input-date')->name('birthday')->label('生日')->format('YYYY-MM-DD'),
                                    shopwwiAmis('input-city')->name('city')->extractValue(false)->label('所在地区'),
                                ])->api(shopwwiUserUrl('asset/set'))->initApi(shopwwiUserUrl('security/info'))
                            ]
                        ])
                    ]),
                shopwwiAmis('table')
                    ->data([
                        ['icon'=>'fa-solid fa-user-lock','is_active' => !empty($user->password),'title'=>'登录密码','desc'=>'安全性高的密码可以使账号更安全。建议您定期更换密码，且设置一个包含数字和字母，并长度超过6位以上的密码，为保证您的账户安全，只有在您绑定邮箱或手机后才可以修改密码。','buttonText'=>'修改密码','type'=>'password'],
                        ['icon'=>'fa-solid fa-envelope','is_active' => $user->email_bind == 1,'title'=>'邮箱绑定','desc'=>'进行邮箱验证后，邮箱可以用于登录和接收敏感操作的身份验证信息，以及订阅更优惠商品的促销邮件。','buttonText'=>'绑定邮箱','type'=>'email'],
                        ['icon'=>'fa-solid ri-smartphone-line','is_active' => $user->phone_bind == 1,'title'=>'手机绑定','desc'=>'进行手机验证后，可用于接收敏感操作的身份验证信息，非常有助于保护您的账号和账户财产安全。','buttonText'=>'绑定手机','type'=>'phone'],
                        ['icon'=>'fa-solid fa-key','is_active' => !empty($user->pay_pwd),'title'=>'支付密码','desc'=>'设置支付密码后，在使用账户中余额时，需输入支付密码。','buttonText'=>'修改密码','type'=>'payword']
                    ])
                    ->columns([
                        shopwwiAmis('icon')->icon('${icon}')->className('text-3xl text-center text-gray-400'),
                        shopwwiAmis('tpl')->tpl("<div class='font-bold py-2'>\${title}</div>\${is_active?'<span class=\'text-success\'>已设置</span>':'<span class=\'text-danger\'>未设置</span>'} ")->width(100)->className('text-center'),
                        shopwwiAmis(null)->name('desc'),
                        shopwwiAmis('operation')->width(100)->buttons([
                            shopwwiAmis('service')->schemaApi(shopwwiUserUrl('security/${type}')),
                        ])
                    ]),
            ]);
            if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
            return $this->getUserView(['seoTitle'=>'账户安全','menuActive'=>'authSecurity','json'=>$page]);
        }catch (\Exception $e){
            return $this->backError($e);
        }

    }

    public function info(Request $request)
    {
        try {
            $user = $this->user();
            return shopwwiSuccess([
                'address_area_id' => $user->address_area_id,
                'address_area_info' => $user->address_area_info,
                'address_city_id' => $user->address_city_id,
                'address_province_id' => $user->address_province_id,
                'birthday' => $user->birthday,
                'nickname' => $user->nickname,
                'sex' => $user->sex,
                'city' => ['code'=>$user->address_area_id],
                'avatarUrl' => $user->avatarUrl
            ]);
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 账户密码
     * @return Response
     * @throws \Exception
     */
    public function password()
    {
        try {
            $user= $this->user();
            if($this->format() == 'json'){
                try {
                    $params = shopwwiParams(['old_password','auth','code','password']);
                    AuthService::setPassword($user,$params);
                    return shopwwiSuccess();
                }catch (\Exception $E){
                    return shopwwiError($E->getMessage());
                }
            }
            $title = '修改密码';

            $list = [];
            if($user->email_bind == 1){
                $list[] = ['label'=>$user->email,'value' => 2];
            }
            if($user->phone_bind == 1){
                $list[] = ['label'=>$user->phone,'value' => 1];
            }
            $form = [
                shopwwiAmis('select')->options($list)->name('auth')->label('验证方式')->required(true),
                shopwwiAmis('input-text')->label('短信码')->name('code')->placeholder('短信码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                    ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                        ->actionType('ajax')->api(shopwwiUserUrl('security/code?auth=${auth}&type=auth'))
                    ),
                shopwwiAmis('input-password')->name('password')->label('新密码')->required(true)
            ];
            if(empty($list)){
                $form = [
                    shopwwiAmis('input-password')->name('old_password')->label('原密码')->required(true),
                    shopwwiAmis('input-password')->name('password')->label('新密码')->required(true)
                ];
            }
            $page = shopwwiAmis('button')->label($title)->actionType('dialog')->dialog([
                'title' => $title,
                'body' => [
                    shopwwiAmis('form')->body($form)->api(shopwwiUserUrl('security/password?_format=json'))
                ]
            ]);
            return shopwwiSuccess($page);
        }catch (\Exception $e){
            return $this->backError($e);
        }

    }

    /**
     * 邮箱设置
     * @return \support\Response
     * @throws \Exception
     */
    public function email()
    {
        try {

        $user= $this->user();
        if($this->format() == 'json'){
            try {
                $params = shopwwiParams(['password','auth','code','account','account_code']);
                AuthService::setEmail($user,$params);
                return shopwwiSuccess();
            }catch (\Exception $E){
                return shopwwiError($E->getMessage());
            }
        }
        $title = $user->email_bind == 1 ? '修改邮箱':'绑定邮箱';
        $list = [];
        if($user->email_bind == 1){
            $list[] = ['label'=>$user->email,'value' => 2];
        }
        if($user->phone_bind == 1){
            $list[] = ['label'=>$user->phone,'value' => 1];
        }
        $form = [
            shopwwiAmis('select')->options($list)->name('auth')->label('验证方式')->required(true),
            shopwwiAmis('input-text')->label('验证码')->name('code')->placeholder('短信码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                    ->actionType('ajax')->api(shopwwiUserUrl('security/code?auth=${auth}&type=auth'))
                ),
            shopwwiAmis('input-text')->name('account')->label('新邮件')->required(true),
            shopwwiAmis('input-text')->label('邮件码')->name('account_code')->placeholder('邮件码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                ->addOn(shopwwiAmis('button')->label('发送邮件码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                    ->actionType('ajax')->api(shopwwiUserUrl('security/code?account=${account}&type=bind'))
                ),
        ];
        if(empty($list)){
            $form = [
                shopwwiAmis('input-password')->name('password')->label('登入密码')->required(true),
                shopwwiAmis('input-text')->name('account')->label('邮箱')->required(true),
                shopwwiAmis('input-text')->label('验证码')->name('account_code')->placeholder('验证码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                    ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                        ->actionType('ajax')->api(shopwwiUserUrl('security/code?account=${account}&type=bind'))
                    ),
            ];
        }
        $page = shopwwiAmis('button')->actionType('dialog')->label($title)->dialog([
            'title' => $title,
            'body' => [
                shopwwiAmis('form')->body($form)
            ]
        ]);
        return shopwwiSuccess($page);
        }catch (\Exception $e){
            return $this->backError($e);
        }
    }

    /**
     * 手机设置
     * @return \support\Response
     * @throws \Exception
     */
    public function phone()
    {
        try {


        $user= $this->user();
        if($this->format() == 'json'){
            $params = shopwwiParams(['password','auth'=>1,'code','account'=>'','account_code']);
            AuthService::setPhone($user,$params);
            return shopwwiSuccess();
        }

        $title = $user->phone_bind == 1 ? '修改手机':'绑定手机';
        $list = [];
        if($user->email_bind == 1){
            $list[] = ['label'=>$user->email,'value' => 2];
        }
        if($user->phone_bind == 1){
            $list[] = ['label'=>$user->phone,'value' => 1];
        }
        $form = [
            shopwwiAmis('select')->options($list)->name('auth')->label('验证方式')->required(true),
            shopwwiAmis('input-text')->label('验证码')->name('code')->placeholder('验证码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                    ->actionType('ajax')->api(shopwwiUserUrl('security/code?auth=${auth}&type=auth'))
                ),
            shopwwiAmis('input-text')->name('account')->label('新手机号')->required(true),
            shopwwiAmis('input-text')->label('短信码')->name('account_code')->placeholder('短信码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                ->addOn(shopwwiAmis('button')->label('发送短信码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                    ->actionType('ajax')->api(shopwwiUserUrl('security/code?account=${account}&type=bind'))
                ),
        ];
        if(empty($list)){
            $form = [
                shopwwiAmis('input-password')->name('password')->label('登入密码')->required(true),
                shopwwiAmis('input-text')->name('account')->label('手机号')->required(true),
                shopwwiAmis('input-text')->label('验证码')->name('account_code')->placeholder('验证码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                    ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                        ->actionType('ajax')->api(shopwwiUserUrl('security/code?account=${account}&type=bind'))
                    ),
            ];
        }

        $page = shopwwiAmis('button')->actionType('dialog')->label($title)->dialog([
            'title' => $title,
            'body' => [
                shopwwiAmis('form')->body($form)
            ]
        ]);
        return shopwwiSuccess($page);
        }catch (\Exception $e){
            return $this->backError($e);
        }
    }

    /**
     * 支付密码设置
     * @return Response
     * @throws \Exception
     */
    public function payword()
    {
        $user= $this->user();
        if($this->format() == 'json'){
            try {
                $params = shopwwiParams(['old_password','auth','code','payword']);
                AuthService::setPayword($user,$params);
                return shopwwiSuccess();
            }catch (\Exception $e){
                return shopwwiError($e->getMessage());
            }
        }

        $title = empty($user->pay_pwd) ? '设置密码':'修改支付密码';
        $list = [];
        if($user->email_bind == 1){
            $list[] = ['label'=>$user->email,'value' => 2];
        }
        if($user->phone_bind == 1){
            $list[] = ['label'=>$user->phone,'value' => 1];
        }
        $form = [
            shopwwiAmis('select')->options($list)->name('auth')->label('验证方式')->required(true),
            shopwwiAmis('input-text')->label('短信码')->name('code')->placeholder('短信码')->maxLength(6)->required(true)->inputControlClassName('login-input code')
                ->addOn(shopwwiAmis('button')->label('发送验证码')->countDown(60)->countDownTpl('${timeLeft} 秒后重发')
                    ->actionType('ajax')->api(shopwwiUserUrl('security/code?auth=${auth}&type=auth'))
                ),
            shopwwiAmis('input-password')->name('payword')->label('支付密码')->required(true)
        ];
        if(empty($list)){
            $form = [
                shopwwiAmis('input-password')->name('old_password')->label('登入密码')->required(true),
                shopwwiAmis('input-password')->name('payword')->label('支付密码')->required(true)
            ];
        }
        $page = shopwwiAmis('button')->actionType('dialog')->label($title)->dialog([
            'title' => $title,
            'body' => [
                shopwwiAmis('form')->body($form)->api(shopwwiUserUrl('security/payword?_format=json'))
            ]
        ]);
        return shopwwiSuccess($page);
    }

    /**
     * 发送验证码
     * @param Request $request
     * @return Response
     */
    public function code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:auth,bind'
        ], [], [
            'type' => '类型'
        ]);
        if ($validator->fails()) {
            return shopwwiValidator('数据验证失败',$validator->errors());
        }
        $params = shopwwiParams(['account', 'type','auth']);

        try {
            $user = Auth::guard($this->guard)->fail(false)->user();
            if($params['type'] == 'auth'){
                if($params['auth'] == 1){
                    $params['account'] = $user->phone;
                }else if($params['auth'] == 2){
                    $params['account'] = $user->email;
                }
            }
            AuthService::sendCode($params['account'], $params['type'], $request->getRealIp(), $user->id ?? 0);
            return shopwwiSuccess(['authCodeVerifyTime' => shopwwiConfig('siteRule.authCodeVerifyTime',5), 'authCodeResendTime' => shopwwiConfig('siteRule.authCodeResendTime',60)]);
        } catch (\Exception $e) {
            return shopwwiError($e->getMessage());
        }
    }
}