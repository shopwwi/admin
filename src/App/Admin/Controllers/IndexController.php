<?php

namespace Shopwwi\Admin\App\Admin\Controllers;


use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\Admin\Service\User\UserService;
use Shopwwi\Admin\Libraries\Amis\BaseController;
use Shopwwi\Admin\Libraries\Amis\Traits\UseTraits;
use Shopwwi\Admin\Libraries\ServerParameters;
use Shopwwi\Admin\Libraries\Validator;
use support\Request;

class IndexController extends BaseController
{
    use UseTraits;
    public $noNeedAuth = ['index']; //需要登入不需要鉴权
     public $routeAction = ['password'=>['OPTIONS','POST']]; //方法注册 未填写的则直接any
    public function index(Request $request)
    {
        try {
            $admin = $this->admin('true');
            if($this->format() == 'json'){
                $userCount = UserService::getIndexInfo();
                $cpuInfo = ServerParameters::getCpuInfo();
                $memInfo = ServerParameters::getMemInfo();
                $systemInfo = ServerParameters::getPhpAndEnvInfo();
                return shopwwiSuccess(['userCount'=>$userCount,'cpuInfo'=>$cpuInfo,'memInfo'=>$memInfo,'systemInfo'=>$systemInfo]);
            }
            $page = $this->basePage()->title('首页')->bodyClassName('p-0 bg-transparent')->css($this->css())->body([
                shopwwiAmis('service')->api(shopwwiAdminUrl('').'?_format=json')->body([
                    shopwwiAmis('card')->body([
                        shopwwiAmis('flex')->justify('space-between')->alignItems('center')->items([
                            shopwwiAmis('tpl')->className('flex-1 flex-shrink-0 w-full')->tpl('<div class="mb-2 text-2xl">Hi,'.$admin->username.'</div><div class="opacity-50">欢迎回来,您正在使用的是Shopwwi智能管理系统。</div>'),
                            $this->clock()
                        ]),
                    ]),
                    shopwwiAmis('grid')->columns([
                        shopwwiAmis('card')->sm(6)->lg(3)->href(shopwwiAdminUrl('users'))->body([
                            shopwwiAmis('flex')->justify('space-between')->alignItems('center')->items([
                                shopwwiAmis('tpl')->tpl('<div class="mb-2">总用户数</div><div class="text-2xl">${userCount.userNum}</div>'),
                                shopwwiAmis('avatar')->icon('ri-user-line text-blue-600')->size(60)->className('bg-blue-50 card-icon')
                            ]),
                            shopwwiAmis('flex')->justify('space-between')->alignItems('center')->className('mt-2')->items([
                                shopwwiAmis('tpl')->tpl('今日新增：<span class="bold">${userCount.newUserNum}</span>')->className('opacity-50'),
                                shopwwiAmis('button')->icon('ri-arrow-right-s-line')->level('link')->className('text-black')
                            ])
                        ]),
                        shopwwiAmis('card')->sm(6)->lg(3)->href(shopwwiAdminUrl('user/balance'))->body([
                            shopwwiAmis('flex')->justify('space-between')->alignItems('center')->items([
                                shopwwiAmis('tpl')->tpl('<div class="mb-2">总金额数</div><div class="text-2xl">${userCount.balanceCount}</div>'),
                                shopwwiAmis('avatar')->icon('ri-money-dollar-circle-line text-blue-600')->size(60)->className('bg-blue-50 card-icon')
                            ]),
                            shopwwiAmis('flex')->justify('space-between')->alignItems('center')->className('mt-2')->items([
                                shopwwiAmis('tpl')->tpl('今日新增：<span class="bold">${userCount.newBalanceCount}</span>')->className('opacity-50'),
                                shopwwiAmis('button')->icon('ri-arrow-right-s-line')->level('link')->className('text-black')
                            ])
                        ]),
                        shopwwiAmis('card')->sm(6)->lg(3)->href(shopwwiAdminUrl('user/point'))->body([
                            shopwwiAmis('flex')->justify('space-between')->alignItems('center')->items([
                                shopwwiAmis('tpl')->tpl('<div class="mb-2">总积分数</div><div class="text-2xl">${userCount.pointsCount}</div>'),
                                shopwwiAmis('avatar')->icon('ri-bit-coin-line text-blue-600')->size(60)->className('bg-blue-50 card-icon')
                            ]),
                            shopwwiAmis('flex')->justify('space-between')->alignItems('center')->className('mt-2')->items([
                                shopwwiAmis('tpl')->tpl('今日新增：<span class="bold">${userCount.newPointsCount}</span>')->className('opacity-50'),
                                shopwwiAmis('button')->icon('ri-arrow-right-s-line')->level('link')->className('text-black')
                            ])
                        ]),
                        shopwwiAmis('card')->sm(6)->lg(3)->href(shopwwiAdminUrl('user/growth'))->body([
                            shopwwiAmis('flex')->justify('space-between')->alignItems('center')->items([
                                shopwwiAmis('tpl')->tpl('<div class="mb-2">总成长值</div><div class="text-2xl">${userCount.growthCount}</div>'),
                                shopwwiAmis('avatar')->icon('ri-funds-line text-blue-600')->size(60)->className('bg-blue-50 card-icon')
                            ]),
                            shopwwiAmis('flex')->justify('space-between')->alignItems('center')->className('mt-2')->items([
                                shopwwiAmis('tpl')->tpl('今日新增：<span class="bold">${userCount.newGrowthCount}</span>')->className('opacity-50'),
                                shopwwiAmis('button')->icon('ri-arrow-right-s-line')->level('link')->className('text-black')
                            ])
                        ])
                    ]),
                    shopwwiAmis('grid')->columns([
                        shopwwiAmis('flex')->direction('column')->items([
                            shopwwiAmis('card')->header(['title'=>'新增会员统计'])->body(shopwwiAmis('chart')->name('chart1')->height(400)->api(shopwwiAdminUrl('users/stat/new?_format=data&type=day'))),
                            shopwwiAmis('card')->header(['title'=>'系统信息'])->body(shopwwiAmis('property')->items([
                                ['label'=>'软件名称','content'=>'${systemInfo.title}'],
                                ['label'=>'软件作者','content'=>'${systemInfo.author}'],
                                ['label'=>'联系QQ','content'=>'${systemInfo.qq}'],
                                ['label'=>'软件版本','content'=>'v${systemInfo.admin_version}'],
                                ['label'=>'运行环境','content'=>'${systemInfo.os}'],
                                ['label'=>'PHP版本','content'=>'${systemInfo.php_version}'],
                                ['label'=>'项目目录','content'=>'${systemInfo.project_path}','span'=>3],
                                ['label'=>'VUE版本','content'=>'${systemInfo.vue_version}'],
                                ['label'=>'AMIS版本','content'=>'${systemInfo.amis_version}'],
                                ['label'=>'tdesign版本','content'=>'${systemInfo.tdesign_version}'],
                            ]))
                        ])->sm(6)->md(9),
                        shopwwiAmis('card')->header(['title'=>'服务器'])->body([
                            shopwwiAmis('chart')->config([
                                'series' => [
                                    [   'type'=>'gauge',
                                        'anchor'=>['show'=>true,'showAbove'=>true,'size'=>18,'itemStyle'=>['color'=>'#FAC858']],
                                        'pointer' => ['icon'=>'path://M2.9,0.7L2.9,0.7c1.4,0,2.6,1.2,2.6,2.6v115c0,1.4-1.2,2.6-2.6,2.6l0,0c-1.4,0-2.6-1.2-2.6-2.6V3.3C0.3,1.9,1.4,0.7,2.9,0.7z','width'=>8,'length'=>'80%','offsetCenter'=>[0,'8%']],
                                        'progress' => ['show'=>true,'overlap'=>true,'roundCap'=>true],
                                        'axisLine' => ['roundCap'=>true],
                                        'title' => ['fontSize' => 14],
                                        'data' => [['name'=>'Cpu','title'=>['offsetCenter'=>['-40%', '80%']],'detail'=>['offsetCenter'=>['-40%', '100%']],'value'=>'${cpuInfo.usage}'],['name'=>'内存','title'=>['offsetCenter'=>['40%', '80%']],'detail'=>['offsetCenter'=>['40%', '100%']],'value'=>'${memInfo.rate}']],
                                        'detail' => ['width'=>40,'height'=>14,'fontSize'=>14,'color'=>'#fff','backgroundColor'=>'inherit','borderRadius'=>3,'formatter'=>'{value}%']
                                    ],
                                ]
                            ]),
                            shopwwiAmis('divider')->title('CPU信息'),
                            shopwwiAmis()->name('cpuInfo.name')->label('型号'),
                            shopwwiAmis()->name('cpuInfo.cores')->label('核心数'),
                            shopwwiAmis()->name('cpuInfo.cache')->label('缓存'),
                            shopwwiAmis()->name('cpuInfo.usage')->tpl('${cpuInfo.usage}%')->label('使用率'),
                            shopwwiAmis()->name('cpuInfo.free')->tpl('${cpuInfo.free}%')->label('空闲率'),
                            shopwwiAmis('divider')->title('内存信息'),
                            shopwwiAmis('tpl')->name('memInfo.total')->tpl('${memInfo.total}G')->label('总内存'),
                            shopwwiAmis()->name('memInfo.usage')->tpl('${memInfo.usage}G')->label('已使用'),
                            shopwwiAmis()->name('memInfo.php')->tpl('${memInfo.php}M')->label('PHP占用'),
                            shopwwiAmis()->name('memInfo.free')->tpl('${memInfo.free}G')->label('空闲'),
                            shopwwiAmis()->name('memInfo.rate')->tpl('${memInfo.rate}%')->label('使用率'),
                        ]),
                    ]),
                ])

            ]);
            if($this->format() == 'web') return shopwwiSuccess($page);
            return $this->getAdminView(['json'=>$page,'seoTitle'=>'首页']);
        }catch (\Exception $e){
            return $this->backError($e);
        }

    }


