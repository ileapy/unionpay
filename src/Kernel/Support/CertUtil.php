<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/18 9:21
 * Copyright: php
 */

namespace unionpay\Kernel\Support;

/**
 * Class CertUtil
 *
 * @package unionpay\Kernel\Support
 */
class CertUtil
{
    /**
     * @var array
     */
    private static $signCerts = array();

    /**
     * @var array
     */
    private static $encryptCerts = array();

    /**
     * @var array
     */
    private static $verifyCerts = array();

    /**
     * @var array
     */
    private static $verifyCerts510 = array();

    /**
     * @var string
     */
    private static $company = "中国银联股份有限公司";

    /**
     * @param string $certPath 签名证书地址
     * @param string $certPwd 签名证书密码
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:25
     */
    private static function initSignCert($certPath, $certPwd)
    {
        // 读取文件
        $pkcs12certdata = file_get_contents($certPath);
        if($pkcs12certdata === false ) throw new \Exception($certPath . " file_get_contents fail。");
        // 签名证书密码
        if(openssl_pkcs12_read($pkcs12certdata,$certs, $certPwd) == FALSE ) throw new \Exception($certPath . ", pwd[" . $certPwd . "] openssl_pkcs12_read fail。");

        $cert = new Cert();
        $x509data = $certs['cert'];

        if(!openssl_x509_read($x509data)) throw new \Exception($certPath . " openssl_x509_read fail。");
        // 解析
        $certdata = openssl_x509_parse($x509data);
        // 读取序列号
        $cert->certId = $certdata['serialNumber'];

        $cert->key = $certs['pkey'];
        $cert->cert = $x509data;

        CertUtil::$signCerts[$certPath] = $cert;
        return true;
    }

    /**
     * @param string $certPath 签名证书地址
     * @param string $certPwd 签名证书密码
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:31
     * @throws \Exception
     */
    public static function getSignKeyFromPfx($certPath, $certPwd)
    {
        if (!array_key_exists($certPath, CertUtil::$signCerts)) self::initSignCert($certPath, $certPwd);
        return CertUtil::$signCerts[$certPath]->key;
    }

    /**
     * @param string $certPath 证书地址
     * @param string $certPwd 证书密码
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:34
     * @throws \Exception
     */
    public static function getSignCertIdFromPfx($certPath, $certPwd)
    {
        if (!array_key_exists($certPath, CertUtil::$signCerts)) self::initSignCert($certPath, $certPwd);
        return CertUtil::$signCerts[$certPath] -> certId;
    }

    /**
     * 初始化加密证书
     * @param string $certPath 加密证书地址
     * @return bool
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:39
     * @throws \Exception
     */
    private static function initEncryptCert($certPath)
    {
        $x509data = file_get_contents($certPath);
        if($x509data === false) throw new \Exception($certPath . " file_get_contents fail。");

        if(!openssl_x509_read($x509data)) throw new \Exception($certPath . " openssl_x509_read fail。");

        $cert = new Cert();
        $certdata = openssl_x509_parse($x509data);
        $cert->certId = $certdata['serialNumber'];

        $cert->key = $x509data;
        CertUtil::$encryptCerts[$certPath] = $cert;
        return true;
    }

