<?php
/**
 *-------------------------------------------------------------------------s*
 * 公用函数处理
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

namespace Shopwwi\Admin\Libraries;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use stdClass;

class Appoint
{

    /**
     * 分级排序
     * @param $data
     * @param int $pid
     * @param string $field
     * @param string $pk
     * @param string $html
     * @param int $level
     * @param bool $clear
     * @return array
     */
    private function ShopwwiSortListTier($data, $pid = 0, $field = 'pid', $pk = 'id', $html = '|-----', $level = 0, $clear = true)
    {
        static $list = [];
        if ($clear) $list = [];
        foreach ($data as $k => $res) {
            if ($res[$field] == $pid) {
                if ($level > 0) {
                    $res['html'] = str_repeat($html, $level);
                } else {
                    $res['html'] = '';
                }

                $list[] = $res;
                unset($data[$k]);
                $this->ShopwwiSortListTier($data, $res[$pk], $field, $pk, $html, $level + 1, false);
            }
        }
        return $list;
    }

    /**
     * 分级返回多维数组
     * @param $data
     * @param int $pid
     * @param string $field
     * @param string $pk
     * @param int $level
     * @param array $list
     * @return array
     */
    private function ShopwwiChindNode($data, $pid = 0, $field = 'pid', $pk = 'id', $level = 1, $list = [])
    {
        foreach ($data as $k => $res) {
            if ($res[$field] == $pid) {
                $new = $res;
                unset($data[$k]);
                $rs = self::ShopwwiChindNode($data, $res[$pk], $field, $pk, $level + 1);
                if (!empty($rs)) {
                    $new['children'] = $rs;
                }
                $list[] = $new;
            }
        }
        return $list;
    }

    /**
     * 数据大小单位转换
     * @param $size
     * @return string
     */
    private function formatBytes($size)
    {
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }

    /**
     * 金额处理
     * @param $price
     * @return string
     */
    private function PriceFormat($price)
    {
        return number_format($price, 2, ".", "");
    }

    /**
     * 价格格式化
     * @param $price
     * @return string
     */
    private function PriceInt($price){
        return $this->PriceFormat(intval($price * 100) / 100);
    }



    /**
     * 规范数据返回函数
     * @param bool $status
     * @param string $msg
     * @param array $data
     * @return array|object
     */
    private function ParamBack($status = true, $msg = '', $data = array())
    {
        return (object) ['status' => $status, 'msg' => $msg, 'data' => $data];
    }

    private function customException(int $code = 203,$message = ''){
        $message = $message;
        $code = $code;
        return shopwwiJson($code,$message);
    }

    /**
     * 列表页返回格式
     * @param $response //分页数据
     * @param $code //状态码
     * @param $name //表名
     * @return mixed
     */
    private function IndexListByJson($response, $code = 200,$name = 'list')
    {
        return shopwwiJson($code, trans($name), $response->items(), ['page' => $response->currentPage(), 'total' => $response->total()]);
    }

    /**
     * 列表页数组型数据返回
     * @param $response
     * @param int $code
     * @param string $name

     */
    private function IndexArrayByJson($response, $code = 200,$name = 'list'){

        return shopwwiJson($code, trans($name), $response['items'], ['page' => $response['currentPage'], 'total' => $response['total']]);
    }

    /**
     * @param array $databaseData
     * @param int $limit
     * @return mixed
     */
    private function ArrayPagination(array $databaseData, int $limit)
    {
        $page = optional(request())->page?request()->page:1;
        $start = ($page-1)*$limit;
        $response['items'] = array_slice($databaseData,$start,$limit);
        $response['total'] = ceil(count($databaseData)/$limit);
        $response['currentPage'] = $page;
        return $response;
    }

    /**
     * 获取子集
     * @param $model
     * @param $id
     * @param string $pid
     * @param array $field
     * @return array
     */
    private function childTree($model,$id,$field = array(),$pid='pid'){
        if (count($field)){
            $self = $model->select(...$field)->find($id)->toArray();
            $res = $model->where([$pid=>$id])->select(...$field)->get()->toArray();
        }else{
            $self = $model->find($id)->toArray();
            $res = $model->where([$pid=>$id])->get()->toArray();
        }
        if (count($res)){
            foreach ($res as $k=>$v){
                $self['child'][] = $this->childTree($model,$v['id'],$field);
            }
        }
        return $self;
    }

    /**
     * 数组转对象
     * @param array $arrayList
     * @return stdClass
     */
    private function getStdObject($arrayList){
        return  json_decode(json_encode($arrayList));
    }

    /**
     * 截取字符串为*号
     * @param $text
     * @return string
     */
    private function substrCut($text){
        $strlen = mb_strlen($text, 'utf-8');
        $firstStr = mb_substr($text, 0, 1, 'utf-8');
        $lastStr = mb_substr($text, -1, 1, 'utf-8');
        if($strlen<2) {
            return $text;
        }else {
            return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($text, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
        }
    }

    /**
     *
     * @param $array
     * @return array|mixed
     */
    private function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        }
        if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = self::object_array($value);
            }
        }
        return $array;
    }

    /**
     * 获取指定时间区间数组
     * @param string $atr
     * @return array
     */
    private function generateDateRange($start_date,$end_date,$atr = 'Y-m-d'){
        $dates = [];
        for ($date = Date::parse($start_date); $date->lte($end_date); $date->addDay()) {
            $dates[$date->format($atr)] = 0 ;
        }
        return $dates;
    }
    /**
     * 获取时间阶段数组
     * @return array
     */
    private function getTimeWeeks($start_date,$end_date){
        $dates = [];
        $week = ['日','一','二','三','四','五','六'];
        for ($date = Date::parse($start_date); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d').'[周'. $week[$date->dayOfWeek] .']';
        }
        return $dates;
    }

    private function replaceStringInFiles($file,$searchTerm, $replacement){
        // 打开文件进行读取和写入
        $handle = fopen($file, 'r+');
        $lines = array();
        // 初始化变量来存储当前行号
        $lineNumber = 1;
        $hasLine = -1;
        // 逐行读取文件内容并存储到数组中
        while (($line = fgets($handle)) !== false) {
            if (strpos($line, $searchTerm) !== false) {
                $hasLine = $lineNumber;
            }
            $lines[] = $line;
            $lineNumber ++;
        }

        fclose($handle);


        // 在指定行插入新的代码行
        $newLines = array();
        for ($i = 0; $i < count($lines); $i++) {
            if ($i === $hasLine && $hasLine != -1) { // 插入新的代码行的行号，从0开始计数
                $newLines[] = $replacement . PHP_EOL;
            }
            $newLines[] = $lines[$i];
        }

        // 将新的内容写回到文件中
        $handle = fopen($file, 'w'); // 以写模式打开文件
        foreach ($newLines as $line) {
            fwrite($handle, $line);
        }
        fclose($handle);

    }

    private function addAll($list,$data){
        foreach ($data as $item){
            $list->push($item);
        }
        return $list;
    }

    public function __call($method, $parameters)
    {
        return $this->{$method}(...$parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        //注意这里，通过延迟静态绑定，仍然new了一个实例
        return (new static)->{$method}(...$parameters);
    }



}
