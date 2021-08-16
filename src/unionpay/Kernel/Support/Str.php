<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 10:22
 * Copyright: php
 */

namespace unionpay\Kernel\Support;

class Str
{
    /**
     * 拼接待签名字符串 按照ASCII从小到大 得到 string
     * @param array $data
     * @return string
     */
    public static function sortByASCII(array $data)
    {
        $strs = [];
        // 取出数组的下标
        $keys = array_keys($data);
        // 对下标进行排序
        asort($keys);
        // 对二维数组按照下标的ASCII从小到大拼接成一维数组
        foreach ($keys as $v) $strs[] = $v."=".$data[$v];
        // 把一维数组用&转换成字符串返回
        return implode("&",$strs);
    }

    /**
     * 对待签名字符串进行 SHA256 签名，得到 signature
     * @param string $data 待签名字符
     * @param bool $rawOutput
     * @return bool|string
     */
    public static function sha256($data, $rawOutput = false)
    {
        if (!is_scalar($data)) return false;
        $data = (string)$data;
        $rawOutput = !!$rawOutput;
        return hash('sha256', $data, $rawOutput);
    }

    /**
     * 生成签名随机字符串 nonceStr
     * @param int $length 生成的随机字符串长度
     * @return string|null
     */
    public static function nonceStr($length = 16)
    {
        $str = null;
        $strPol ="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0; $i<$length; $i++){
            $str .= $strPol[rand(0,$max)];
        }
        return $str;
    }

    /**
     * @param $appId
     * @param $nonceStr
     * @param $secret
     * @param $timestamp
     * @return bool|string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:28
     */
    public static function signature($appId , $nonceStr, $secret, $timestamp)
    {
        return self::sha256(self::sortByASCII(compact('appId','nonceStr','secret','timestamp')));
    }
}
