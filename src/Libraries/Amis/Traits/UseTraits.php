<?php

namespace Shopwwi\Admin\Libraries\Amis\Traits;

use Shopwwi\Admin\Amis\AjaxAction;
use Shopwwi\Admin\Amis\Button;
use Shopwwi\Admin\Amis\ButtonGroup;
use Shopwwi\Admin\Amis\CRUDTable;
use Shopwwi\Admin\Amis\Dialog;
use Shopwwi\Admin\Amis\DialogAction;
use Shopwwi\Admin\Amis\Drawer;
use Shopwwi\Admin\Amis\DrawerAction;
use Shopwwi\Admin\Amis\DropdownButton;
use Shopwwi\Admin\Amis\Form;
use Shopwwi\Admin\Amis\LinkAction;
use Shopwwi\Admin\Amis\Operation;
use Shopwwi\Admin\Amis\OtherAction;
use Shopwwi\Admin\Amis\Page;

trait UseTraits
{
    protected $projectFields;
    protected $useShowDialog = 1;
    protected $useCreateDialog = 1;
    protected $useEditDialog = 1;
    protected $useIndexBack = false;

    protected $useShowDialogSize = 'md';
    protected $useCreateDialogSize = 'md';
    protected $useEditDialogSize = 'md';
    protected $useHasRecovery = false;
    protected $useHasCreate = true;
    protected $useHasDestroy = true;
    protected $useHasCsv = true;
    protected $buttonCache = false;
    protected $buttonNext = true;
    public function __construct()
    {
        $this->projectFields = $this->fields();
    }
    /**
     * 基础页面
     * @param string $title
     * @return Page
     */
    protected function basePage(string $title = 'list'): Page
    {
        $page = Page::make()->className('m:overflow-auto px-4 bg-transparent')->headerClassName('border-b-0');
        $page->title(trans($title,[],$this->trans));
        return $page;
    }

    /**
     * 返回按钮
     * @return OtherAction|null
     */
    protected function backButton(): ?OtherAction
    {
        return OtherAction::make()
            ->label(trans('back',[],'messages'))
            ->icon('ri-arrow-go-back-line')
            ->level('primary')
            ->onEvent([
                'click' => [
                    'actions' => [
                        [
                            'actionType' => 'goBack'
                        ]
                    ]
                ]
            ]);
    }

    /**
     * 批量删除操作
     * @return AjaxAction
     */
    protected function bulkDeleteButton(): AjaxAction
    {
        return AjaxAction::make()
            ->api($this->useAmisBatchDestroyUrl())
            ->icon('ri-delete-bin-6-line')->level('danger')
            ->label(trans('batchDelete',[],'messages'))
            ->confirmText(trans('confirmDelete',[],'messages'));
    }

    /**
     * 批量还原
     * @return AjaxAction
     */
    protected function bulkRestoreButton(){
        return AjaxAction::make()
            ->api('put:' . $this->getUrl($this->queryPath . '/recovery/${ids}'))
            ->level('primary')
            ->label(trans('batchRestore',[],'messages'))
            ->confirmText(trans('confirmRestore',[],'messages'));
    }

    /**
     * 批量销毁
     * @return AjaxAction
     */
    protected function bulkErasureButton(){
        return AjaxAction::make()
            ->api('delete:' . $this->getUrl($this->queryPath . '/recovery/${ids}'))
            ->level('danger')
            ->label(trans('batchErasure',[],'messages'))
            ->confirmText(trans('confirmErasure',[],'messages'));
    }

