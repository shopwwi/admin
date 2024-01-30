<?php

namespace Shopwwi\Admin\App\User\Service;

use Shopwwi\Admin\Amis\AjaxAction;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;
use Shopwwi\Admin\App\User\Models\UserAddress;

class AddressService
{
    public static function getIndexAmis()
    {
        return
            shopwwiAmis('crud')->perPage(15)
            ->mode('cards')->columnsCount(3)->perPageField('limit')
            ->bulkActions()->syncLocation(false)
            ->headerToolbar([
                'bulkActions',
                shopwwiAmis('reload')->align('right'),
                self::getCreatedAmisModel()
            ])
            ->api(shopwwiUserUrl('address?_format=json'))
            ->card([
                'actions' => [self::getUpdateAmisModel(),self::bulkDeleteButton()],
                'body'=>[
                    shopwwiAmis()->name('real_name')->label('收货人'),
                    shopwwiAmis()->name('area_info')->label('所在地区'),
                    shopwwiAmis()->name('address_info')->label('地址'),
                    shopwwiAmis()->name('mobile')->label('手机号'),
                    "<% if (this.address_default == 1){ %><span class=\"label label-default\">默认</span><% } %> "
                ]
            ]);
    }

    public static function getCreatedAmisModel()
    {
        $form = self::getAmisForm()->api('post:'.shopwwiUserUrl('address'))->initApi(shopwwiUserUrl('address/create?_format=json'));
        return shopwwiAmis('button')->actionType('dialog')->dialog(
            shopwwiAmis('dialog')->body($form)->title('新增收货地址')
        )->label('新增收货地址')->icon('ri-add-line')->level('primary');
    }

    public static function getUpdateAmisModel()
    {
        $form = self::getAmisForm()->api('put:'.shopwwiUserUrl('address/${id}'))->initApi(shopwwiUserUrl('address/${id}/edit?_format=json'));
        return shopwwiAmis('button')->actionType('dialog')->dialog(
            shopwwiAmis('dialog')->body($form)->title('修改收货地址')
        )->label('修改收货地址')->icon('ri-edit-line')->level('primary');
    }

    public static function getAmisForm()
    {
        return shopwwiAmis('form')->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')
            ->mode('horizontal')->body([
                shopwwiAmis('hidden')->name('lat')->value('${location.lat}'),
                shopwwiAmis('hidden')->name('lng')->value('${location.lng}'),
                shopwwiAmis('hidden')->name('area_ids')->value('${city.provinceCode},${city.cityCode},${city.districtCode}'),
                shopwwiAmis('hidden')->name('area_id')->value('${city.code}'),
                shopwwiAmis('hidden')->name('area_info')->value('${city.province} ${city.city} ${city.district}'),
                shopwwiAmis('input-text')->name('real_name')->sm(6)->label('收货人')->placeholder('收货人真实姓名')->required(true),
                shopwwiAmis('input-city')->name('city')->sm(12)->label('所在地区')->extractValue(false),
                shopwwiAmis('input-text')->name('address_info')->sm(12)->label('详细地址')->placeholder('收货详细地址，无需填写地区')->required(true),
                shopwwiAmis('location-picker')->name('location')->sm(12)->label('定位')->placeholder('选择定位可以更好的服务')->ak('LiZT5dVbGTsPI91tFGcOlSpe5FDehpf7'),
                shopwwiAmis('input-text')->name('mobile')->sm(12)->label('手机号码')->placeholder('填写收货人手机号')->required(true),
                shopwwiAmis('input-text')->name('telphone')->sm(12)->label('电话号码')->placeholder('填写电话号码与手机号至少填一个'),
                shopwwiAmis('radios')->name('address_default')->label('默认地址')->options(DictTypeService::getAmisDictType('yesOrNo'))
            ]);
    }

    public static function bulkDeleteButton(): AjaxAction
    {
        return AjaxAction::make()
            ->api('delete:'.shopwwiUserUrl('address/${id}'))
            ->icon('ri-delete-bin-line')->level('danger')
            ->label(trans('delete',[],'messages'))
            ->confirmText(trans('confirmDelete',[],'messages'));
    }

    /**
     * 获取用户地址信息
     * @param $user
     * @return array
     */
    public static function getUserAddressInfo($user){
        $addressList = UserAddress::where('user_id',$user->id)->orderBy('address_default','desc')->orderBy('id','desc')->get();
        $realName = self::getDefaultUserRealName($addressList);
        $mobile = self::getDefaultUserMobile($addressList);
        $defaultAddressId = self::getDefaultAddressId($addressList);
        $address = $addressList->where('id',$defaultAddressId)->first();
        return [
            'realName'=>empty($realName)?$user->true_name:$realName,
            'mobile'=>empty($mobile)?$user->phone:$mobile,
            'addressList' => $addressList,
            'address'=>$address,
            'defaultAddressId'=>$defaultAddressId];
    }

    /**
     * 取得默认收货地址Id
     * @param $addressList
     * @return int|mixed
     */
    public static function getDefaultAddressId($addressList){
        $addressId = 0;
        if ($addressList->count() > 0) {
            foreach ($addressList as $address){
                if ($address->address_default == 1){
                    $addressId = $address->id;
                    break;
                }
            }
            if($addressId == 0){
                $addressId = $addressList->first()->id;
            }
        }
        return $addressId;
    }

    /**
     * 取得默认收货地址中的手机
     * @param $addressList
     * @return mixed|null
     */
    public static function getDefaultUserMobile($addressList){
        $mobile = null;
        if ($addressList->count() > 0) {
            foreach ($addressList as $address){
                if ($address->address_default == 1){
                    $mobile = $address->mobile;
                    break;
                }
            }
            if($mobile == null){
                $mobile = $addressList->first()->mobile;
            }
        }
        return $mobile;
    }

    /**
     * 获取会员真实名称
     * @param $addressList
     * @return mixed|null
     */
    public static function getDefaultUserRealName($addressList){
        $realName = null;
        if ($addressList->count() > 0) {
            foreach ($addressList as $address){
                if ($address->address_default == 1){
                    $realName = $address->real_name;
                    break;
                }
            }
            if($realName == null){
                $realName = $addressList->first()->real_name;
            }
        }
        return $realName;
    }
}