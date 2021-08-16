<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 20:19
 * Copyright: php
 */

namespace unionpay\Kernel\Support;

/**
 * Class TripleEncrypt
 *
 * @package unionpay\Kernel\Support
 */
class TripleEncrypt
{
    /**
     * 加密
     * @param $input
     * @param $key
     * @param string $iv
     * @return string
     */
    public static function encrypt3DES($input, $key, $iv = "12345678910111213")
    {
        $key = pack("H*", $key);
        $iv = pack("H*", $iv);
        $input = self::addPKCS7Padding($input);
        $td = @mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        @mcrypt_generic_init($td, $key, $iv);
        $data = @mcrypt_generic($td, $input);
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);
        //$data= iconv("UTF-8","GB2312//IGNORE",$data);
        return self::removeBR(base64_encode($data));
    }

    /**
     * 加密
     * @param $source
     * @return string
     */
    private static function addPKCS7Padding($source)
    {
        $block = @mcrypt_get_block_size('tripledes', 'cbc');
        $pad = $block - (strlen($source) % $block);
        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }
        return $source;
    }

    /**
     * 解密
     * @param $encrypted
     * @param $key
     * @param string $iv
     * @return false|string
     */
    public static function decrypt3DES($encrypted, $key, $iv = "12345678910111213")
    {
        $key = pack("H48", $key);
        $iv = pack("H16", $iv);
        $encrypted = base64_decode($encrypted);
        $td = @mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        @mcrypt_generic_deinit($td);
        @mcrypt_module_close($td);
        return self::stripPKSC5Padding($decrypted);
    }

    /**
     * @param $source
     * @return false|string
     */
    public static function stripPKSC5Padding($source)
    {
        $char = substr($source, -1, 1);
        $num = ord($char);
        if ($num > 8) {
            return $source;
        }
        $len = strlen($source);
        for ($i = $len - 1; $i >= $len - $num; $i--) {
            if (ord(substr($source, $i, 1)) != $num) {
                return $source;
            }
        }
        return substr($source, 0, -$num);
    }

    /**
     * @param $str
     * @return string
     */
    public static function removeBR($str)
    {
        $len = strlen($str);
        $newstr = "";
        $str = str_split($str);
        for ($i = 0; $i < $len; $i++) {
            if ($str[$i] != '\n' and $str[$i] != '\r') {
                $newstr .= $str[$i];
            }
        }
        return $newstr;
    }
}