    /**
     * 新增按钮
     * @param int $dialog
     * @return DialogAction|LinkAction
     */
    protected function createButton(int $dialog = 0)
    {
        $form = $this->form()->api($this->useAmisStoreUrl())->initApi($this->useAmisCreateUrl());

        if ($dialog > 0) {
            if($dialog == 2){
                $button = DrawerAction::make()->drawer(
                    Drawer::make()->title(trans('create',[],$this->trans))->position('left')->body($form)->size($this->useCreateDialogSize)
                );
            }else {
                $button = DialogAction::make()->dialog(
                    Dialog::make()->title(trans('create', [], $this->trans))->body($form)->size($this->useCreateDialogSize)
                );
            }
        } else {
            if($this->buttonCache){
                $createPath = shopwwiAdminUrl($this->queryPath.'/create',true,true);
                $button = shopwwiAmis('button')->onEvent([
                    'click' => [
                        'actions' => [
                            [
                                'actionType' => 'custom',
                                'script' => " 
                                if(this.wwiRouter){
                                    this.wwiRouter.push('{$createPath}');
                                }else{
                                    doAction({actionType: 'link', args: {link: '{$this->useCreateUrl()}'}});
                                }
                            "
                            ]
                        ]
                    ]
                ]);
            }else{
                $button = LinkAction::make()->link($this->useCreateUrl());
            }
        }

        return $button->label(trans('create',[],$this->trans))->icon('ri-add-line')->level('primary');
    }

    /**
     * 行编辑按钮
     * @param int $dialog
     * @return DialogAction|LinkAction
     */
    protected function rowEditButton(int $dialog = 0,$key='$id')
    {
        if ($dialog > 0) {
            $form = $this->form('edit')->api($this->useAmisUpdateUrl($key))->initApi($this->useAmisEditUrl($key));
            if($dialog == 2){
                $button = DrawerAction::make()->drawer(
                    Drawer::make()->title(trans('update',[],$this->trans))->actions([
                        shopwwiAmis('button')->actionType('prev')->visibleOn('data.hasPrev')->label(trans('prev',[],'messages'))->level('link'),
                        shopwwiAmis('button')->actionType('cancel')->label(trans('cancel',[],'messages')),
                        shopwwiAmis('submit')->actionType('next')->visibleOn('data.hasNext')->label(trans('submitNext',[],'messages'))->level('primary'),
                        shopwwiAmis('submit')->visibleOn('!data.hasNext')->label(trans('submit',[],'messages'))->level('primary'),
                        shopwwiAmis('button')->actionType('next')->visibleOn('data.hasNext')->label(trans('next',[],'messages'))->level('link'),
                    ])->position('left')->body($form)->size($this->useEditDialogSize)
                );
            }else{
                if($this->buttonNext){
                    $button = DialogAction::make()->dialog(
                        Dialog::make()->title(trans('update',[],$this->trans))->actions([
                            shopwwiAmis('button')->actionType('prev')->visibleOn('data.hasPrev')->label(trans('prev',[],'messages'))->level('link'),
                            shopwwiAmis('button')->actionType('cancel')->label(trans('cancel',[],'messages')),
                            shopwwiAmis('submit')->actionType('next')->visibleOn('data.hasNext')->label(trans('submitNext',[],'messages'))->level('primary'),
                            shopwwiAmis('submit')->visibleOn('!data.hasNext')->label(trans('submit',[],'messages'))->level('primary'),
                            shopwwiAmis('button')->actionType('next')->visibleOn('data.hasNext')->label(trans('next',[],'messages'))->level('link'),
                        ])->body($form)->size($this->useEditDialogSize)
                    );
                }else{
                    $button = DialogAction::make()->dialog(
                        Dialog::make()->title(trans('update',[],$this->trans))->body($form)->size($this->useEditDialogSize)
                    );
                }

            }

        } else {
            if($this->buttonCache){
                $editPath = shopwwiAdminUrl($this->queryPath,true,true);
                $tk = trim($key,'$');
                $button = shopwwiAmis('button')->onEvent([
                    'click' => [
                        'actions' => [
                            [
                                'actionType' => 'custom',
                                'script' => " 
                                if(this.wwiRouter){
                                    this.wwiRouter.push('{$editPath}/' + event.data['{$tk}'] + '/edit')
                                }else{
                                    doAction({actionType: 'link', args: {link: '{$this->useEditUrl()}'}});
                                }
                            "
                            ]
                        ]
                    ]
                ]);
            }else{
                $button = LinkAction::make()->link($this->useEditUrl());
            }
        }

        return $button->label(trans('update',[],'messages'))->icon('ri-edit-line')->level('link');
    }