    private function clock()
    {
        return shopwwiAmis('card')->className('bg-blingbling max-w-xs')->body([
            shopwwiAmis('custom')
                ->name('clock')->className('text-center')
                ->html('<div id="clock" class="text-3xl"></div><div id="clock-date" class="mt-5"></div>')
                ->onMount(<<<JS
                    const clock = document.getElementById('clock');
                    const tick = () => {
                        clock.innerHTML = (new Date()).toLocaleTimeString();
                        requestAnimationFrame(tick);
                    };
                    tick();
                    const clockDate = document.getElementById('clock-date');
                    clockDate.innerHTML = (new Date()).toLocaleDateString();
                JS),
        ]);
    }


    private function css(): array
    {
        return [
            '.amis-scope .clear-card-mb'                 => [
                'margin-bottom' => '0 !important',
            ],
            '.amis-scope .cxd-Image'                     => [
                'border' => '0',
            ],
            '.amis-scope .bg-blingbling'                 => [
                'color'             => '#fff',
                'background'        => 'linear-gradient(to bottom right, #2C3E50, #FD746C, #FF8235, #ffff1c, #92FE9D, #00C9FF, #a044ff, #e73827)',
                'background-repeat' => 'no-repeat',
                'background-size'   => '1000% 1000%',
                'animation'         => 'gradient 60s ease infinite',
            ],
            '@keyframes gradient'                        => [
                '0%{background-position:0% 0%}
                  50%{background-position:100% 100%}
                  100%{background-position:0% 0%}',
            ],
            '.amis-scope .bg-blingbling .cxd-Card-title' => [
                'color' => '#fff',
            ],
            '.amis-scope .card-icon i' => ['font-size'=>'2rem','line-height'=>'inherit']
        ];
    }

