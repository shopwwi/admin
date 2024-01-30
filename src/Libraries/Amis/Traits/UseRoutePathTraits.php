<?php

namespace Shopwwi\Admin\Libraries\Amis\Traits;

trait UseRoutePathTraits
{

    protected function getUrl($path = null){
        return shopwwiAdminUrl($path);
    }

    /**
     * 列表获取数据
     *
     * @return string
     */
    protected function useAmisListUrl(): string
    {
        return $this->getUrl($this->queryPath . '?_format=json');
    }

    /**
     * 删除
     *
     * @return string
     */
    protected function useAmisDestroyUrl($key): string
    {
        return 'delete:' . $this->getUrl($this->queryPath . '/'.$key);
    }

    /**
     * 批量删除
     *
     * @return string
     */
    protected function useAmisBatchDestroyUrl(): string
    {
        return 'delete:' . $this->getUrl($this->queryPath . '/${ids}');
    }

    /**
     * 编辑页面
     *
     * @return string
     */
    protected function useEditUrl(): string
    {
        if($this->format() == 'data') return '/' . trim($this->queryPath, '/') . '/${id}/edit';
        return $this->getUrl($this->queryPath . '/${id}/edit');
    }

    /**
     * 编辑 获取数据
     *
     * @param $id
     *
     * @return string
     */
    protected function useAmisEditUrl($id): string
    {
        return $this->getUrl($this->queryPath . '/' . $id . '/edit?_format=json');
    }
    protected function useAmisCreateUrl(): string
    {
        return $this->getUrl($this->queryPath . '/create?_format=json');
    }
    /**
     * 详情页面
     *
     * @return string
     */
    protected function useShowUrl(): string
    {
        if($this->format() == 'data') return '/' . trim($this->queryPath, '/') . '/$'.$this->key;
        return $this->getUrl($this->queryPath . '/$'.$this->key);
    }

    /**
     * 编辑保存
     *
     * @param $id
     *
     * @return string
     */
    protected function useAmisUpdateUrl($id): string
    {
        return 'put:' . $this->getUrl($this->queryPath . '/' . $id);
    }

    /**
     * 详情 获取数据
     *
     * @param $id
     *
     * @return string
     */
    protected function useAmisShowUrl($id): string
    {
        return $this->getUrl($this->queryPath . '/' . $id . '?_format=json');
    }

    /**
     * 新增页面
     * @return string
     */
    protected function useCreateUrl(): string
    {
        if($this->format() == 'data') return '/' . trim($this->queryPath, '/') . '/create';
        return $this->getUrl($this->queryPath . '/create');
    }

    /**
     * 新增 保存
     *
     * @return string
     */
    protected function useAmisStoreUrl(): string
    {
        return 'post:' . $this->getUrl($this->queryPath);
    }

    protected function useListUrl(): string
    {
        if($this->format() == 'data') return '/' . trim($this->queryPath, '/');
        return request()->uri();
    }
}