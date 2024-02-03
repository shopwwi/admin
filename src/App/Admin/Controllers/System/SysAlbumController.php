<?php
/**
 *-------------------------------------------------------------------------s*
 * 系统相册控制器
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
namespace Shopwwi\Admin\App\Admin\Controllers\System;

use Shopwwi\Admin\App\Admin\Service\AmisService;
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Shopwwi\Admin\Libraries\Amis\AdminController;
use support\Request;


class SysAlbumController extends AdminController
{
    protected $model = \Shopwwi\Admin\App\Admin\Models\SysAlbum::class;
    protected $orderBy = ['id' => 'desc'];
    protected $adminOp = true;
    protected $trans = 'sysAlbum'; // 语言文件名称
    protected $queryPath = 'system/album'; // 完整路由地址
    protected $activeKey = 'sysAlbum';
    /**
     * 路由注册
     * @var string
     */
     public $routePath = 'album'; // 当前路由模块不填写则直接控制器名
    // public $routeAction = ['index'=>['GET','POST']]; //方法注册 未填写的则直接any
    // public $routeNoAction = ['index']; //不允许方法注册
    // public $noNeedLogin = []; //不需要登入
    // public $noNeedAuth = []; //需要登入不需要鉴权
    /**
     * 数据字段处理
     * @return array
     */
    protected function fields(){
        return [
            shopwwiAmisFields(trans('field.id',[],'sysAlbum'),'id')->showOnCreation(false)->showOnUpdate(false)->rules('required'),
            shopwwiAmisFields(trans('field.name',[],'sysAlbum'),'name')->rules(['required','min:1','max:20']),
            shopwwiAmisFields(trans('field.created_user_id',[],'sysAlbum'),'created_user_id')->showOnCreation(false)->showOnUpdate(false)->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.created_at',[],'sysAlbum'),'created_at')->showOnCreation(false)->showOnUpdate(false),
            shopwwiAmisFields(trans('field.updated_user_id',[],'sysAlbum'),'updated_user_id')->showOnCreation(false)->showOnUpdate(false)->filterColumn('picker',['joinValues'=>true,'source'=>shopwwiAdminUrl('system/user/list?_format=json'),'size'=>'lg','valueField'=>'id','labelField'=>'nickname','pickerSchema'=>AmisService::getSysUserList()]),
            shopwwiAmisFields(trans('field.updated_at',[],'sysAlbum'),'updated_at')->showOnCreation(false)->showOnUpdate(false)
        ];
    }

    public function setting(Request $request)
    {
        try {
            if($request->method() === 'GET'){
                if($this->format() == 'json'){
                    $info = SysConfigService::getFirstOrCreate([
                        'key' => 'filesystem'
                    ],['name'=>'附件信息配置','value'=>[
                        'default' => 'public',
                        'ext_yes' => [],
                        'ext_no' => [],
                        'max_size' => 1024 * 1024 * 10, //单个文件大小10M
                        'storage' => [
                            'public' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\LocalAdapterFactory::class,
                                'root' => public_path().'/static',
                                'url' => '//127.0.0.1:8787/static' // 静态文件访问域名
                            ],
                            'local' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\LocalAdapterFactory::class,
                                'root' => runtime_path(),
                                'url' => '//127.0.0.1:8787' // 静态文件访问域名
                            ],
                            'ftp' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\FtpAdapterFactory::class,
                                'host' => 'ftp.example.com',
                                'username' => 'username',
                                'password' => 'password',
                                'url' => '' // 静态文件访问域名
                                // 'port' => 21,
                                // 'root' => '/path/to/root',
                                // 'passive' => true,
                                // 'ssl' => true,
                                // 'timeout' => 30,
                                // 'ignorePassiveAddress' => false,
                                // 'timestampsOnUnixListingsEnabled' => true,
                            ],
                            'memory' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\MemoryAdapterFactory::class,
                            ],
                            's3' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\S3AdapterFactory::class,
                                'credentials' => [
                                    'key' => 'S3_KEY',
                                    'secret' => 'S3_SECRET',
                                ],
                                'region' => 'S3_REGION',
                                'version' => 'latest',
                                'bucket_endpoint' => false,
                                'use_path_style_endpoint' => false,
                                'endpoint' => 'S3_ENDPOINT',
                                'bucket_name' => 'S3_BUCKET',
                                'url' => '' // 静态文件访问域名
                            ],
                            'minio' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\S3AdapterFactory::class,
                                'credentials' => [
                                    'key' => 'S3_KEY',
                                    'secret' => 'S3_SECRET',
                                ],
                                'region' => 'S3_REGION',
                                'version' => 'latest',
                                'bucket_endpoint' => false,
                                'use_path_style_endpoint' => true,
                                'endpoint' => 'S3_ENDPOINT',
                                'bucket_name' => 'S3_BUCKET',
                                'url' => '' // 静态文件访问域名
                            ],
                            'oss' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\AliyunOssAdapterFactory::class,
                                'accessId' => 'OSS_ACCESS_ID',
                                'accessSecret' => 'OSS_ACCESS_SECRET',
                                'bucket' => 'OSS_BUCKET',
                                'endpoint' => 'OSS_ENDPOINT',
                                'url' => '' // 静态文件访问域名
                                // 'timeout' => 3600,
                                // 'connectTimeout' => 10,
                                // 'isCName' => false,
                                // 'token' => null,
                                // 'proxy' => null,
                            ],
                            'qiniu' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\QiniuAdapterFactory::class,
                                'accessKey' => 'QINIU_ACCESS_KEY',
                                'secretKey' => 'QINIU_SECRET_KEY',
                                'bucket' => 'QINIU_BUCKET',
                                'domain' => 'QINBIU_DOMAIN',
                                'url' => '' // 静态文件访问域名
                            ],
                            'cos' => [
                                'driver' => \Shopwwi\WebmanFilesystem\Adapter\CosAdapterFactory::class,
                                'region' => 'COS_REGION',
                                'app_id' => 'COS_APPID',
                                'secret_id' => 'COS_SECRET_ID',
                                'secret_key' => 'COS_SECRET_KEY',
                                // 可选，如果 bucket 为私有访问请打开此项
                                // 'signed_url' => false,
                                'bucket' => 'COS_BUCKET',
                                'read_from_cdn' => false,
                                'url' => '' // 静态文件访问域名
                                // 'timeout' => 60,
                                // 'connect_timeout' => 60,
                                // 'cdn' => '',
                                // 'scheme' => 'https',
                            ],
                        ]
                    ]]);
                    return shopwwiSuccess($info);
                }

                $form = $this->baseForm()->body([
                    shopwwiAmis('grid')->gap('lg')->columns([
                        shopwwiAmis('input-text')->name('filesystem.default')->label(trans('filesystem.default',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('filesystem.default',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-text')->name('filesystem.max_size')->label(trans('filesystem.max_size',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('filesystem.max_size',[],$this->trans)],'messages'))->xs(12),
                        shopwwiAmis('input-array')->name('filesystem.ext_yes')->label(trans('filesystem.ext_yes',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('filesystem.ext_yes',[],$this->trans)],'messages'))->xs(12)->items([
                            'type' => 'input-text'
                        ]),
                        shopwwiAmis('input-array')->name('filesystem.ext_no')->label(trans('filesystem.ext_no',[],$this->trans))->placeholder(trans('form.input',['attribute'=>trans('filesystem.ext_no',[],$this->trans)],'messages'))->xs(12)->items([
                            'type' => 'input-text'
                        ]),
                        shopwwiAmis('json-schema')->label(trans('filesystem.storage',[],$this->trans))->name('filesystem.storage')->xs(12)->schema(
                            shopwwiAmis('object')->properties([
                                'public' => shopwwiAmis('object')->title('本地(公共)')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                    'root' => shopwwiAmis('string')->title('存储路径'),
                                    'url' => shopwwiAmis('string')->title('访问地址'),
                                ]),
                                'local' => shopwwiAmis('object')->title('本地(私有)')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                    'root' => shopwwiAmis('string')->title('存储路径'),
                                    'url' => shopwwiAmis('string')->title('访问地址'),
                                ]),
                                'ftp' => shopwwiAmis('object')->title('FTP')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                    'host' => shopwwiAmis('string')->title('host'),
                                    'username' => shopwwiAmis('string')->title('username'),
                                    'password' => shopwwiAmis('string')->title('password'),
                                    'url' => shopwwiAmis('string')->title('访问地址'),
                                ]),
                                'oss' => shopwwiAmis('object')->title('阿里云')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                    'accessId' => shopwwiAmis('string')->title('accessId'),
                                    'accessSecret' => shopwwiAmis('string')->title('accessSecret'),
                                    'bucket' => shopwwiAmis('string')->title('bucket'),
                                    'endpoint' => shopwwiAmis('string')->title('endpoint'),
                                    'url' => shopwwiAmis('string')->title('访问地址'),
                                ]),
                                'qiniu' => shopwwiAmis('object')->title('七牛云')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                    'accessKey' => shopwwiAmis('string')->title('accessId'),
                                    'secretKey' => shopwwiAmis('string')->title('accessSecret'),
                                    'bucket' => shopwwiAmis('string')->title('bucket'),
                                    'domain' => shopwwiAmis('string')->title('domain'),
                                    'url' => shopwwiAmis('string')->title('访问地址'),
                                ]),
                                'cos' => shopwwiAmis('object')->title('腾讯云')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                    'region' => shopwwiAmis('string')->title('region'),
                                    'app_id' => shopwwiAmis('string')->title('app_id'),
                                    'secret_id' => shopwwiAmis('string')->title('secret_id'),
                                    'bucket' => shopwwiAmis('string')->title('bucket'),
                                    'read_from_cdn' => shopwwiAmis('string')->title('read_from_cdn'),
                                    'url' => shopwwiAmis('string')->title('访问地址'),
                                ]),
                                's3' => shopwwiAmis('object')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                    'credentials' => shopwwiAmis('object'),
                                ]),
                                'memory' => shopwwiAmis('object')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                ]),
                                'minio' => shopwwiAmis('object')->properties([
                                    'driver' => shopwwiAmis('string')->title('选定器'),
                                    'credentials' => shopwwiAmis('object'),
                                ]),
                            ])
                        )
                    ])
                ])->api('post:' . shopwwiAdminUrl('system/album/setting'))
                    ->actions([
                        shopwwiAmis('reset')->label(trans('reset',[],'messages')),
                        shopwwiAmis('submit')->label(trans('submit',[],'messages'))->level('primary')
                    ])
                    ->initApi(shopwwiAdminUrl('system/album/setting?_format=json'));
                $page = $this->basePage()->body($form);
                $page = $page->subTitle('附件设置');
                if($this->format() == 'data'||$this->format() == 'web') return shopwwiSuccess($page);
                return $this->getAdminView(['json'=>$page,'activeKey'=>'settingSiteAlbumSite']);
            }else{
                $params = shopwwiParams(['filesystem']);
                if(is_string($params['filesystem']['storage'])){
                    $params['filesystem']['storage'] = json_decode($params['filesystem']['storage']);
                }
                SysConfigService::updateSetting($params);
            }
            return shopwwiSuccess();
        }catch (\Exception $e){
            return shopwwiError($e->getMessage());
        }
    }

}
