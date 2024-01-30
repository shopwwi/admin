<?php

namespace Shopwwi\Admin\App\Admin\Controllers;

use Shopwwi\Admin\App\Admin\Models\SysUser;
use Shopwwi\Admin\Install\InstallService;
use Shopwwi\Admin\Libraries\Validator;
use support\Request;

class InstallController
{
    public $noNeedLogin = ['index','check','store','text']; //不需要登入
     public $routeAction = ['check'=>['OPTIONS','POST']]; //方法注册 未填写的则直接any

    public function index(Request $request){
        if($this->checkIsInstall()){ // 已经安装过了
            $page = shopwwiAmis('page')->bodyClassName('must p-0 bg-transparent')->body([
                shopwwiAmis('wrapper')->body(
                    shopwwiAmis('flex')->items([
                        shopwwiAmis('link')->href('/')->body(
                            shopwwiAmis('image')->src('/static/uploads/common/logo.svg')->imageMode('original')->innerClassName('must border-0')
                        ),
                        shopwwiAmis('tpl')->tpl('系统安装')->className('text-xl')
                    ])->className('max-w-5xl mx-auto')->alignItems('center')->justify('space-between')
                )->className('hd'),
                shopwwiAmis('wrapper')->className('flex items-center justify-center h-96')->body('系统已经安装过了，如果要重新安装，那么请删除public目录下的lock文件')
            ]);
        }else{
            $page = shopwwiAmis('page')->bodyClassName('must p-0 bg-transparent')->body(
                [
                    shopwwiAmis('wrapper')->body(
                        shopwwiAmis('flex')->items([
                            shopwwiAmis('link')->href('/')->body(
                                shopwwiAmis('image')->src('/static/uploads/common/logo.svg')->imageMode('original')->innerClassName('must border-0')
                            ),
                            shopwwiAmis('tpl')->tpl('系统安装')->className('text-xl')
                        ])->className('max-w-5xl mx-auto')->alignItems('center')->justify('space-between')
                    )->className('hd'),
                    shopwwiAmis('wrapper')->body(
                        shopwwiAmis('wizard')->steps([

                            ['title'=>'安装协议','body'=>shopwwiAmis('html')->html(<<<HTML
<h1>ShopWWI智能管理系统安装协议</h1>
      <p>感谢您选择ShopWWI智能管理系统。本系统是无锡豚豹科技有限公司自主开发、独立拥有版权、集快速构建管理于一体的智能建站解决方案。官方网址为 http://www.shopwwi.com。</p>
      <p>用户须知：本协议是您与无锡豚豹科技有限公司之间关于您安装使用无锡豚豹科技有限公司提供的ShopWWI智能管理系统及服务的法律协议。无论您是个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议。请您审阅并接受或不接受本协议条款。如您不同意本协议条款或豚豹科技公司随时对其的修改，您应不使用或主动取消豚豹科技公司提供的ShopWWI智能管理系统。否则，您的任何对ShopWWI智能管理系统使用的行为将被视为您对本服务条款全部的完全接受，包括接受豚豹科技对服务条款随时所做的任何修改。</p>
      <p>本服务条款一旦发生变更，豚豹科技公司将在网页上公布修改内容。修改后的服务条款一旦在网页上公布即有效代替原来的服务条款。如果您选择接受本条款，即表示您同意接受协议各项条件的约束。如果您不同意本服务条款，则不能获得使用本服务的权利。您若有违反本条款规定，豚豹科技公司有权随时中止或终止您对ShopWWI智能管理系统的使用资格并保留追究相关法律责任的权利。</p>
      <p>在理解、同意、并遵守本协议的全部条款后，方可开始使用ShopWWI智能管理系统。您可能与豚豹科技公司直接签订另一书面协议，以补充或者取代本协议的全部或者任何部分。</p>
      <p>豚豹科技拥有本软件的全部知识产权。本软件只供许可协议，并非出售。豚豹科技只允许您在遵守本协议各项条款的情况下复制、下载、安装、使用或者以其他方式受益于本软件的功能或者知识产权。</p>
      <h3>I. 协议许可的权利</h3>
      <ol>
        <li>您可以在完全遵守本许可协议的基础上，将本软件应用于非商业用途，而不必支付软件版权许可费用。</li>
        <li>您可以在协议规定的约束和限制范围内修改ShopWWI智能管理系统源代码(如果被提供的话)或界面风格以适应您的网站要求。</li>
        <li>您拥有使用本软件构建的网站中全部会员资料、文章、商品及相关信息的所有权，并独立承担与使用本软件构建的网站内容的审核、注意义务，确保其不侵犯任何人的合法权益，独立承担因使用ShopWWI智能管理系统和服务带来的全部责任，若造成豚豹科技公司或用户损失的，您应予以全部赔偿。</li>
        <li>本协议是您与豚豹科技公司之间关于您安装使用豚豹科技公司提供的ShopWWI智能管理系统及服务的法律协议，若您需将ShopWWI智能管理系统或服务用于商业用途，必须另行获得豚豹科技的授权许可，您在获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持期限、技术支持方式和技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权利，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。</li>
      </ol>
      <p></p>
      <h3>II. 协议规定的约束和限制</h3>
      <ol>
        <li>未获豚豹科技公司商业授权之前，不得将本软件用于商业用途（包括但不限于企业网站、经营性网站、以营利为目或实现盈利的网站）。购买商业授权请登录http://www.shopwwi.com参考相关说明。</li>
        <li>不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。</li>
        <li>无论用途如何、是否经过修改或美化、修改程度如何，只要使用ShopWWI智能管理系统的整体或任何部分，未经授权许可，页面页脚处的ShopWWI智能管理系统的版权信息都必须保留，而不能清除或修改。</li>
        <li>禁止在ShopWWI智能管理系统的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</li>
        <li>如果您未能遵守本协议的条款，您的授权将被终止，所许可的权利将被收回，同时您应承担相应法律责任。</li>
      </ol>
      <p></p>
      <h3>III. 有限担保和免责声明</h3>
      <ol>
        <li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>
        <li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>
        <li>豚豹科技公司不对使用本软件构建的平台中的会员、商品或文章信息承担责任，全部责任由您自行承担。</li>
        <li>豚豹科技公司对提供的软件和服务之及时性、安全性、准确性不作担保，由于不可抗力因素、豚豹科技公司无法控制的因素（包括黑客攻击、停断电等）等造成软件使用和服务中止或终止，而给您造成损失的，您同意放弃追究豚豹科技公司责任的全部权利。</li>
        <li>豚豹科技公司特别提请您注意，豚豹科技公司为了保障公司业务发展和调整的自主权，豚豹科技公司拥有随时经或未经事先通知而修改服务内容、中止或终止部分或全部软件使用和服务的权利，修改会公布于豚豹科技公司网站相关页面上，一经公布视为通知。豚豹科技公司行使修改或中止、终止部分或全部软件使用和服务的权利而造成损失的，豚豹科技公司不需对您或任何第三方负责。</li>
      </ol>
      <p></p>
      <p>有关ShopWWI智能管理系统最终用户授权协议、商业授权与技术服务的详细内容，均由豚豹科技公司独家提供。豚豹科技公司拥有在不事先通知的情况下，修改授权协议和服务价目表的权利，修改后的协议或价目表对自改变之日起的新授权用户生效。</p>
      <p>一旦您开始安装ShopWWI智能管理系统，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权利的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权利。</p>
      <p></p>
      <p align="right">无锡豚豹科技有限公司</p>
HTML
                            )],
                            ['title'=>'创建数据库','body'=>[
                                shopwwiAmis('alert')->body('请认真填写数据库信息，数据库信息不正确将影响安装！'),
                                shopwwiAmis('input-text')->name('host')->label('数据库地址')->value('127.0.0.1')->required(true),
                                shopwwiAmis('input-text')->name('port')->label('数据库端口')->value('3306')->required(true),
                                shopwwiAmis('input-text')->name('database')->label('数据库名')->value('shopwwi')->required(true),
                                shopwwiAmis('input-text')->name('username')->label('数据库用户名')->value('root')->required(true),
                                shopwwiAmis('input-password')->name('password')->label('数据库密码')->required(true),
                            ],'mode'=>'horizontal','api'=>$request->url().'/check'],
                            ['title'=>'导入数据','body'=>[
                                shopwwiAmis('alert')->body('系统将导入初始化数据，根据服务器的配置不同，所需时间也不同，请耐心等待！<br/>开启强覆盖，将重置所有数据表！<br/>如果你的数据库存在数据，请先完成备份在点下一步'),
                                shopwwiAmis('input-text')->name('admin_name')->label('管理员账号')->value('admin')->required(true),
                                shopwwiAmis('input-password')->name('admin_password')->label('管理员密码')->required(true),
                                shopwwiAmis('switch')->name('is_drop')->label('强覆盖')->trueValue(1)->falseValue(0)->value(0)->required(true),

                            ],'mode'=>'horizontal','api'=>$request->url()],
                            ['title'=>'安装完成', 'body'=>[
                                shopwwiAmis('tpl')->className('text-xl flex items-center justify-center')->tpl('恭喜你！安装成功'),
                                shopwwiAmis('flex')->className('py-10')->items([
                                    shopwwiAmis('button')->label('访问管理中心')->actionType('url')->url(shopwwiAdminUrl(''))->level('primary')->className('mr-4'),
                                    shopwwiAmis('button')->label('访问会员中心')->actionType('url')->url(shopwwiUserUrl(''))->level('primary')
                                ])
                            ]
                            ],
                        ])
                    )->className('max-w-5xl mx-auto must mt-20 px-4 bg-white'),
                    shopwwiAmis('flex')->alignItems('center')->items('Shopwwi 智能管理系统')->className('py-4')
                ]
            );
        }
        return view('admin/install',['json'=>$page],'');
    }