    /**
     * 行详情按钮
     *
     * @param int $dialog
     * @return DialogAction|LinkAction
     */
    protected function rowShowButton(int $dialog = 0,$key = '$id')
    {
        if ($dialog > 0) {
            if($dialog == 2){
                $button = DrawerAction::make()->drawer(
                    Drawer::make()->title(trans('show',[],$this->trans))->position('left')->body($this->htmlShow($key))->size($this->useShowDialogSize)
                );
            }else {
                $button = DialogAction::make()->dialog(
                    Dialog::make()->title(trans('show', [], $this->trans))->body($this->htmlShow($key))->size($this->useShowDialogSize)
                );
            }
        } else {
            if($this->buttonCache){
                $showPath = shopwwiAdminUrl($this->queryPath,true,true);
                $tk = trim($key,'$');
                $button = shopwwiAmis('button')->onEvent([
                    'click' => [
                        'actions' => [
                            [
                                'actionType' => 'custom',
                                'script' => " 
                                if(this.wwiRouter){
                                    this.wwiRouter.push('{$showPath}/' + event.data['{$tk}'])
                                }else{
                                    doAction({actionType: 'link', args: {link: '{$this->useShowUrl()}'}});
                                }
                            "
                            ]
                        ]
                    ]
                ]);
            }else{
                $button = LinkAction::make()->link($this->useShowUrl());
            }
        }

        return $button->label(trans('show',[],'messages'))->icon('ri-eye-line mr-2')->level('link');
    }

    /**
     * 行删除按钮
     *
     * @return AjaxAction
     */
    protected function rowDeleteButton(): AjaxAction
    {
        return AjaxAction::make()
            ->label(trans('delete',[],'messages'))
            ->icon('ri-delete-bin-2-line mr-2')
            ->level('link')
            ->confirmText(trans('confirm_delete',[],$this->trans))
            ->api($this->useAmisDestroyUrl('${'.$this->key.'}'));
    }

    /**
     * 行恢复按钮
     * @return AjaxAction
     */
    protected function rowRestoreButton(): AjaxAction
    {
        return AjaxAction::make()
            ->label(trans('restore',[],'messages'))
            ->level('link')
            ->actionType('ajax')
            ->confirmText(trans('confirmRestore',[],'messages'))
            ->api('put:' . $this->getUrl($this->queryPath . '/recovery/${'.$this->key.'}'));
    }

    /**
     * 行销毁按钮
     * @return AjaxAction
     */
    protected function rowErasureButton(): AjaxAction
    {
        return AjaxAction::make()
            ->label(trans('erasure',[],'messages'))
            ->level('link')
            ->className('text-danger')
            ->actionType('ajax')
            ->confirmText(trans('confirmErasure',[],'messages'))
            ->api('delete:' . $this->getUrl($this->queryPath . '/recovery/${'.$this->key.'}'));
    }

    /**
     * 操作列
     * @return Operation
     */
    protected function rowActions(): Operation
    {
        return Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(160)->buttons([
            $this->rowEditButton($this->useEditDialog,'$'.$this->key),
            DropdownButton::make()->label(trans('more',[],'messages'))->icon('ri-more-2-line')->trigger('hover')->align('right')->level('link')->buttons([
                $this->rowShowButton($this->useShowDialog,'$'.$this->key),
                $this->rowDeleteButton(),
            ])
        ]);
    }

    /**
     * 仅有编辑和删除按钮
     * @param $dialog
     * @param string $dialogSize
     * @return Operation
     */
    protected function rowActionsOnlyEditAndDelete($dialog = false, string $dialogSize = ''): Operation
    {
        return Operation::make()->label(trans('actions',[],'messages'))->fixed('right')->width(145)->buttons([
            $this->rowEditButton($dialog, '$'.$this->key),
            $this->rowDeleteButton(),
        ]);
    }