    public function info(Request $request){
        try {
            $admin = $this->admin();
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    return shopwwiSuccess([]);
                }
                $page = $this->basePage()->body([
                    shopwwiAmis('tabs')->tabs([
                        ['title'=>'个人信息','tab'=>$this->baseForm()->body([
                            shopwwiAmis('grid')->gap('lg')->columns([
                                shopwwiAmis('hidden')->name('avatar')->value($admin->avatar),
                                shopwwiAmis('input-image')->name('avatarUrl')->value($admin->avatarUrl)->label('头像')->autoFill(['avatar'=>'${file_name}'])->initAutoFill(false)->receiver(shopwwiAdminUrl('common/upload'))->crop(['aspectRatio'=>1])->xs(12),
                                shopwwiAmis('input-text')->name('username')->value($admin->username)->disabled(true)->label('账号')->xs(12)->lg(6),
                                shopwwiAmis('input-text')->name('nickname')->value($admin->nickname)->label('昵称')->xs(12)->lg(6),
                                shopwwiAmis('input-text')->name('email')->value($admin->email)->label('邮箱')->xs(12)->lg(6),
                                shopwwiAmis('input-text')->name('mobile')->value($admin->mobile)->label('手机')->xs(12)->lg(6),
                                shopwwiAmis('radios')->name('sex')->value($admin->sex)->options(DictTypeService::getAmisDictType('sex'))->label('性别')->xs(12)->lg(6),
                            ])
                        ])->api('post:' . shopwwiAdminUrl('index/info'))
                            ->actions([
                                shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                                shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                            ])
                        ],
                        [ 'title'=>'密码设置', 'tab'=>$this->baseForm()->body([
                            shopwwiAmis('grid')->gap('lg')->columns([
                                shopwwiAmis('input-password')->name('old_password')->label('原密码')->required(true)->md(12),
                                shopwwiAmis('input-password')->name('password')->label('新密码')->required(true)->xs(12)->lg(6),
                                shopwwiAmis('input-password')->name('password_confirmation')->required(true)->label('确认密码')->xs(12)->lg(6),
                            ])
                        ])->api('post:' . shopwwiAdminUrl('index/password'))
                            ->actions([
                                shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                                shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                            ])
                        ]
                    ])
                ]);
                $page = $page->title('个人信息');
                if($this->format() == 'data' || $this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'seoTitle'=>'个人信息']);
            }else{
                $params = shopwwiParams(['avatar','nickname','email','mobile','sex']);
                foreach ($params as $key=>$val){
                    $admin->$key = $val;
                }
                $admin->save();
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return $this->backError($e);
        }
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \support\Response
     */
    public function password(Request $request){
        $validator = Validator::make($request->all(), [
            'old_password' => 'bail|required|min:6|chs_dash_pwd',
            'password' => 'bail|required|confirmed|min:6|chs_dash_pwd',
        ], [], [
            'password' => '密码',
            'old_password' => '原密码',
        ]);
        if($validator->fails()){
            return shopwwiValidator('数据验证失败',$validator->errors());
        }
        try {
            $admin = $this->admin();
            $params = shopwwiParams(['old_password','password']);
            if(!password_verify($params['old_password'],$admin->password)){
                throw new \Exception('原密码不正确');
            }
            $admin->password = $params['password'];
            $admin->save();
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }
}