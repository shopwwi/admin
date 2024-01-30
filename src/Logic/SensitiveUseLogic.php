<?php

namespace Shopwwi\Admin\Logic;

use Shopwwi\Admin\App\Admin\Models\SysSensitives;
use Shopwwi\LaravelCache\Cache;
use Shopwwi\Admin\Libraries\HashMap;

class SensitiveUseLogic
{
    /**
     * 敏感词过滤
     * @param $content *文本内容
     * @param string $replaceChar 替换字符
     * @param bool $repeat 重复替换为敏感词相同长度的字符
     * @param int $matchType 匹配类型，默认为最小匹配规则
     * @return string
     */
    public static function sensitiveWordFilter($content, $replaceChar = '*', $repeat = true, $matchType = 1)
    {

        $badWordList = self::getBadWord($content, $matchType);

        // 未检测到敏感词，直接返回
        if (empty($badWordList)) {
            return $content;
        }

        foreach ($badWordList as $badWord) {
            $hasReplacedChar = $replaceChar;
            if ($repeat) {
                $hasReplacedChar = self::dfaBadWordConversChars($badWord, $replaceChar);
            }

            $content = str_replace($badWord, $hasReplacedChar, $content);
        }

        return $content;
    }

    /**
     * 获取敏感词
     * @param $content * 文本内容
     * @param $matchType * 匹配类型，默认为最小匹配规则
     * @param $wordNum * 需要获取的敏感词数量，默认获取全部
     * @return array
     */
    public static function getBadWord($content, $matchType = 1, $wordNum = 0){
        $contentLength = mb_strlen($content, 'utf-8');
        $badWordList = [];

        for ($length = 0; $length < $contentLength; $length++) {
            $matchFlag = 0;
            $flag = false;
            $tempMap = self::getList();

            for ($i = $length; $i < $contentLength; $i++) {
                $keyChar = mb_substr($content, $i, 1, 'utf-8');

                // 检测干扰因子
                if (self::checkInterferenceFactor($keyChar)) {
                    $matchFlag++;
                    continue;
                }

                // 获取指定节点树
                $nowMap = $tempMap->get($keyChar);

                // 不存在节点树，直接返回
                if (empty($nowMap)) {
                    break;
                }

                // 存在，则判断是否为最后一个
                $tempMap = $nowMap;

                // 找到相应key，偏移量+1
                $matchFlag++;

                // 如果为最后一个匹配规则,结束循环，返回匹配标识数
                if ($nowMap->get('isEnd') === false) {
                    continue;
                }

                $flag = true;

                // 最小规则，直接退出
                if ($matchType === 1)  {
                    break;
                }
            }

            if (!$flag) {
                $matchFlag = 0;
            }

            if ($matchFlag > 0) {
                $badWordList[] = self::ltrimInterferenceFactorBadWord(mb_substr($content, $length, $matchFlag, 'utf-8'));

                // 有返回数量限制
                if ($wordNum > 0 && count($badWordList) == $wordNum) {
                    return $badWordList;
                }

                // 需匹配内容标志位往后移
                $length += $matchFlag - 1;
            }
        }


        return $badWordList;
    }

    /**
     * 检查干扰因子
     * @param $word
     * @return bool
     */
    public static function checkInterferenceFactor($word){
        return in_array($word, ['(', ')', ',', '，', ';', '；', '。','*']);
    }
    /**
     * 删除敏感词前的干扰因子
     *
     * @param string $word 需要处理的敏感词
     *
     * @return string
     */
    public static function ltrimInterferenceFactorBadWord(string $word)
    {
        $interferenceFactors = ['(', ')', ',', '，', ';', '；', '。'];
        $characters = '';
        foreach($interferenceFactors as $interferenceFactor) {
            $characters .= $interferenceFactor. '\\' .' '. $interferenceFactor;
        }

        return ltrim($word, $characters);
    }

    /**
     * 敏感词替换为对应长度的字符
     * @param $word
     * @param $char
     * @return string
     */
    public static function dfaBadWordConversChars($word, $char)
    {
        $str = '';
        $length = mb_strlen($word, 'utf-8');

        for ($counter = 0; $counter < $length; ++$counter) {
            $str .= $char;
        }

        return $str;
    }

    /**
     * 敏感词检测
     * @param $str
     * @return bool
     */
    public static function isSensitive($content = '')
    {
      //  $bad_word = self::getList();
        $contentLength = mb_strlen($content, 'utf-8');
        for ($length = 0; $length < $contentLength; $length++) {
            $matchFlag = 0;
            $tempMap = self::getList();
            for ($i = $length; $i < $contentLength; $i++) {
                $keyChar = mb_substr($content, $i, 1, 'utf-8');

                // 检测干扰因子
                if (self::checkInterferenceFactor($keyChar)) {
                    $matchFlag++;
                    continue;
                }

                // 获取指定节点树
                $nowMap = $tempMap->get($keyChar);

                // 不存在节点树，直接返回
                if (empty($nowMap)) {
                    break;
                }

                // 找到相应key，偏移量+1
                $tempMap = $nowMap;
                $matchFlag++;

                // 如果为最后一个匹配规则,结束循环，返回匹配标识数
                if ($nowMap->get('isEnd') === false) {
                    continue;
                }

                return true;
            }

            // 找到相应key
            if ($matchFlag <= 0) {
                continue;
            }

            // 需匹配内容标志位往后移
            $length += $matchFlag - 1;
        }
        return false;
    }

    /**
     * 构建敏感词
     * @param $word
     * @param $tree
     */
    public static function buildWordToTree( $word, &$hasMap)
    {
        if ($word === '') {
            return;
        }
        $wordLength = mb_strlen($word, 'utf-8');
        $tree = $hasMap;
        for ($i = 0; $i < $wordLength; $i++) {
            $keyChar = mb_substr($word, $i, 1, 'utf-8');

            // 获取子节点树结构
            $tempTree = $tree->get($keyChar);

            if ($tempTree) {
                $tree = $tempTree;
            } else {
                // 设置标志位
                $newTree = new hashMap;
                $newTree->put('isEnd', false);

                // 添加到集合
                $tree->put($keyChar, $newTree);
                $tree = $newTree;
            }

            // 到达最后一个节点
            if ($i == $wordLength - 1) {
                $tree->put('isEnd', true);
            }
        }

        return;
    }

    /**
     * 获取敏感词列表
     * @return mixed
     */
    public static function getList()
    {
        return Cache::rememberForever('shopwwiSensitiveWord',function (){
            $list = SysSensitives::where('status',1)->get();
            $hashMap = new HashMap;
            foreach ($list as $item){
                self::buildWordToTree(trim($item->word),$hashMap);
            }
            return $hashMap;
        });
    }

    /**
     * 清理敏感词缓存
     * @return void
     */
    public static function clear()
    {
        Cache::forget("shopwwiSensitiveWord");
    }
}