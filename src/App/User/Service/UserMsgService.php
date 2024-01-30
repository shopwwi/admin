<?php

namespace Shopwwi\Admin\App\User\Service;

use Shopwwi\Admin\Amis\AjaxAction;

class UserMsgService
{
    public static function getIndexAmis()
    {
        return
            shopwwiAmis('crud')->perPage(15)->id('crud')->name('crud')
                ->mode('list')->columnsCount(3)->perPageField('limit')
                ->bulkActions([self::bulkDeleteButton(),self::bulkReadButton()])->syncLocation(false)
                ->headerToolbar([
                    'bulkActions',
                    shopwwiAmis('reload')->align('right')
                ])
                ->api(shopwwiUserUrl('message?_format=json'))
                ->listItem([
                    'title' => '$tplClassName',
                    'className' => "\${is_read == 1 ? 'opacity-50':''}",
                    'body'=> shopwwiAmis('flex')->justify('flex-start')->className('text-xs')->alignItems('center')->items([shopwwiAmis('icon')->icon('ri-focus-fill')->className('text-primary')->visibleOn('this.is_read != 1'),shopwwiAmis('tpl')->tpl('$content')]),
                    'actions' => [
                        shopwwiAmis('button')->body('查看详情')->className('text-xs')->onEvent([
                            'click' => [
                                'actions' => [
                                    ['actionType'=>'ajax','api'=>('post:'.shopwwiUserUrl('message/read/${id}')),'expression'=>'is_read != 1'],
                                    ['actionType'=>'link','args'=>['link'=> shopwwiUserUrl('mall/orders').'/${sn}'],'expression'=>"tpl_code =='userOrdersCancel' || tpl_code =='userOrdersEvaluateExplain' || tpl_code =='userOrdersModifyFreight' || tpl_code =='userOrdersPay' || tpl_code =='userOrdersReceive' || tpl_code =='userOrdersSend' || tpl_code =='userOrdersBookFinalPaymentNotice'"],
                                    ['actionType'=>'link','args'=>['link'=> shopwwiUserUrl('mall/orders/virtual').'/${sn}'],'expression'=>"tpl_code =='userVirtualOrdersCancel' || tpl_code =='userVirtualOrdersCodeUse' || tpl_code =='userVirtualOrdersPay'|| tpl_code =='userVirtualRefundUpdate'||tpl_code == 'userSendVirtualCode'"],
                                    ['actionType'=>'link','args'=>['link'=> shopwwiUserUrl('mall/chain/orders').'/${sn}'],'expression'=>"tpl_code =='userChainOrdersCancel' || tpl_code =='userChainOrdersPay'"],
                                    ['actionType'=>'link','args'=>['link'=> shopwwiUserUrl('mall/refund').'/${sn}'],'expression'=>"tpl_code =='userRefundUpdate'"],
                                    ['actionType'=>'link','args'=>['link'=> shopwwiUserUrl('mall/return').'/${sn}'],'expression'=>"tpl_code == 'userReturnAutoCancelNotice' || tpl_code == 'userReturnUpdate'"],
                                    ['actionType'=>'link','args'=>['link'=> shopwwiUserUrl('cash')],'expression'=>"tpl_code =='userBalanceCashFail'"],
                                    ['actionType'=>'link','args'=>['link'=> shopwwiUserUrl('recharge')],'expression'=>"tpl_code =='userBalanceChange'"],
                                ]
                            ]
                        ]),
                        shopwwiAmis('button')->actionType('ajax')->confirmText('您确定要删除该消息?')->tooltip('删除')->icon('fa fa-times')->api('delete:'.shopwwiUserUrl('message/${id}'))
                    ]
                ]);
    }

    protected static function bulkDeleteButton(): AjaxAction
    {
        return AjaxAction::make()
            ->api('delete:'.shopwwiUserUrl('message/${ids}'))
            ->icon('fa-solid fa-trash-can')->level('danger')
            ->label(trans('batchDelete',[],'messages'))
            ->confirmText(trans('confirmDelete',[],'messages'));
    }
    protected static function bulkReadButton(): AjaxAction
    {
        return AjaxAction::make()
            ->api('post:'.shopwwiUserUrl('message/read/${ids}'))
            ->level('info')
            ->label('设置已读');
    }
}