    /**
     * 验证数据库信息
     * @param Request $request
     * @return \support\Response
     */
    public function check(Request $request){
        $validator = Validator::make($request->all(), [
            'host' => 'required',
            'port' => 'required|bail|numeric|min:0|max:99999',
            'database' => 'required',
            'username' => 'required',
            'password' => 'required'
        ], [], [
            'host' => '主机名',
            'port' => '端口号',
            'database' => '数据库名',
            'username' => '数据库用户名',
            'password' => '数据库密码'
        ]);
        if ($validator->fails()) {
            return shopwwiValidator('数据验证失败',$validator->errors());
        }

        try {
            $params = shopwwiParams(['host','port','database','username','password'],null,true);
            $database_config_file = base_path() . '/config/database.php';
            clearstatcache();

            $db = $this->getPdo($params->host, $params->username, $params->password, $params->port);
            $smt = $db->query("show databases like '$params->database'");
            if (empty($smt->fetchAll())) {
                $db->exec("create database $params->database");
            }
            $db->exec("use $params->database");

            $config_content = <<<EOF
<?php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '$params->host',
            'port'        => '$params->port',
            'database'    => '$params->database',
            'username'    => '$params->username',
            'password'    => '$params->password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
EOF;

            file_put_contents($database_config_file, $config_content);
            // 尝试reload
            if (function_exists('posix_kill')) {
                set_error_handler(function () {});
                posix_kill(posix_getppid(), SIGUSR1);
                restore_error_handler();
            }
            return shopwwiSuccess([],'链接数据库成功');
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    /**
     * 提交数据
     * @param Request $request
     * @return \support\Response
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'host' => 'required',
            'port' => 'required|bail|numeric|min:0|max:99999',
            'database' => 'required',
            'username' => 'required',
            'password' => 'required',
            'is_drop' => 'required|numeric|in:0,1',
            'admin_name' => 'required',
            'admin_password' => 'required|bail|chs_dash_pwd|min:6'
        ], [], [
            'host' => '主机名',
            'port' => '端口号',
            'database' => '数据库名',
            'username' => '数据库用户名',
            'password' => '数据库密码',
            'is_drop' => '强覆盖',
            'admin_name' => '管理员账号',
            'admin_password' => '管理员密码'
        ]);
        if ($validator->fails()) {
            return shopwwiValidator('数据验证失败',$validator->errors());
        }
        try {
            if($this->checkIsInstall()){ // 已经安装过了
                throw new \Exception('系统已经安装过了，如果要重新安装，那么请删除public目录下的lock文件');
            }
            $params = shopwwiParams(['host','port','database','username','password','admin_name','admin_password','is_drop'=>0],null,true);

            // 写入数据表
            if($params->is_drop > 0){ //开启强覆盖
                InstallService::DropTable();
            }
            InstallService::CreateTable();
            // 写入管理员信息
            SysUser::create([
                'username' => $params->admin_name,
                'nickname' => '管理员',
                'password' => $params->admin_password,
                'role_id' => 1
            ]);
            // 写入初始数据
            InstallService::Seeders();

            //新增一个标识文件，用来屏蔽重新安装
            $fp = @fopen(public_path().'/lock','wb+');
            @fclose($fp);

            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

    protected function checkIsInstall(){
        clearstatcache();
        return is_file($menu_file = public_path(). '/lock');
    }

    /**
     * 获取pdo连接
     * @param $host
     * @param $username
     * @param $password
     * @param $port
     * @param $database
     * @return \PDO
     */
    protected function getPdo($host, $username, $password, $port, $database = null)
    {
        $dsn = "mysql:host=$host;port=$port;";
        if ($database) {
            $dsn .= "dbname=$database";
        }
        $params = [
            \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4",
            \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_TIMEOUT => 5,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        return new \PDO($dsn, $username, $password, $params);
    }
}