    /**
     * @param string $certBase64String 验签根证书
     * @param string $middleCertPath 验签中级证书
     * @param string $rootCertPath
     * @param bool $ifValidateCNName 是否验证验签证书的CN
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:53
     * @throws \Exception
     */
    public static function verifyAndGetVerifyCert($certBase64String, $middleCertPath, $rootCertPath, $ifValidateCNName = true)
    {
        // 存在直接返回
        if (array_key_exists($certBase64String, CertUtil::$verifyCerts510)) return CertUtil::$verifyCerts510[$certBase64String];

        // 验签中级证书 验签根证书
        if ($middleCertPath === null || $rootCertPath === null) throw new \Exception("rootCertPath or middleCertPath is none, exit initRootCert");

        openssl_x509_read($certBase64String);
        $certInfo = openssl_x509_parse($certBase64String);

        $cn = CertUtil::getIdentitiesFromCertficate($certInfo);

        // 是否验证验签证书的CN，测试环境请设置false，生产环境请设置true。非false的值默认都当true处理。
        if ($ifValidateCNName)
        {
            if (self::$company != $cn)
                throw new \Exception("cer owner is not CUP:" . $cn);
            elseif (self::$company != $cn && "00040000:SIGN" != $cn)
                throw new \Exception("cer owner is not CUP:" . $cn);
        }

        $from = date_create('@' . $certInfo ['validFrom_time_t']);
        $to = date_create('@' . $certInfo ['validTo_time_t']);
        $now = date_create(date('Ymd'));

        $interval1 = $from->diff($now);
        $interval2 = $now->diff($to);

        if ($interval1->invert || $interval2->invert) throw new \Exception("signPubKeyCert has expired");

        $result = openssl_x509_checkpurpose($certBase64String, X509_PURPOSE_ANY, array($rootCertPath, $middleCertPath));

        if ($result === TRUE)
        {
            CertUtil::$verifyCerts510[$certBase64String] = $certBase64String;
            return CertUtil::$verifyCerts510[$certBase64String];
        } elseif ($result === FALSE)
        {
            throw new \Exception("validate signPubKeyCert by rootCert failed");
        } else
        {
            throw new \Exception("validate signPubKeyCert by rootCert failed with error");
        }
    }

    /**
     * @param array $certInfo
     * @return mixed|string|null
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:43
     */
    public static function getIdentitiesFromCertficate($certInfo)
    {
        $cn = $certInfo['subject'];
        $cn = $cn['CN'];
        $company = explode('@',$cn);
        if(count($company) < 3) return null;
        return $company[2];
    }

    /**
     * @param string $certPath 加密证书路径
     * @return false
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:57
     * @throws \Exception
     */
    public static function getEncryptCertId($certPath)
    {
        if(!array_key_exists($certPath, CertUtil::$encryptCerts)) self::initEncryptCert($certPath);
        if(array_key_exists($certPath, CertUtil::$encryptCerts)) return CertUtil::$encryptCerts[$certPath]->certId;
        return false;
    }

    /**
     * @param string $certPath 加密证书地址
     * @return false
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 9:59
     * @throws \Exception
     */
    public static function getEncryptKey($certPath)
    {
        if(!array_key_exists($certPath, CertUtil::$encryptCerts)) self::initEncryptCert($certPath);
        if(array_key_exists($certPath, CertUtil::$encryptCerts)) return CertUtil::$encryptCerts[$certPath]->key;
        return false;
    }

    /**
     * 初始化验签证书
     * @param string $certDir 验签证书路径
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 10:07
     * @throws \Exception
     */
    private static function initVerifyCerts($certDir)
    {
        $handle = opendir ($certDir);
        if (!$handle) throw new \Exception('证书目录 ' . $certDir . '不正确');

        while ($file = readdir($handle))
        {
            clearstatcache();
            $filePath = $certDir . '/' . $file;
            if (is_file($filePath))
            {
                if (pathinfo($file,PATHINFO_EXTENSION) == 'cer')
                {
                    $x509data = file_get_contents($filePath);
                    if($x509data === false) continue;
                    if(!openssl_x509_read($x509data)) continue;

                    $cert = new Cert();
                    $certdata = openssl_x509_parse($x509data);
                    $cert->certId = $certdata ['serialNumber'];

                    $cert->key = $x509data;
                    CertUtil::$verifyCerts[$cert->certId] = $cert;
                }
            }
        }
        closedir ( $handle );
    }

    /**
     * 匹配某序列号的证书
     * @param string $certId 证书序列号
     * @param string $certDir 证书目录
     * @return false
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 10:09
     * @throws \Exception
     */
    public static function getVerifyCertByCertId($certId, $certDir)
    {
        if(count(CertUtil::$verifyCerts) == 0) self::initVerifyCerts($certDir);
        if(count(CertUtil::$verifyCerts) == 0) throw new \Exception("未读取到任何证书……");

        if(array_key_exists($certId, CertUtil::$verifyCerts)) return CertUtil::$verifyCerts[$certId]->key;

        return false;
    }
}
