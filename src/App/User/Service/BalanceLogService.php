<?php

namespace Shopwwi\Admin\App\User\Service;

class BalanceLogService
{
    public static function getIndexAmis()
    {
        return
            shopwwiAmis('crud')->perPage(15)
                ->perPageField('limit')
                ->bulkActions()->syncLocation(false)
                ->headerToolbar([
                    'bulkActions',
                    shopwwiAmis('reload')->align('right'),
                ])
                ->api(shopwwiUserUrl('balance?_format=json'))
                ->columns([
                    shopwwiAmis()->name('created_at')->label(trans('field.created_at',[],'messages')),
                    shopwwiAmis()->name('old_amount')->label(trans('field.old_amount',[],'userBalanceLog')),
                    shopwwiAmis()->name('available_balance')->label(trans('field.available_balance',[],'userBalanceLog'))->classNameExpr("<%= data.available_balance > 0 ? 'text-danger' : 'text-success' %>"),
                    shopwwiAmis()->name('frozen_balance')->label(trans('field.frozen_balance',[],'userBalanceLog'))->classNameExpr("<%= data.frozen_balance > 0 ? 'text-danger' : 'text-success' %>"),
                    shopwwiAmis()->name('description')->label(trans('field.description',[],'userBalanceLog')),
                ]);
    }
}