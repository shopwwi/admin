<?php
/**
 *-------------------------------------------------------------------------s*
 * 全局配置文件
 *-------------------------------------------------------------------------h*
 * @copyright  Copyright (c) 2015-2022 Shopwwi Inc. (http://www.shopwwi.com)
 *-------------------------------------------------------------------------o*
 * @license    http://www.shopwwi.com        s h o p w w i . c o m
 *-------------------------------------------------------------------------p*
 * @link       http://www.shopwwi.com by 无锡豚豹科技
 *-------------------------------------------------------------------------w*
 * @since      ShopWWI智能管理系统
 *-------------------------------------------------------------------------w*
 */

use support\Redis;
function shopwwiAdminPath(){
    return __DIR__. DIRECTORY_SEPARATOR . 'src';
}
if (!function_exists('shopwwiWhereParams')) {
    /**
     * 索引条件
     * @param $model
     * @param $param
     * @param array $unset
     * @return mixed
     */
    function shopwwiWhereParams($model, $param, $unset = [])
    {
        unset($param['page']);unset($param['perPage']);
        unset($param['limit']);
        unset($param['orderDir']);
        unset($param['_t']);
        unset($param['orderBy']);
        unset($param['resultType']);
        unset($param['dataRecovery']);
        unset($param['_format']);
        unset($param['client_type']); //优先排除
        foreach ($unset as $val) {
            unset($param[$val]);
        }
        $with_has = '';
        foreach ($param as $value => $key) {
            if ($key != null) {
                if (strchr($value, '_like') != null) {
                    $model = $model->where(strchr($value, '_like', true), 'like', '%' . $key . '%');
                }else if(strchr($value, '_likeId') != null){
                    $model = $model->where(strchr($value, '_like', true), 'like', '%\\_' . $key . '\\_%');
                }else if (strchr($value, '_betweenDate') != null && $key != null) {
                    if (preg_match('/^\d{4}-\d{2}-\d{2} ~ \d{4}-\d{2}-\d{2}$/', $key)) {
                        $date = explode('~', $key);
                        $timeMin = $date[0];
                        $timeMax = $date[1];
                        $model = $model->whereBetween(strchr($value, '_betweenDate', true), [$timeMin, $timeMax]);
                    }
                } else if (strchr($value, '_betweenAmisTime') != null) {
                    if ($date = explode(',',$key)) {
                        $column = strchr($value, '_betweenAmisTime', true);
                        if(is_numeric($date[0])){
                            $date[0] = \Carbon\Carbon::parse($date[0]);
                            $date[1] = \Carbon\Carbon::parse($date[1]);
                        }
                        if ($date[0] > $date[1]) {
                            $model = $model->whereBetween($column, [$date[1], $date[0]]);
                        } else {
                            $model = $model->whereBetween($column, [$date[0], $date[1]]);
                        }
                    }
                } else if (strchr($value, '_betweenTime') != null) {
                    if (is_array($key)) {
                        $column = strchr($value, '_betweenTime', true);
                        if(is_numeric($key[0])){
                            $key[0] = \Carbon\Carbon::parse($key[0])->toDateTimeString();
                            $key[1] = \Carbon\Carbon::parse($key[1])->toDateTimeString();
                        }
                        if ($key[0] > $key[1]) {
                            $model = $model->whereBetween($column, [$key[1], $key[0]]);
                        } else {
                            $model = $model->whereBetween($column, [$key[0], $key[1]]);
                        }
                    }
                } else if (strchr($value, '_between') != null) {
                    if (is_array($key)) {
                        if ($key[0] > $key[1]) {
                            $model = $model->whereBetween(strchr($value, '_between', true), [$key[1], $key[0]]);
                        } else {
                            $model = $model->whereBetween(strchr($value, '_between', true), [$key[0], $key[1]]);
                        }
                    }
                } else if (strchr($value, '_array') != null) {
                    if (is_array($key)) {
                        $model = $model->whereIn(strchr($value, '_array', true), $key);
                    }else if (is_string($key)) {
                        $model = $model->whereIn(strchr($value, '_array', true), explode(',',$key));
                    }
                } else if (strchr($value, '_not') != null) {
                    $model = $model->where(strchr($value, '_not', true), '<>', $key);
                } else if (strchr($value, '_has')) {
                    $relation = explode('_has', $value)[0];
                    if (!empty($param[$value])) {
                        $with_has = $param[$value];
                        $model = $model->whereHas($relation, function ($q) use (&$param, $value) {
                            if (!empty($param[$param[$value]])) {
                                $q->where($param[$value], $param[$param[$value]]);
                            }
                        });
                    }
                } else if ($with_has != $value) {
                    $model = $model->where($value, $key);
                }
            }
        }
        return $model;
    }
}
if (!function_exists('shopwwiParams')) {
    /**
     * 设定默认词
     * @param $params
     * @param null $request
     * @param false $object
     * @return array|mixed
     */
    function shopwwiParams($params, $request = null, $object = false)
    {
        if ($request === null) $request = request()->all();
        $data = $request;
        $item = [];
        foreach ($params as $key => $val) {
            if (is_int($key)) {
                $default = null;
                $key = $val;
                if (!array_key_exists($key,$data)) {
                    continue;
                }
                //当传过来的值为非数字型 且为空时直接过滤掉
//                if (!is_numeric($data[$key]) && empty($data[$key])) {
//                    continue;
//                }
            } else {
                if($val === null && !array_key_exists($key,$data)){
                    continue;
                }
                $default = $val;
            }
            $item[$key] = !array_key_exists($key,$data) ? $default:$data[$key];
        }
        if ($object) {
            return shopwwiGetStdObject($item);
        }
        return $item;
    }
}

