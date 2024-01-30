<?php

namespace Shopwwi\Admin\App\User\Service;

class PointLogService
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
                ->api(shopwwiUserUrl('points?_format=json'))
                ->columns([
                    shopwwiAmis()->name('created_at')->label(trans('field.created_at',[],'messages')),
                    shopwwiAmis()->name('points')->label(trans('field.points',[],'userPointLog')),
                    shopwwiAmis()->name('available_points')->label(trans('field.available_points',[],'userPointLog'))->classNameExpr("<%= data.available_points > 0 ? 'text-danger' : 'text-success' %>"),
                    shopwwiAmis()->name('frozen_points')->label(trans('field.frozen_points',[],'userPointLog'))->classNameExpr("<%= data.frozen_points > 0 ? 'text-danger' : 'text-success' %>"),
                    shopwwiAmis()->name('description')->label(trans('field.description',[],'userPointLog')),
                ]);
    }
}