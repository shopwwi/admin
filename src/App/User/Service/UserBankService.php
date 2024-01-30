<?php

namespace Shopwwi\Admin\App\User\Service;

use Shopwwi\Admin\Amis\AjaxAction;
use Shopwwi\Admin\Amis\DropdownButton;
use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\App\Admin\Service\DictTypeService;

class UserBankService
{
    /**
     * 获取首页Amis
     * @return mixed
     */
    public static function getIndexAmis()
    {
        return
            shopwwiAmis('crud')->perPage(15)
                ->perPageField('limit')
                ->bulkActions()->syncLocation(false)
                ->headerToolbar([
                    'bulkActions',
                    shopwwiAmis('reload')->align('right'),
                    self::getCreateAmis()
                ])
                ->api(shopwwiUserUrl('bank?_format=json'))
                ->columns([
                    shopwwiAmis()->name('bank_name')->label(trans('field.bank_name',[],'userCard')),
                    shopwwiAmis()->name('bank_account')->label(trans('field.bank_account',[],'userCard')),
                    shopwwiAmis()->name('bank_username')->label(trans('field.bank_username',[],'userCard')),
                    shopwwiAmis()->name('bank_branch')->label(trans('field.bank_branch',[],'userCard')),
                    shopwwiAmis()->name('mobile')->label(trans('field.mobile',[],'userCard')),
                    Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(145)->buttons([
                        self::bulkDeleteButton()
                    ])
                ]);
    }

    public static function getCreateAmis()
    {
        $form = self::getAmisForm()->api('post:'.shopwwiUserUrl('bank'));
        return shopwwiAmis('button')->actionType('dialog')->dialog(
            shopwwiAmis('dialog')->body($form)->title('绑定银行卡')
        )->label('绑定银行卡')->icon('ri-add-circle-line')->level('primary');
    }

    public static function getAmisForm()
    {
        return shopwwiAmis('form')->panelClassName('must px-40 py-10 m:px-0 border-0 box-shadow-none')
            ->mode('horizontal')->body([
                shopwwiAmis('select')->name('bank_type')->options(DictTypeService::getAmisDictType('userCardType'))->label(trans('field.bank_type',[],'userCard'))->required(true),
                shopwwiAmis('input-text')->name('bank_name')->sm(6)->label(trans('field.bank_name',[],'userCard'))->placeholder(trans('form.input',['attribute'=>trans('field.bank_name',[],'userCard')],'messages')),
                shopwwiAmis('input-text')->name('bank_account')->sm(6)->label(trans('field.bank_account',[],'userCard'))->placeholder(trans('form.input',['attribute'=>trans('field.bank_account',[],'userCard')],'messages'))->required(true),
                shopwwiAmis('input-text')->name('bank_username')->sm(6)->label(trans('field.bank_username',[],'userCard'))->placeholder(trans('form.input',['attribute'=>trans('field.bank_username',[],'userCard')],'messages'))->required(true),
                shopwwiAmis('input-text')->name('bank_branch')->sm(6)->label(trans('field.bank_branch',[],'userCard'))->placeholder(trans('form.input',['attribute'=>trans('field.bank_branch',[],'userCard')],'messages')),
                shopwwiAmis('input-text')->name('mobile')->sm(6)->label(trans('field.mobile',[],'userCard'))->placeholder(trans('form.input',['attribute'=>trans('field.mobile',[],'userCard')],'messages'))->required(true),
            ]);
    }

    public static function bulkDeleteButton(): AjaxAction
    {
        return AjaxAction::make()
            ->api('delete:'.shopwwiUserUrl('bank/${id}'))
            ->icon('ri-forbid-2-line')->level('danger')
            ->label('解绑')
            ->confirmText('确认要解除选定绑定银行卡吗？');
    }
}