    /**
     * 筛选数据
     * @return Form
     */
    protected function baseFilter(): Form
    {
        return Form::make()
            ->panelClassName('base-filter')
            ->title('')
            ->actions([
                Button::make()->label(trans('reset',[],'messages'))->actionType('clear-and-submit'),
                shopwwiAmis('submit')->label(trans('search',[],'messages'))->level('primary'),
            ]);
    }

    /**
     * crudTable
     * @return CRUDTable
     */
    protected function baseCRUD(): CRUDTable
    {
        return CRUDTable::make()
            ->perPage(15)
            ->affixHeader(false)
            ->syncLocation(false)
            ->keepItemSelectionOnPageChange(true)
            ->perPageField('limit')
            ->defaultParams(request()->except(['_format']))
            ->primaryField($this->key)
            ->api($this->useAmisListUrl())
            ->quickSaveItemApi($this->useAmisUpdateUrl('$'.$this->key))
            ->bulkActions([$this->bulkDeleteButton()])
            ->perPageAvailable([15, 30, 60, 100, 200, 500])
            ->footerToolbar(['statistics', 'switch-per-page', 'pagination'])
            ->headerToolbar([
                ...$this->baseHeaderToolBar(),
                ...$this->rightHeaderTooBar()
            ]);
    }

    protected function baseCardCRUD(){
        return CRUDTable::make()
            ->perPage(15)
            ->mode('cards')
            ->bulkActions([$this->bulkDeleteButton()])
            ->quickSaveItemApi($this->useAmisUpdateUrl('$'.$this->key))
            ->headerToolbar([
                'bulkActions',
                shopwwiAmis('reload')->align('right'),
                ...$this->rightHeaderTooBar()
            ])
            ->api($this->useAmisListUrl());
    }

    protected function defaultCrud(){
        return CRUDTable::make()

            ->affixHeader(false)
            ->keepItemSelectionOnPageChange(true)
            ->syncLocation(false)
            ->perPageField('limit')
            ->autoGenerateFilter(true)
            ->perPageAvailable([15, 30, 60, 100, 200, 500])
            ->perPage(15)
            ->footerToolbar(['statistics', 'switch-per-page', 'pagination']);
    }

    /**
     *
     * @return array
     */
    protected function baseHeaderToolBar(): array
    {
        $excel = '';
        $csv = '';
        if($this->useHasCsv){
            $excel = shopwwiAmis('export-excel');
            $csv = shopwwiAmis('export-csv');
        }
        return [
            'bulkActions',
            $excel,$csv,
            shopwwiAmis('reload')->align('right'),
            shopwwiAmis('columns-toggler')->draggable(true)->overlay(true)->footerBtnSize('sm')->align('right')
        ];
    }
    protected function rightHeaderTooBar($recovery = false){
        $list = [];
        if($this->useHasRecovery && !$recovery){
            $list[] = Button::make()->label(trans('recovery',[],'messages'))->actionType('link')->url($this->getUrl($this->queryPath.'/recovery'))->level('light')->align('right');
        }
        if($this->useHasCreate && !$recovery) $list[] = $this->createButton($this->useCreateDialog)->align('right');

        return $list;
    }

    /**
     * 基础表单
     * @return Form
     */
    protected function baseForm(): Form
    {
        return Form::make()
            ->panelClassName('px-40 py-10 m:px-0 border-0 box-shadow-none')
            ->title(null)
            ->mode('horizontal');
          //  ->redirect($this->useListUrl());
    }

    /**
     * 基础详情
     * @param $id
     * @return Form
     */
    protected function baseShow($id): Form
    {
        return Form::make()
            ->panelClassName('px-48 m:px-0')
            ->title(' ')
            ->mode('horizontal')
            ->static(true)
            ->actions([])
            ->initApi($this->useAmisShowUrl($id));
    }

    /**
     * 基础列表
     * @param $crud
     * @return Page
     */
    protected function baseList($crud): Page
    {
        $page = $this->basePage()->body([
            $this->setTips(),
            $crud
        ]);
        if($this->useIndexBack){
            $page->toolbar([$this->backButton()]);
        }
        return $page;
    }


}