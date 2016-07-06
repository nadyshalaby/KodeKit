<?php

/**
 * This file is part of kodekit framework
 * 
 * @copyright (c) 2015-2016, nady shalaby
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Libs\Statics;

use Locale;

abstract class Request {

    public static function isPost() {
        return ($_POST && !self::hasParam('_method')) ? true : false;
    }

    public static function isGet() {
        return ($_GET && !self::hasParam('_method')) ? true : false;
    }

    public static function isPut() {
        return (self::isPost() && self::getParam('_method') === 'PUT') ? true : false;
    }

    public static function isDelete() {
        return (self::isPost() && self::getParam('_method') === 'DELETE') ? true : false;
    }

    public static function getParam($name) {
        return (self::hasParam($name)) ? ($_REQUEST[$name]) : null;
    }

    public static function hasParam($name) {
        return (isset($_REQUEST[$name]));
    }

    public static function getParamNames() {
        return array_keys($_REQUEST);
    }

    public static function getParamValues() {
        return array_values($_REQUEST);
    }

    public static function getALlParams() {
        return $_REQUEST;
    }

    public static function removeParam($name) {
        unset($_REQUEST[$name]);
    }

    public static function appendParam($name, $value) {
        $_REQUEST[$name] = $value;
    }

    public static function getFile($name, $as_arr = false) {
        if (self::hasFile($name)) {
            return (!$as_arr) ? arr2obg($_FILES[$name]) : $_FILES[$name];
        }
        return null;
    }

    public static function getFiles($as_arr = false) {
        if ($_FILES) {
            return (!$as_arr) ? arr2obg($_FILES) : $_FILES;
        }
        return null;
    }

    public static function hasFile($name) {
        return (isset($_FILES[$name]) && $_FILES[$name]['error'] == 0);
    }

    public static function getPageUrl() {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getFullUrl($use_forwarded_host = false) {
        return self::getUrlOrigin($_SERVER, $use_forwarded_host) . $_SERVER['REQUEST_URI'];
    }

    public static function getUrlOrigin($use_forwarded_host = false) {
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' );
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . ( ( $ssl ) ? 's' : '' );
        $port = $_SERVER['SERVER_PORT'];
        $port = ( (!$ssl && $port == '80' ) || ( $ssl && $port == '443' ) ) ? '' : ':' . $port;
        $host = ( $use_forwarded_host && isset($_SERVER['HTTP_X_FORWARDED_HOST']) ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : ( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null );
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    /**
     * Returns the name of the host getServer (such as www.w3schools.com)
     * @return (type) (description)
     */
    public static function getServerName() {
        return self::SERVER('SERVER_NAME');
    }

    /**
     * Returns the IP address of the host getServer
     * @return (type) (description)
     */
    public static function getServerIp() {
        return self::SERVER('SERVER_ADDR');
    }

    public static function getServerPort() {
        return self::SERVER('SERVER_PORT');
    }

    /**
     * Returns the getServer identification string (such as Apache/2.2.24)
     * @return (type) (description)
     */
    public static function getServerSoftware() {
        return self::SERVER('SERVER_SOFTWARE');
    }

    public static function getServerAdmin() {
        return self::SERVER('SERVER_ADMIN');
    }

    /**
     * Returns the name and revision of the information protocol (such as HTTP/1.1)
     * @return (type) (description)
     */
    public static function getServerProtocol() {
        return self::SERVER('SERVER_PROTOCOL');
    }

    /**
     * Returns the filename of the currently executing script
     * @return (type) (description)
     */
    public static function fileName() {
        return self::SERVER('PHP_SELF');
    }

    public static function getMethod() {
        return self::SERVER('REQUEST_METHOD');
    }

    /**
     * Returns the timestamp of the start of the getRequest (such as 1377687496)
     * @return (type) (description)
     */
    public static function getRequestTimestamp() {
        return self::SERVER('REQUEST_TIME');
    }

    public static function getRequestLocale() {
        return Locale::acceptFromHttp(self::SERVER('HTTP_ACCEPT_LANGUAGE'));
    }

    /**
     * Returns the query string if the page is accessed via a query string
     * @return (type) (description)
     */
    public static function getRequestQueryString() {
        return self::SERVER('QUERY_STRING');
    }

    /**
     * Returns the Accept_Charset header from the current getRequest (such as utf-8,ISO-8859-1)
     * @return (type) (description)
     */
    public static function getRequestCharset() {
        return self::SERVER('HTTP_ACCEPT_CHARSET');
    }

    /**
     * Returns the complete URL of the current page (not reliable because not all getClient-agents
     * @return (type) (description)
     */
    public static function getPrevUrl() {
        return (self::SERVER('HTTP_REFERER')) ? self::SERVER('HTTP_REFERER') : Url::app();
    }

    public static function getClientIP() {
        return self::SERVER('REMOTE_ADDR');
    }

    public static function getClientInfo($ip = NULL, $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        return @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
    }

    public static function getClientHost() {
        return self::SERVER('REMOTE_HOST');
    }

    public static function getClientPort() {
        return self::SERVER('REMOTE_PORT');
    }

    /**
     * eg. (Chrome, firefox)
     * @return (type) (description)
     */
    public static function getClientBrowser() {
        return self::browser('browser');
    }

    /**
     * eg. (Mozilla Foundation . Google Inc)
     * @return (type) (description)
     */
    public static function getClientBrowserMaker() {
        return self::browser('browser_maker');
    }

    /**
     * eg. (32)
     * @return (type) (description)
     */
    public static function getClientBrowserBits() {
        return self::browser('browser_bits');
    }

    /**
     * eg. (48.0, 43.0)
     * @return (type) (description)
     */
    public static function getClientBrowserVersion() {
        return self::browser('version');
    }

    /**
     * eg. (Win8.1)
     * @return (type) (description)
     */
    public static function getClientOS() {
        return self::browser('platform');
    }

    /**
     * eg. (64, 32)
     * @return (type) (description)
     */
    public static function getClientOSBits() {
        return self::browser('platform_bits');
    }

    /**
     * eg. (Microsoft Corporation)
     * @return (type) (description)
     */
    public static function getClientOSMaker() {
        return self::browser('platform_maker');
    }

    /**
     * eg. (Windows 8.1)
     * @return (type) (description)
     */
    public static function getClientOSDesc() {
        return self::browser('platform_description');
    }

    /**
     * eg. (Windows Desktop)
     * @return (type) (description)
     */
    public static function getClientDevice() {
        return self::browser('device_name');
    }

    public static function isSecure($param) {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            return true;
        }

        return false;
    }

    public static function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' );
    }

    /**
     * Helper Method for retrieving browser info
     * @param  (type) $key (description)
     * @return (type)      (description)
     */
    private static function browser($key) {
        return (get_browser(null, true)[$key]) ? : '';
    }

    /**
     * Helper Method for retrieving browser info
     * @param  (type) $key (description)
     * @return (type)      (description)
     */
    private static function SERVER($key) {
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : '';
    }

}
