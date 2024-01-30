<?php

namespace Shopwwi\Admin\App\User\Service;

class GradeService
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
                ->api(shopwwiUserUrl('grade?_format=json'))
                ->columns([
                    shopwwiAmis()->name('created_at')->label(trans('field.created_at',[],'messages')),
                    shopwwiAmis()->name('growth')->label(trans('field.growth',[],'userGrowthLog'))->classNameExpr("<%= data.growth > 0 ? 'text-danger' : 'text-success' %>"),
                    shopwwiAmis()->name('description')->label(trans('field.description',[],'userGrowthLog')),
                ]);
    }

    public static function getUserGrade(){

    }
}