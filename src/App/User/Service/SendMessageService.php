<?php
/**
 *-------------------------------------------------------------------------s*
 *
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
namespace Shopwwi\Admin\App\User\Service;

use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Shopwwi\Admin\App\Admin\Models\SysMsgTplCommon;
use Shopwwi\Admin\App\Admin\Models\SysMsgTplSystem;
use Shopwwi\Admin\App\User\Models\UserMessageSetting;
use Shopwwi\Admin\App\User\Models\UserMsg;
use Shopwwi\Admin\App\User\Models\Users;
use Shopwwi\Admin\Logic\SendMessageLogic;

class SendMessageService
{
    /**
     * 发送系统消息
     * @param $tplCode
     * @param $number
     * @param $code
     * @param array $param
     * @return void
     * @throws InvalidArgumentException
     */
    public static function sendSystem($tplCode,$number,$code,$param=[])
    {
        try {
            $tpl = SysMsgTplSystem::where('code',$tplCode)->first();
            if($tpl == null){
                throw new \Exception('模板不存在');
            }
            $param['siteName'] = shopwwiConfig('siteInfo.siteName','Shopwwi智能系统');
            $param['code'] = $code;
            $title = self::replaceMsg($tpl->title,$param);
            $content = self::replaceMsg($tpl->content,$param);
            switch ($tpl->send_type){
                case 'EMAIL':
                    SendMessageLogic::sendEmail($number,$title,$content);
                    break;
                case 'MSG':
                    SendMessageLogic::sendSms($number,$content,'SMS_203780295'??$tplCode,$param);
                    break;
            }
        }catch (\Exception $e){
           //  消息失败不回滚
        }

    }

    /**
     * 发送会员消息
     * @param $tplCode
     * @param $userId
     * @param array $param
     * @param string $sn
     * @throws \Exception
     */
    public static function sendUser($tplCode,$userId,$param = [],$sn ='')
    {
        // 验证会员是否接收该信息 还没写
        $userMessageSetting = UserMessageSetting::where('tpl_code',$tplCode)->where('user_id',$userId)->first();
        if($userMessageSetting != null && empty($userMessageSetting->status)){
            return;
        }
        try {
            $config = shopwwiConfig('siteInfo',[]);
            $tpl = SysMsgTplCommon::where('code',$tplCode)->first();
            if($tpl == null || $tpl->type != 1){
                throw new \Exception('不是会员的消息模板');
            }
            $userInfo = Users::where('id',$userId)->first();
            $param['siteName'] = $config['siteName']??'Shopwwi';
            $param['wapRoot'] = $config['wapRoot']??'';
            //站内信
            UserMsg::create([
                'user_id' => $userInfo->id,
                'content' => self::replaceMsg($tpl->notice_content,$param),
                'tpl_class' => $tpl->class,
                'tpl_code' => $tplCode,
                'sn' => $sn
            ]);
            //短信
            if($tpl->sms_status && $userInfo->mobile){
                $smsContent = self::replaceMsg($tpl->sms_content,$param);
                // 队列发送短信 SendMessageQueue::pushQueueSms($userInfo->mobile,$smsContent,$tplCode,$param);
            }
            //邮件
            if($tpl->email_status && $userInfo->email){
                $emailTitle = self::replaceMsg($tpl->email_title,$param);
                $emailContent = self::replaceMsg($tpl->email_content,$param);
               // SendMessageQueue::pushQueueEmail($userInfo->email,$emailTitle,$emailContent);
            }
            //微信
            if($tpl->wechat_status){
                $templateId = $tpl->wechat_mp_template_id;
                $toUrl = $tpl->wechat_template_url;
                if($tpl->wechat_data_params){
                    $weChatDataParams = self::replaceMsg($tpl->wechat_data_params,$param);
                }
                if($userId > 0){
                    if($templateId){
                     //   SendMessageQueue::pushQueueWeiXin($userId,$templateId,$toUrl,$weChatDataParams);
                    }
                }
            }
        }catch (\Exception $exception){
            // 消息失败不回滚
            //  throw new \Exception($exception->getMessage());
        }
    }

    /**
     * 格式化模板内容
     * @param $html
     * @param array $param
     * @return string|string[]
     */
    public static function replaceMsg($html , array $param=[])
    {
        $search = [];
        $replace = [];
        //获得需要替换的字段和值
        foreach ($param as $key=>$item){
            $search[] = '{$' . $key . '}';
            $replace[] = $item;
        }
        //替换
        return str_replace($search,$replace,$html);
    }
}