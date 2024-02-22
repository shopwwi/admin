<?php
/**
 *-------------------------------------------------------------------------s*
 * AMIS
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2024 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 * @author     8988354@qq.com TycoonSong
 *-------------------------------------------------------------------------i*
 */
namespace Shopwwi\Admin\App\Admin\Service;

use Shopwwi\Admin\App\Admin\Models\SysRole;
use Shopwwi\Admin\App\Admin\Models\SysUserSector;
use Shopwwi\Admin\Libraries\Appoint;

class AmisService
{
    /**
     * 获取用户列表信息
     * @param $admin
     * @return array
     */
    public static function getSysUserList($admin = null)
    {
        if(!empty($admin)){
            $list = SysSectorService::getUseList($admin);
            $diffPid = array_diff($list->pluck('pid')->all(),$list->pluck('id')->all());
            $sectorList = Appoint::ShopwwiChindNode(json_decode($list->toJson(),true),$diffPid[0]??0);
        }else{
            $list = SysSectorService::getList();
            $sectorList = Appoint::ShopwwiChindNode(json_decode($list->toJson(),true));
        }

        $roleList = SysRole::where('status',1)->get();
        return [
            'mode' => 'table',
            'name' => 'sysUserList',
            'autoGenerateFilter' => ['columnsNum'=>3,'showBtnToolbar'=>false],
            'columns' => [
                shopwwiAmis('text')->name('id')->label(trans('field.id',[],'messages'))
                    ->searchable(shopwwiAmis('input-text')->name('id_like')->label(trans('field.id',[],'messages'))->placeholder('输入编号查询')),
                shopwwiAmis('text')->name('nickname')->label(trans('field.nickname',[],'sysUser'))->searchable(shopwwiAmis('input-text')->name('nickname')->label('昵称')->placeholder('输入昵称查询')),
                shopwwiAmis('text')->name('username')->label(trans('field.username',[],'sysUser'))->searchable(shopwwiAmis('input-text')->name('username_like')->label('账号')->placeholder('输入账号查询')),
                shopwwiAmis('text')->name('mobile')->label(trans('field.mobile',[],'sysUser'))->searchable(shopwwiAmis('input-text')->name('mobile_like')->label('手机号')->placeholder('输入手机号查询')),
                shopwwiAmis('text')->name('email')->label(trans('field.email',[],'sysUser'))->searchable(shopwwiAmis('input-text')->name('email_like')->label('邮箱')->placeholder('输入邮箱查询')),
                shopwwiAmis('select')->name('role_id')->label(trans('field.role_id',[],'sysUser'))->options($roleList)->labelField('name')->valueField('id')->static(true)->searchable(false),
                shopwwiAmis('tree-select')->name('sector_ids')->label(trans('field.sector_ids',[],'sysUser'))
                    ->options($sectorList)->labelField('name')->valueField('id')->multiple(true)->static(true)
                    ->searchable(shopwwiAmis('tree-select')->name('sector_ids_likeId')->options($sectorList)->labelField('name')->valueField('id')->placeholder('选择部门查询')),
                shopwwiAmis('text')->name('created_at')->label(trans('field.created_at',[],'messages')),
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getUserList()
    {
        $allowOrUnAllow = DictTypeService::getAmisDictType('allowOrUnAllow');
        return [
            'mode' => 'table',
            'name' => 'userAmisList',
            'autoGenerateFilter' => ['columnsNum'=>3,'showBtnToolbar'=>false],
            'columns' => [
                shopwwiAmis('text')->name('id')->label(trans('field.id',[],'messages'))->sortable(true)->searchable(shopwwiAmis('input-text')->name('id_like')->label(trans('field.id',[],'messages'))->placeholder('输入编号查询')),
                shopwwiAmis('text')->name('nickname')->label(trans('field.nickname',[],'users'))->searchable(shopwwiAmis('input-text')->name('nickname_like')->label(trans('field.nickname',[],'users'))->placeholder('输入昵称查询')),
                shopwwiAmis('text')->name('phone')->label(trans('field.phone',[],'users'))->sortable(true)->searchable(shopwwiAmis('input-text')->name('phone_like')->label(trans('field.phone',[],'users'))->placeholder('输入手机号查询')),
                shopwwiAmis('text')->name('growth')->label(trans('field.growth',[],'users'))->sortable(true),
                shopwwiAmis('text')->name('points')->label(trans('field.points',[],'users'))->sortable(true),
                shopwwiAmis('text')->name('available_points')->label(trans('field.available_points',[],'users'))->sortable(true),
                shopwwiAmis('text')->name('frozen_points')->label(trans('field.frozen_points',[],'users'))->sortable(true),
                shopwwiAmis('text')->name('balance')->label(trans('field.balance',[],'users'))->sortable(true),
                shopwwiAmis('text')->name('available_balance')->label(trans('field.available_balance',[],'users'))->sortable(true),
                shopwwiAmis('text')->name('frozen_balance')->label(trans('field.frozen_balance',[],'users'))->sortable(true),
                shopwwiAmis('mapping')->name('status')->label(trans('field.status',[],'users'))->sortable(true)
                    ->map(self::toMappingSelect($allowOrUnAllow,'${status}'))
                    ->searchable(shopwwiAmis('select')->options($allowOrUnAllow)),
            ]
        ];
    }

    public static function getGradeList(){
        return [
            'mode' => 'table',
            'name' => 'userAmisGradeList',
            'autoGenerateFilter' => ['columnsNum'=>3,'showBtnToolbar'=>false],
            'columns' => [
                shopwwiAmis('text')->name('id')->label(trans('field.id',[],'messages'))->sortable(true)->searchable(shopwwiAmis('input-text')->name('id_like')->label(trans('field.id',[],'messages'))->placeholder('输入编号查询')),
                shopwwiAmis('text')->name('name')->label(trans('field.name',[],'userGrade'))->searchable(shopwwiAmis('input-text')->name('name_like')->label(trans('field.name',[],'userGrade'))->placeholder('输入等级名称查询')),
                shopwwiAmis('text')->name('ext_name')->label(trans('field.ext_name',[],'userGrade'))->sortable(true),
                shopwwiAmis('text')->name('level')->label(trans('field.level',[],'userGrade'))->sortable(true),
                shopwwiAmis('tpl')->name('group_id')->tpl('${group_name}(ID:${group_id})')->label(trans('field.group_id',[],'userGrade'))->sortable(true),
            ]
        ];
    }

    public static function toMappingSelect($data,$type = '',$show = 'label'){
        $new = [];
        foreach ($data as $val){
            switch ($show){
                case 'label':
                    $new[$val->value] = '<span class="label label-'.$val->list_class.'">'.$val->label.'</span>';
                    break;
                case 'text':
                    $new[$val->value] = '<span class="text-'.$val->list_class.'">'.$val->label.'</span>';
                    break;
                case 'round':
                    $new[$val->value] = "<span class='label rounded-full border border-solid border-$val->list_class text-$val->list_class'>$val->label</span>";
                    break;
                case 'default':
                    $new[$val->value] = '<span class="cxd-Tag">'.$val->label.'</span>';
                    break;
                default:
                    $new[$val->value] = '<span>'.$val->label.'</span>';
                    break;
            }
        }
        $new['*'] = $type;
        return $new;
    }
}