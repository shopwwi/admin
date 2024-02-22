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
namespace Shopwwi\Admin\App\Admin\Models;

// //不开启缓存则去掉注释

class SysMenu extends Model
{

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'sys_menu';

    /**
     * 与表关联的主键
     *
     * @var string
     */
    // protected $primaryKey = 'flight_id';

    /**
     * 主键是否主动递增
     *
     * @var bool
     */
//     public $incrementing = false;

     /**
     * 自动递增主键的「类型」
     *
     * @var string
     */
//      protected $keyType = 'string';

     /**
      * 是否主动维护时间戳
      *
      * @var bool
      */
     // public $timestamps = false;

     /**
      * 模型日期的存储格式
      *
      * @var string
      */
     // protected $dateFormat = 'U';

     /**
      * 模型的数据库连接名
      *
      * @var string
      */
     // protected $connection = 'connection-name';

     /**
      * 可批量赋值属性
      *
      * @var array
      */
     // protected $fillable = [];

    /**
     * 模型属性的默认值
     *
     * @var array
     */
    protected $attributes = [
        'is_frame'=> '0',
        'is_cache'=> '0',
        'visible'=> '1',
        'status' => '1',
        'sort' => 999
    ];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * 模型的「引导」方法。
     */
    protected static function booted(): void
    {
        static::created(function (){
            // 清除菜单缓存
            \Shopwwi\Admin\App\Admin\Service\SysMenuService::clear();
        });
        static::updated(function (){
            // 清除菜单缓存
            \Shopwwi\Admin\App\Admin\Service\SysMenuService::clear();
        });
        static::deleted(function (){
            // 清除菜单缓存
            \Shopwwi\Admin\App\Admin\Service\SysMenuService::clear();
        });
    }
    public function menu()
    {
        return $this->belongsToMany(SysRole::class,'sys_role_menu','menu_id','role_id');
    }

    public function child()
    {
        return $this->hasMany(self::class,'pid','id')->with('child');
    }

    public function children()
    {
        return $this->hasMany(self::class,'pid','id')->with('children');
    }

    /**
     * 查询菜单
     * @param $menu
     * @return array|mixed
     */
    public static function getUserMenu($menu=[])
    {
        $_menu = [
            'path' => '/dashboard',
            'name' => 'Welcome',
            // 'component' => 'Layout',
            'component' => '/dashboard/analysis/index',
            'meta'=>[ 'title'=>'首页','icon'=>'ri:home-4-line','affix'=>true],
//            'children' => [
//                [
//                    'path' => 'site',
//                    'name' => 'forum-site',
//                    'component' => 'redirect',
//                    'meta'=>['title'=>'平台','icon'=>'home-4-line','noCache'=>true]
//                ]
//            ]
        ];
//        $_menu_2 = [
//            [
//                'path' => '/dashboard',
//                'name' => 'Welcome',
//                'component' => 'LAYOUT',
//                'redirect' => '/dashboard/analysis',
//                'meta'=>[ 'title'=>'首页','icon'=>'ri:home-4-line','affix'=>true],
//                'children' => [
//                    [
//                        'path' => 'analysis',
//                        'name' => 'Analysis',
//                        'component' => '/dashboard/analysis/index',
//                        'meta'=>['title'=>'平台','icon'=>'home-4-line','noCache'=>true]
//                    ]
//                ]
//            ],
//            [
//            'path' => '/forum',
//            'name' => 'Forum',
//            'component' => 'LAYOUT',
//            'meta'=>[ 'icon'=> "ri:home-gear-line" ,'title'=>"平台"],
//            'redirect' => '/forum/user/uList',
//            'children' => [
//                [
//                    'path' => 'user',
//                    'name' => 'ForumUser',
//                    'meta'=>[ 'icon'=> "ri:home-gear-line" ,'title'=>"会员"],
//                    'children' => [
//                        [
//                            'path' => 'uList',
//                            'name' => 'ForumUser',
//                            'meta'=>[ 'icon'=> "ri:home-gear-line" ,'title'=>"会员管理"],
//                            'component' => '/forum/user/userIndex',
//                        ]
//                ]
//            ]
//        ]
//        ]];
        $menu = (new SysMenu)->_getMenuChildrenRouter($menu);
        array_unshift($menu,  $_menu);
        return $menu;
    }

    /**
     * 循环菜单值
     * @param $data
     * @param int $pid
     * @param array $arr
     * @return array|mixed
     */
    private function _getMenuChildrenRouter($data,$pid=0,$path ='',$highlight='', $arr = []){
        foreach($data as $k=>$v){
            if( $v['pid'] == $pid && $v['menu_type'] != 'F'){
                $list = [
                    'path' => $v['path'],
                  //  'hidden' => $v['visible'] == 2,
                    'component' => $v['menu_type'] == 'M' ? ($v['component']?$v['component']:'LAYOUT'):$v['component'],
                    'name' => $path==''?ucfirst($v['path']):ucfirst($path).ucfirst($v['path']),
                    'meta' => [
                        'title'=>$v['name'],
                        'hideTab'=> $v['is_cache'] == 2 || $v['menu_type'] == 'M',
                        'hideMenu'=> $v['visible'] == 2 ,
                        'currentActiveMenu' =>$v['highlight']?($highlight==''?$v['highlight']:$highlight.'/'.$v['highlight']):($highlight==''?$v['highlight']:$highlight),
                        'icon' => 'ri:'.$v['icon']
                    ],
                ];
                if($list['component'] == 'LAYOUT'){
                    $list['path'] = '/'.$v['path'];
                }else{
                    $list['component'] = '/'.$list['component'];
                }
                unset($data[$k]);
                $rs = $this->_getMenuChildrenRouter($data,$v['id'],$list['name'],$list['meta']['currentActiveMenu']);
                if(!empty($rs)){
                    if($list['component'] == 'LAYOUT'){
                        //计算第一个链接
                        $path = '/'.$v['path'];
                        if($rs[0]['path']){
                            $path .= '/'.$rs[0]['path'];
                            if(collect($rs[0]['children'])->count() > 0){
                                $path .= '/'.$rs[0]['children'][0]['path'];
                            }
                        }
                        $list['redirect'] = $path;
                        unset($list['meta']['currentActiveMenu']);
                    }
                    if($list['component'] == '/redirect'){
                        unset($list['component']);
                    }
                    $list['children'] = $rs;
                  //  $list['meta']['hideTab'] = true;
                }
                $arr[] = $list;
            }
        }
        return $arr;
    }


}
