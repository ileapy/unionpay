<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/18 9:02
 * Copyright: php
 */

namespace unionpay\Kernel\Support;

/**
 * Class AcpService
 *
 * @package unionpay\Kernel\Support
 */
class AcpService
{
    /**
     * 取得备份文件名
     * @param string $path 文件地址
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 10:33
     */
    public static function getBackupFileName($path)
    {
        $i = strrpos($path, ".");
        $leftFileName = substr($path, 0, $i);
        $rightFileName = substr($path, $i + 1);
        return $leftFileName . '_backup.' . $rightFileName;
    }

    /**
     * 对数组排序
     * @param array $params
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 10:49
     */
    public static function argSort($params)
    {
        ksort ($params);
        reset ($params);
        return $params;
    }

    /**
     * 将数组转换为 string
     * @param array $params 待转换数组
     * @param bool $sort 是否需要排序
     * @param bool $encode 是否需要URL编码
     * @return string
     */
    public static function createLinkString($params, $sort, $encode)
    {
        if($params == NULL || !is_array($params)) return "";
        $linkString = "";
        if ($sort) $params = self::argSort($params);
        while (list($key, $value) = each($params))
        {
            if ($encode) $value = urlencode($value);
            $linkString .= $key . "=" . $value . "&";
        }
        return substr($linkString,0,-1);
    }

    /**
     * @param $temp
     * @param $isKey
     * @param $key
     * @param $result
     * @param $needUrlDecode
     * @return false|void
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:27
     */
    static function putKeyValueToDictionary($temp, $isKey, $key, &$result, $needUrlDecode)
    {
        if ($isKey) {
            $key = $temp;
            if (strlen ( $key ) == 0) {
                return false;
            }
            $result [$key] = "";
        } else {
            if (strlen ( $key ) == 0) {
                return false;
            }
            if ($needUrlDecode)
                $result [$key] = urldecode ( $temp );
            else
                $result [$key] = $temp;
        }
    }

    /**
     * key1=value1&key2=value2转array
     * @param string $str key1=value1&key2=value2的字符串
     * @param bool $needUrlDecode 是否需要解url编码，默认不需要
     * @return array
     */
    static function parseQString($str, $needUrlDecode = false)
    {
        $result = array();
        $len = strlen($str);
        $temp = "";
        $key = "";
        $isKey = true;
        $isOpen = false;
        $openName = "\0";
        for($i=0; $i<$len; $i++){
            $curChar = $str[$i];
            if($isOpen){
                if($curChar == $openName){
                    $isOpen = false;
                }
                $temp .= $curChar;
            } elseif ($curChar == "{"){
                $isOpen = true;
                $openName = "}";
                $temp .= $curChar;
            } elseif ($curChar == "["){
                $isOpen = true;
                $openName = "]";
                $temp .= $curChar;
            } elseif ($isKey && $curChar == "="){
                $key = $temp;
                $temp = "";
                $isKey = false;
            } elseif ($curChar == "&"){
                self::putKeyValueToDictionary($temp, $isKey, $key, $result, $needUrlDecode);
                $temp = "";
                $isKey = true;
            } else {
                $temp .= $curChar;
            }
        }
        self::putKeyValueToDictionary($temp, $isKey, $key, $result, $needUrlDecode);
        return $result;
    }

    /**
     * 更新证书
     * @param array $params 报文参数
     * @param string $encryptCertPath 加密证书路径
     * @return int
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 10:35
     * @throws \Exception
     */
    public static function updateEncryptCert(&$params, $encryptCertPath)
    {
        // 取得证书
        $strCert = $params['encryptPubKeyCert'];
        $certType = $params['certType'];
        openssl_x509_read($strCert);
        $certInfo = openssl_x509_parse($strCert);
        if($certType === "01")
        {
            // 更新敏感信息加密公钥
            if (CertUtil::getEncryptCertId($encryptCertPath) != $certInfo['serialNumber'])
            {
                $newFileName = self::getBackupFileName($encryptCertPath);
                // 将原证书备份重命名 备份失败返回 -1
                if(!copy($encryptCertPath, $newFileName)) return -1;
                // 更新证书 失败返回 -1
                if(!file_put_contents($encryptCertPath, $strCert)) return -1;
                // 更新成功
                return 1;
            }
            // 无需更新
            return 0;
        } else if($certType === "02")
        {
            // 无需更新
            return 0;
        } else
        {
            // 未知证书状态
            return -1;
        }
    }