if (!function_exists('shopwwiGetStdObject')) {
    /**
     * 转为对象
     * @param $arrayList
     * @return mixed
     */
    function shopwwiGetStdObject($arrayList)
    {
        return json_decode(json_encode($arrayList));
    }
}
if (!function_exists('shopwwiJson')) {
    /**
     * 状态返回
     * @param int $code
     * @param string $msg
     * @param array $datas
     * @param array $extend_data
     * @return \support\Response
     */
    function shopwwiJson($code = 0, $msg = "成功", $datas = [], $extend_data = [])
    {
        $data = array();
        $data['status'] = $code;
        $data['msg'] = $msg;
        $data['message'] = $msg;
        $data['data'] = $datas;

        if (!empty($extend_data)) {
            $data = array_merge($data, $extend_data);
        }
        return json($data);
    }
}
if (!function_exists('shopwwiSuccess')) {
    function shopwwiSuccess($datas = [], $msg = null||[], $extend_data = [])
    {
        if ($msg == null) {
            $msg = trans('opSuccess',[],'messages');
        } else {
            if (is_array($msg) || is_object($msg)) {
                $extend_data = $msg;
                $msg = trans('opSuccess',[],'messages');
            }
        }
        return shopwwiJson(0, $msg, $datas, $extend_data);
    }
}
if (!function_exists('shopwwiError')) {
    function shopwwiError($msg = "失败", $datas = [], $extend_data = [])
    {
        $extend_data['error'] = $msg;
        return shopwwiJson(400, $msg, $datas, $extend_data);
    }
}
if (!function_exists('shopwwiValidator')) {
    function shopwwiValidator($msg = "失败", $errors = [],$datas = [])
    {
        return shopwwiJson(422, $msg, $datas,['errors'=>$errors]);
    }
}
if (!function_exists('shopwwiMethod')) {
    /**
     * 判断请求类型的正确性
     * @param $method
     * @return void
     * @throws \Exception
     */
    function shopwwiMethod($method)
    {

        if (is_array($method)) {
            if (!in_array(request()->method(), $method)) {
                throw new \Exception('请求方式不正确');
            }
        } else {
            if (request()->method() != $method) {
                throw new \Exception('请求方式不正确');
            }
        }
    }
}
if (!function_exists('shopwwiIsPhoneNumber')) {
    /**
     * 验证是否手机号
     * @param $phone_number
     * @return bool
     */
    function shopwwiIsPhoneNumber($phone_number)
    {
        //中国联通号码：130、131、132、145（无线上网卡）、155、156、185（iPhone5上市后开放）、186、176（4G号段）、175（2015年9月10日正式启用，暂只对北京、上海和广东投放办理）,166,146
        //中国移动号码：134、135、136、137、138、139、147（无线上网卡）、148、150、151、152、157、158、159、178、182、183、184、187、188、198
        //中国电信号码：133、153、180、181、189、177、173、149、199
        $g = "/^1[34578]\d{9}$/";
        $g1 = "/^19[89]\d{8}$/";
        $g2 = "/^166\d{8}$/";
        if (preg_match($g, $phone_number)) {
            return true;
        } else if (preg_match($g1, $phone_number)) {
            return true;
        } else if (preg_match($g2, $phone_number)) {
            return true;
        }
        return false;
    }
}
if (!function_exists('shopwwiIsTelNumber')) {
    function shopwwiIsTelNumber($tel)
    {
        $isTel = "/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        if (preg_match($isTel, $tel)) {
            return true;
        }
        return false;
    }
}
if (!function_exists('shopwwiIsEmailText')) {
    function shopwwiIsEmailText($email)
    {
        $regex = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        $regex = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if (preg_match($regex, $email)) {
            return true;
        }
        return false;
    }
}
if (!function_exists('shopwwiIsUserOnline')) {
    function shopwwiIsUserOnline($uid)
    {
        $has = Redis::get("user-is-online-{$uid}");
        return $has ? 1 : 0;
    }
}

