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
namespace Shopwwi\Admin\App\Admin\Service;

use Shopwwi\Admin\App\Admin\Models\SysOperLog;
use Shopwwi\Admin\App\Admin\Models\SysRoleMenu;
use Shopwwi\WebmanAuth\Facade\Auth;

class AdminService
{
    /**
     * 登入鉴权
     * @param $controller
     * @param $action
     * @param $code
     * @param $msg
     * @return bool
     * @throws \ReflectionException
     */
    public static function loginSuccess($controller, $action, &$code = 0, &$msg = '')
    {
        // 获取控制器鉴权信息
        $class = new \ReflectionClass($controller);
        $properties = $class->getDefaultProperties();
        $noNeedLogin = $properties['noNeedLogin'] ?? [];
        $noNeedAuth = $properties['noNeedAuth'] ?? [];

        // 不需要登录
        if (in_array($action, $noNeedLogin)) {
            return true;
        }

        // 获取登录信息
        $admin = Auth::guard('admin')->fail(false)->user(true);
        if (!$admin) {
            $msg = '请登录';
            $code = 401;
            return false;
        }
        // 不需要鉴权
        if (in_array($action, $noNeedAuth)) {
            return true;
        }

        // 当前管理员无角色
        $roleId = $admin->role_id;
        if (empty($roleId)) {
            $msg = '无权限';
            $code = 403;
            return false;
        }
        // 超级管理员
        if($roleId === 1){
            return true;
        }

        // 角色没有规则
        $menuIds = SysRoleMenu::where('role_id', $roleId)->pluck('menu_id');
        if (!$menuIds) {
            $msg = '无权限';
            $code = 403;
            return false;
        }

        // 没有当前控制器的规则
        $rule = self::hasMenusIn($controller,$action,$menuIds);
        if (!$rule) {
            $msg = '无权限';
            $code = 403;
            return false;
        }

        return true;
    }
    /**
     * @param $businessType 0其它 1新增 2编辑 3删除
     * @param $method *请求方式
     * @param $status * 状态
     * @param $title * 标题
     * @param $router * 请求地址
     * @param $userId * 用户id
     * @param string $userName * 用户名称
     * @param string $errorMsg * 错误信息
     * @param array $params * 携带参数
     * @return void
     */
    public static function addLog($businessType,  $status, $title,  $userId, $userName, array $params=[], string $errorMsg = '')
    {
        SysOperLog::create([
            'business_type' => $businessType,
            'method' => request()->action,
            'request_method' => request()->method(),
//            'user_id' => $userId,
            'name' => $userName.'('.$userId.')',
            'title' => $title,
            'url' => request()->controller,
            'param' => $params,
            'ip' => request()->getRealIp(),
            'status' => $status,
            'error_msg' => $errorMsg,
            'location' => request()->header('user-agent','')
        ]);
    }

    /**
     * 判断是否存在
     * @param $controller
     * @param $action
     * @param $menuIds
     * @return bool
     */
    public static function hasMenusIn($controller,$action,$menuIds)
    {
        $list = SysMenuService::getMenusList();
        $hasMenu = collect($list)->whereIn('id',$menuIds)->all();
        $rulePerms = [];
        foreach ($hasMenu as $item){
            if(!empty($item->perms)){
                $rulePerms = array_merge($rulePerms, explode(',', $item->perms));
            }
        }
        if (in_array($controller.'@'.$action, $rulePerms)){
            return true;
        }
        return false;
    }

    /**
     * 判断
     * @param $ids
     * @return bool
     */
    public static function clearCache($ids)
    {

    }
}