    /**
     * 签名
     * @param array $params 待签名数据
     * @param string $signCertPath 签名证书路径
     * @param string $signCertPwd 签名证书密码
     * @param string $secureKey 使用密钥签名
     * @return bool
     * @throws \Exception
     */
    static function sign(&$params, $signCertPath = "", $signCertPwd = "", $secureKey = "")
    {
        return $params['signMethod'] =='01' ?
            AcpService::signByCertInfo($params, $signCertPath, $signCertPwd) :
            AcpService::signBySecureKey($params, $secureKey);
    }

    /**
     * 证书加签
     * @param array $params 待签名数据
     * @param string $certPath 签名证书路径
     * @param string $certPwd 签名证书密码
     * @return bool
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 10:47
     */
    static function signByCertInfo(&$params, $certPath, $certPwd)
    {
        if(isset($params['signature'])) unset($params['signature']);
        if($params['signMethod']=='01')
        {
            //证书ID
            $params['certId'] = CertUtil::getSignCertIdFromPfx($certPath, $certPwd);
            $private_key = CertUtil::getSignKeyFromPfx($certPath, $certPwd );
            // 转换成key=val&串
            $params_str = self::createLinkString($params, true, false);
            if($params['version']=='5.0.0')
            {
                $params_sha1x16 = sha1($params_str,FALSE);
                // 签名
                $result = openssl_sign($params_sha1x16,$signature, $private_key,OPENSSL_ALGO_SHA1);
                if ($result)
                {
                    $signature_base64 = base64_encode($signature);
                    $params['signature'] = $signature_base64;
                    return true;
                }
            } else if($params['version']=='5.1.0')
            {
                //sha256签名摘要
                $params_sha256x16 = hash('sha256', $params_str);
                // 签名
                $result = openssl_sign($params_sha256x16,$signature, $private_key,'sha256');
                if ($result)
                {
                    $signature_base64 = base64_encode($signature);
                    $params['signature'] = $signature_base64;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 密钥加签
     * @param array $params 待签名数据
     * @param string $secureKey 签名证书路径
     * @return bool
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 10:58
     */
    static function signBySecureKey(&$params, $secureKey)
    {
        if($secureKey == null || trim($secureKey) == '') throw new \Exception('未配置密钥，签名失败');

        if($params['signMethod']=='11')
        {
            // 转换成key=val&串
            $params_str = self::createLinkString($params,true,false);
            $params_before_sha256 = hash('sha256', $secureKey);
            $params_before_sha256 = $params_str.'&'.$params_before_sha256;
            $params_after_sha256 = hash('sha256',$params_before_sha256);
            $params ['signature'] = $params_after_sha256;
            return true;
        } else if($params['signMethod']=='12') {
            //TODO SM3
            throw new \Exception('signMethod=12未实现');
        } else {
            throw new \Exception("signMethod不正确");
        }
    }

    /**
     * 验签
     * @param array $params 验签报文数据
     * @param string $certDir 证书目录
     * @param string $middleCertPath 验签中级证书
     * @param string $rootCertPath
     * @param bool $ifValidateCNName 是否验证验签证书的CN
     * @param string $secureKey 密钥
     * @return bool|int
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:22
     * @throws \Exception
     */
    static function validate($params, $certDir = "", $middleCertPath = "", $rootCertPath = "", $ifValidateCNName = true, $secureKey = "")
    {
        $signature_str = $params['signature'];
        unset($params['signature']);
        if($params['signMethod']=='01')
        {
            $params_str = self::createLinkString($params,true,false );
            if($params['version']=='5.0.0')
            {
                // 公钥
                $public_key = CertUtil::getVerifyCertByCertId($params['certId'], $certDir);
                $signature = base64_decode($signature_str);
                $params_sha1x16 = sha1($params_str,FALSE);
                $isSuccess = openssl_verify($params_sha1x16, $signature, $public_key,OPENSSL_ALGO_SHA1);
            } else if($params['version']=='5.1.0')
            {
                $strCert = $params['signPubKeyCert'];
                $strCert = CertUtil::verifyAndGetVerifyCert($strCert, $middleCertPath, $rootCertPath, $ifValidateCNName);
                if($strCert == null) throw new \Exception("validate cert err: " . $params["signPubKeyCert"]);
                else
                {
                    $params_sha256x16 = hash('sha256', $params_str);
                    $signature = base64_decode($signature_str);
                    $isSuccess = openssl_verify($params_sha256x16, $signature, $strCert,"sha256");
                }
            } else {
                $isSuccess = false;
            }
        } else {
            $isSuccess = AcpService::validateBySecureKey($params, $secureKey);
        }
        return $isSuccess;
    }

    /**
     * @param array $params
     * @param string $secureKey
     * @return bool
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:24
     */
    static function validateBySecureKey($params, $secureKey)
    {
        if($secureKey == null || trim($secureKey) == '') throw new \Exception('密钥没配，验签失败');
        $signature_str = $params['signature'];
        unset($params['signature']);
        $params_str = self::createLinkString($params,true,false );
        if($params['signMethod']=='11')
        {
            $params_before_sha256 = hash('sha256', $secureKey);
            $params_before_sha256 = $params_str.'&'.$params_before_sha256;
            $params_after_sha256 = hash('sha256',$params_before_sha256);
            return $params_after_sha256 == $signature_str;
        } else if($params['signMethod']=='12') {
            //TODO SM3
            throw new \Exception('signMethod=12未实现');
        } else {
            throw new \Exception("signMethod不正确");
        }
    }

    /**
     * @param string $jsonData json格式数据，例如：{"sign" : "J6rPLClQ64szrdXCOtV1ccOMzUmpiOKllp9cseBuRqJ71pBKPPkZ1FallzW18gyP7CvKh1RxfNNJ66AyXNMFJi1OSOsteAAFjF5GZp0Xsfm3LeHaN3j/N7p86k3B1GrSPvSnSw1LqnYuIBmebBkC1OD0Qi7qaYUJosyA1E8Ld8oGRZT5RR2gLGBoiAVraDiz9sci5zwQcLtmfpT5KFk/eTy4+W9SsC0M/2sVj43R9ePENlEvF8UpmZBqakyg5FO8+JMBz3kZ4fwnutI5pWPdYIWdVrloBpOa+N4pzhVRKD4eWJ0CoiD+joMS7+C0aPIEymYFLBNYQCjM0KV7N726LA==",  "data" : "pay_result=success&tn=201602141008032671528&cert_id=68759585097"}
     * @param string $certDir 证书目录
     * @throws \Exception
     * @deprecated 5.1.0开发包已删除此方法，请直接参考5.1.0开发包中的VerifyAppData.php验签 对控件支付成功返回的结果信息中data域进行验签
     * @return false|int
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:32
     * @throws \Exception
     */
    static function validateAppResponse($jsonData, $certDir)
    {
        $data = json_decode($jsonData);
        $sign = $data->sign;
        $data = $data->data;
        $dataMap = self::parseQString($data);
        $public_key = CertUtil::getVerifyCertByCertId($dataMap['cert_id'], $certDir);
        $signature = base64_decode($sign);
        $params_sha1x16 = sha1($data,FALSE );
        return openssl_verify($params_sha1x16, $signature, $public_key,OPENSSL_ALGO_SHA1);
    }

    /**
     * @param array $params 参数
     * @param string $reqUrl 提交地址
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:34
     */
    static function createAutoFormHtml($params, $reqUrl)
    {
        $encodeType = isset($params['encoding']) ? $params['encoding'] : 'UTF-8';
        $html = <<<eot
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={$encodeType}" />
</head>
<body onload="javascript:document.pay_form.submit();">
    <form id="pay_form" name="pay_form" action="{$reqUrl}" method="post">
	
eot;
        foreach ($params as $key => $value)
        {
            $html .= "    <input type=\"hidden\" name=\"{$key}\" id=\"{$key}\" value=\"{$value}\" />\n";
        }
        $html .= <<<eot
    </form>
</body>
</html>
eot;
        return $html;
    }

    /**
     * @param array $customerInfo
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:35
     */
    static function getCustomerInfo($customerInfo)
    {
        if($customerInfo == null || count($customerInfo) == 0) return "";
        return base64_encode("{" . self::createLinkString($customerInfo,false,false ) . "}");
    }

    /**
     * map转换string，敏感信息加密
     * @param array $customerInfo
     * @param string $encryptCertPath
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:36
     * @throws \Exception
     */
    static function getCustomerInfoWithEncrypt($customerInfo, $encryptCertPath)
    {
        if($customerInfo == null || count($customerInfo) == 0) return "";
        $encryptedInfo = array();
        foreach ($customerInfo as $key => $value)
        {
            if ($key == 'phoneNo' || $key == 'cvn2' || $key == 'expired')
            {
                $encryptedInfo[$key] = $value;
                unset ($customerInfo[$key]);
            }
        }
        if(count($encryptedInfo) > 0)
        {
            $encryptedInfo = self::createLinkString($encryptedInfo,false,false);
            $encryptedInfo = AcpService::encryptData($encryptedInfo, $encryptCertPath);
            $customerInfo['encryptedInfo'] = $encryptedInfo;
        }
        return base64_encode("{" . self::createLinkString($customerInfo,false,false) . "}");
    }

    /**
     * 解析customerInfo。 为方便处理，encryptedInfo下面的信息也均转换为customerInfo子域一样方式处理，
     * @param string $customerInfostr
     * @return array 形式ParseCustomerInfo
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:39
     * @throws \Exception
     */
    static function parseCustomerInfo($customerInfostr, $certPath, $certPwd)
    {
        $customerInfostr = base64_decode($customerInfostr);
        $customerInfostr = substr($customerInfostr,1,strlen($customerInfostr) - 2);
        $customerInfo = self::parseQString($customerInfostr);
        if(array_key_exists("encryptedInfo", $customerInfo))
        {
            $encryptedInfoStr = $customerInfo["encryptedInfo"];
            unset($customerInfo["encryptedInfo"] );
            $encryptedInfoStr = AcpService::decryptData($encryptedInfoStr, $certPath, $certPwd);
            $encryptedInfo = self::parseQString($encryptedInfoStr);
            foreach ($encryptedInfo as $key => $value) $customerInfo[$key] = $value;
        }
        return $customerInfo;
    }

    /**
     * @param string $encryptCertPath
     * @return false
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:42
     * @throws \Exception
     */
    static function getEncryptCertId($encryptCertPath)
    {
        return CertUtil::getEncryptCertId($encryptCertPath);
    }

    /**
     * 加密数据
     * @param string $data 待加密数据
     * @param string $certPath 加密证书配置路径
     * @return string
     * @throws \Exception
     */
    static function encryptData($data, $certPath)
    {
        $public_key = CertUtil::getEncryptKey($certPath);
        openssl_public_encrypt($data,$crypted, $public_key);
        return base64_encode ($crypted);
    }

    /**
     * 解密数据
     * @param $data 待解密数据
     * @param $certPath 签名证书路径
     * @param $certPwd 签名证书密码
     * @return mixed
     * @throws \Exception
     */
    static function decryptData($data, $certPath, $certPwd)
    {
        $data = base64_decode($data);
        $private_key = CertUtil::getSignKeyFromPfx($certPath, $certPwd);
        openssl_private_decrypt($data,$crypted, $private_key);
        return $crypted;
    }

    /**
     * 报文中的文件保存
     * @param $params 原始报文
     * @param $fileDirectory 要保存的文件路径
     * @return bool
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:17
     * @throws \Exception
     */
    static function decodeFileContent($params, $fileDirectory)
    {
        if (!isset($params['fileContent'])) throw new \Exception('文件内容为空');
        $fileContent = $params ['fileContent'];
        if (empty($fileContent)) throw new \Exception('文件内容为空');
        // 解压缩
        $content = gzuncompress(base64_decode($fileContent));
        if (empty($params['fileName']))
        {
            $filePath = $fileDirectory . $params ['merId'] . '_' . $params ['batchNo'] . '_' . $params ['txnTime'] . '.txt';
        } else {
            $filePath = $fileDirectory . $params ['fileName'];
        }
        $handle = fopen($filePath, "w+");
        if (!is_writable($filePath)) throw new \Exception("文件:" . $filePath . "不可写，请检查！");
        file_put_contents ($filePath, $content);
        fclose ($handle);
        return true;
    }

    /**
     * 功能：将批量文件内容使用DEFLATE压缩算法压缩，Base64编码生成字符串并返回
     * 适用到的交易：批量代付，批量代收，批量退货
     * @param $path 文件路径
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:09
     * @throws \Exception
     */
    static function encodeFileContent($path)
    {
        if(!file_exists($path)) throw new \Exception('文件不存在');
        $file_content = file_get_contents ($path);
        //UTF8 去掉文本中的 bom头
        $BOM = chr(239).chr(187).chr(191);
        $file_content = str_replace($BOM,'',$file_content);
        $file_content_deflate = gzcompress($file_content);
        return base64_encode ($file_content_deflate);
    }
}
