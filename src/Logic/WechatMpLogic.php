<?php
/**
 *-------------------------------------------------------------------------s*
 * 微信公众号
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

namespace Shopwwi\Admin\Logic;

use EasyWeChat\OfficialAccount\Application;

class WechatMpLogic
{
    protected static $_instance = null;

    public static function instance()
    {
        if (!static::$_instance) {
            $config = [
                'app_id' => 'wx0910e2c67718b730',
                'secret' => '329c43d93bd78cfd11bdab4a7b62e72f',
                'token' => 'YiMeiJuan6868',
                'aes_key' => '10MTemDdTvcdy0ImfwJZzMeCeUI6iA30PjYVsMeRrtN' // 明文模式请勿填写 EncodingAESKey
                //...
            ];
            static::$_instance = new Application($config);
        }
        return static::$_instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(... $arguments);
    }

    /**
     * 创建菜单
     * @return void
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public static function menuCreate()
    {
        $api = static::getClient();
        try {
           $res = $api->postJson('/cgi-bin/menu/create',[]);

        }catch (\Exception $e){

        }
    }

    public static function addMaterialImage()
    {
        
    }

    /**
     * 加载会员标签列表
     */
    public static function loadUserTagList()
    {
        try {
          //  $app = self::getConfig();
            $api = static::getClient();
            $res = $api->get('/cgi-bin/tags/get');
        }catch (\Exception $e){

        }
    }

    /**
     * 新增标签
     */
    public static function addTag($name)
    {
        try {
            $api = static::getClient();
            $res = $api->postJson('/cgi-bin/tags/create',[
                'tag'=>['name'=>$name]
            ]);
        }catch (\Exception $e){

        }
    }

    /**
     * 删除标签
     * @param $id
     */
    public static function delTag($id)
    {
        try {
            $api = static::getClient();
            $res = $api->postJson('/cgi-bin/tags/delete',[
                'tag'=>['id'=>$id]
            ]);
        }catch (\Exception $e){

        }
    }

    /**
     * 加载用户列表
     */
    public static function loadUserList()
    {

        try {
            $api = static::getClient();
            // next_openid = NEXT_OPENID
            $res = $api->get('/cgi-bin/user/get',[]);
        }catch (\Exception $e){

        }
    }

    public static function setUserRemark($openId,$remark)
    {
        try {
            $api = static::getClient();
            // next_openid = NEXT_OPENID
            $res = $api->postJson('/cgi-bin/user/info/updateremark',[
                'openid' => $openId,
                'remark' => $remark
            ]);
        }catch (\Exception $e){

        }
    }

    /**
     * 加载黑名单
     */
    public static function loadUserBlackList()
    {

        try {
            $api = static::getClient();
            // next_openid = NEXT_OPENID
            $res = $api->get('/cgi-bin/tags/members/getblacklist?next_openid=');

        }catch (\Exception $e){

        }
    }

    public static function laodTemplateList()
    {
        try {
            $api = static::getClient();
            // next_openid = NEXT_OPENID
            $res = $api->get('/cgi-bin/template/get_all_private_template');

        }catch (\Exception $e){

        }
    }

    /**
     * 返回消息事件
     */
    public static function relate()
    {
//        $app->rebind('request', $request);
        $server = static::getServer();
        return $server->serve();
    }

}