<?php
/**
 *-------------------------------------------------------------------------s*
 * 发送消息
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
namespace Shopwwi\Admin\Logic;

use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Shopwwi\Admin\App\Admin\Service\SysConfigService;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class SendMessageLogic
{
    /**
     * 发送邮件
     * @param $number
     * @param $title
     * @param $content
     * @return void
     * @throws \Exception
     */
    public static function sendEmail($number,$title,$content)
    {
        try {
            $config = SysConfigService::getSettingByKey('email');
            $transport = Transport::fromDsn('smtp://'.$config['username'].':'.$config['password'].'@'.$config['smtp'].':'.$config['port']);
            $mailer = new Mailer($transport);
            $email = (new Email())->from($config['form_address'])->to($number)->subject($title)->html($content);
            $mailer->send($email);
        }catch (TransportExceptionInterface $exception){
            throw new \Exception('发送失败');
            //  dd($exception->getExceptions());
        }
    }

    /**
     * 发送短信
     * @param $number
     * @param $content
     * @param string $tplCode
     * @param string $data
     * @return void
     * @throws InvalidArgumentException
     */
    public static function sendSms($number,$content,$tplCode='',$data='')
    {
        $config = SysConfigService::getSettingByKey('sms');
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => $config['timeout'],
            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                // 默认可用的发送网关
                'gateways' => [
                    'yunpian'
                ],
            ],
            // 可用的网关配置
            'gateways' => $config['gateways'],
        ];
        //发送短信
        try {
            $easySms = new EasySms($config);
            $easySms->send($number, ['content'=> $content]//短信内容
            );
        }catch (NoGatewayAvailableException $exception){
           // Log::info($exception->getMessage());
            //  dd($exception->getExceptions());
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