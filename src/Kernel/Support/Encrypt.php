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
class Encrypt
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
        $decrypted = @mdecrypt_generic($td, $encrypted);
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

    /**
     * 签名
     * @param array param 待签名的参数
     * @param string signKey 签名密钥
     * @return string 签名结果字符串
     */
    public static function sign($params, $signKey)
    {
        openssl_sign(Str::sortByASCII($params), $binary_signature, $signKey, "SHA256");
        return base64_encode($binary_signature);
    }

    /**
     * 对字段批量签名
     * @param string symmetricKey 签名秘钥
     * @param array params 待加密数据
     * @field 要加密的字段
     */
    public static function encryptedParamMap($symmetricKey, $params, $field)
    {
        foreach ($field as $k => $v) $params[$v] = self::encrypt3DES($params[$v], $symmetricKey);
        return $params;
    }

    /**
     * 使用公钥加密对称密钥
     * @param string $publicKey 公钥
     * @param string $symmetricKey 对称密钥字节
     * @return false|string 加密后的对称密钥字节
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 19:05
     */
    public function encrypt($publicKey, $symmetricKey)
    {
        $key = openssl_pkey_get_public($publicKey);
        if ($key === false) return false;
        $return_en = openssl_public_encrypt($symmetricKey, $crypted, $key);
        if (!$return_en) return false;
        return base64_encode($crypted);
    }

    /**
     * 使用私钥解密对称密钥
     * @param string $privateKey 私钥
     * @param string $symmetricKey 对称密钥字节
     * @return false|mixed|void
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 19:06
     */
    public static function decrypt($privateKey, $symmetricKey)
    {
        $key = openssl_pkey_get_private($privateKey);
        if ($key === false) return false;
        $return_en = openssl_private_decrypt(base64_decode($symmetricKey), $decrypted, $key);
        if (!$return_en) return false;
        return $decrypted;
    }
}
