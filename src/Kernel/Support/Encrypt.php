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
        $key = pack("H48", $key);
        $input = self::pkcs5Pad($input);
        $data = openssl_encrypt($input,'DES-EDE3',$key,OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,'');
        return self::removeBR(base64_encode($data));
    }

    /**
     * @param $text
     * @return string
     * Author cfn <cfn@leapy.cn>
     * Date 2022/1/19
     */
    private static function pkcs5Pad($text) {
        $pad = 8 - (strlen($text) % 8);
        $input = $text . str_repeat(chr($pad), $pad);
        if (strlen($input) % 8) {
            $input = str_pad($input,
                strlen($input) + 8 - strlen($input) % 8, "\0");
        }
        return $input;
    }

    /**
     * 解密
     * @param $encrypted
     * @param $key
     * @return false|string
     */
    public static function decrypt3DES($encrypted, $key)
    {
        $key = pack("H48", $key);
        $decrypted = openssl_decrypt(base64_decode($encrypted),'DES-EDE3',$key,OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,'');
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
     * @param string signKey 私钥加密
     * @return string 签名结果字符串
     */
    public static function sign($params, $signKey)
    {
        openssl_sign(Str::sortByASCII($params), $binary_signature, $signKey, "SHA256");
        return base64_encode($binary_signature);
    }

    /**
     * 银联公钥验签
     * @param array $data 原始数据
     * @param string $publicKey 银联公钥
     * @return bool 验签结果
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 19:13
     */
    public static function verify($data, $publicKey = "")
    {
        $signature = $data['signature'];
        unset($data['signature']);
        $res = openssl_get_publickey($publicKey);
        $result = (bool)openssl_verify(Str::sortByASCII($data), base64_decode($signature), $res,'SHA256');
        openssl_free_key($res);
        return $result;
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