if (!function_exists('shopwwiClientType')) {
    /**
     * 索引条件
     * @param $model
     * @param $param
     * @param array $unset
     * @return mixed
     */
    function shopwwiClientType()
    {
        return request()->input('client_type', 'api');
    }
}
if (!function_exists('now')) {
    function now()
    {
        return \Illuminate\Support\Facades\Date::now();
    }
}

if (!function_exists('shopwwiAmis')) {
    function shopwwiAmis($type = null): \Shopwwi\Admin\Amis\Component
    {
        $component = Shopwwi\Admin\Amis\Component::make();

        if ($type) {
            $component->setType($type);
        }

        return $component;
    }
}
if (!function_exists('shopwwiAmisFields')) {
    function shopwwiAmisFields($name, $attribute)
    {
        return new \Shopwwi\Admin\Libraries\Amis\AmisFields($name, $attribute);
    }
}

if (!function_exists('shopwwiAdminUrl')) {
    function shopwwiAdminUrl($path,$auto = true,$noUrl = false)
    {
        $prefix = config('plugin.shopwwi.admin.app.prefix.admin','/admin');
        $url = config('plugin.shopwwi.admin.app.base_url');
        if(request()->host() && $auto){
            $url = '//'.request()->host();
        }
        if(!empty($prefix)){
            $url = $url.$prefix;
        }
        if(empty($path)){
            return $url;
        }
        if($noUrl){
            if(!empty($prefix)){
                return  $prefix.'/'. trim($path, '/');
            }
            return  '/'. trim($path, '/');
        }
        return  $url.'/'. trim($path, '/');
    }
}

if (!function_exists('shopwwiUserUrl')) {
    function shopwwiUserUrl($path,$auto = true,$noUrl = false)
    {
        $prefix = config('plugin.shopwwi.admin.app.prefix.user','/user');
        $url = config('plugin.shopwwi.admin.app.base_url');
        if(request()->host() && $auto){
            $url = '//'.request()->host();
        }
        if(!empty($prefix)){
            $url = $url.$prefix;
        }
        if(empty($path)){
            return $url;
        }
        if($noUrl){
            if(!empty($prefix)){
                return  $prefix.'/'. trim($path, '/');
            }
            return  '/'. trim($path, '/');
        }
        return  $url.'/'. trim($path, '/');
    }
}

if (!function_exists('shopwwiConfig')) {
    function shopwwiConfig(string $key = null, $default = null){
        return (new Shopwwi\Admin\Libraries\ShopwwiConfig)->get($key,$default);
    }
}

if (!function_exists('shopwwiParamsUrl')) {
    function shopwwiParamsUrl($parameters = []){
        $fullUrl = request()->url();
        $query = request()->all();
        if (count($query) > 0) {
            $parameters = array_merge($query, $parameters);
        }
        return $fullUrl.(str_contains($fullUrl, '?') ? '&' : '?').http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    }
}

/**
 * 重定义除非 防止被除数为0时报错
 * @param $num1
 * @param $num2
 * @param $scale
 * @return string
 */
function shopwwiDiv($num1,$num2,$scale = null){
    if(\bccomp($num2,0,10) == 0 || \bccomp($num1,0,10) == 0){
        return 0;
    }
    return \bcdiv($num1,$num2,$scale);
}