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
 */


namespace Shopwwi\Admin;


use Shopwwi\Admin\Libraries\Appoint;

class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * @var array
     */
    protected static $pathRelation = array (
        'Install/config/plugin/shopwwi/admin' => 'config/plugin/shopwwi/admin',
        'Install/public' => 'public',
        'Install/translations' => 'resource/translations',
        'Install/view' => 'app/view',
        'Install/queue' => 'app/queue'
    );

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        // 写入默认auth信息
//        Appoint::replaceStringInFiles(base_path() . '/config/plugin/shopwwi/auth/app.php',"'guard'",<<<EOF
//         'member' => [
//             'key' => 'id',
//             'field' => ['id','username','email','mobile','nickname','avatar','avatarUrl'], //设置允许写入扩展中的字段
//             'num' => -1, //-1为不限制终端数量 0为只支持一个终端在线 大于0为同一账号同终端支持数量 建议设置为1 则同一账号同终端在线1个
//             'model'=> \Shopwwi\Admin\App\User\Models\Users::class
//         ],
//         'admin' => [
//             'key' => 'id',
//             'field' => ['id','username','role_id','sector_ids','nickname','avatar','status','login_ip','login_time','login_num'],
//             'num' => -1, //-1为不限制终端数量 0为只支持一个终端在线 大于0为同一账号同终端支持数量 建议设置为1 则同一账号同终端在线1个
//             'model'=> \Shopwwi\Admin\App\Admin\Models\SysUser::class
//         ],
//EOF
//        );
        static::installByRelation();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        self::uninstallByRelation();
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation()
    {
        $view_config = <<<EOF
<?php
return [
    'handler' => \\support\\view\\Blade::class
];

EOF;
        file_put_contents(base_path() . '/config/view.php', $view_config);

        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path().'/'.substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            if($dest === 'public'){
                copy_dir(__DIR__ . "/$source", base_path()."/$dest",true);
            }else{
                copy_dir(__DIR__ . "/$source", base_path()."/$dest");
            }

        }
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation()
    {
        foreach (static::$pathRelation as $source => $dest) {
            $path = base_path()."/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            /*if (is_link($path) {
                unlink($path);
            }*/
            remove_dir($path);
        }
    }

}