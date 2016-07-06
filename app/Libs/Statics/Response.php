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

use function twig;

abstract class Response {

    const TYPE_php = 'text/plain';
    const TYPE_au = 'audio/basic';
    const TYPE_avi = 'video/msvideo, video/avi, video/x-msvideo';
    const TYPE_bmp = 'image/bmp';
    const TYPE_bz2 = 'application/x-bzip2';
    const TYPE_css = 'text/css';
    const TYPE_dtd = 'application/xml-dtd';
    const TYPE_doc = 'application/msword';
    const TYPE_docx = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    const TYPE_dotx = 'application/vnd.openxmlformats-officedocument.wordprocessingml.template';
    const TYPE_es = 'application/ecmascript';
    const TYPE_exe = 'application/octet-stream';
    const TYPE_gif = 'image/gif';
    const TYPE_gz = 'application/x-gzip';
    const TYPE_hqx = 'application/mac-binhex40';
    const TYPE_html = 'text/html';
    const TYPE_jar = 'application/java-archive';
    const TYPE_jpg = 'image/jpeg';
    const TYPE_js = 'application/x-javascript';
    const TYPE_midi = 'audio/x-midi';
    const TYPE_mp3 = 'audio/mpeg';
    const TYPE_mpeg = 'video/mpeg';
    const TYPE_ogg = 'audio/vorbis, application/ogg';
    const TYPE_pdf = 'application/pdf';
    const TYPE_pl = 'application/x-perl';
    const TYPE_png = 'image/png';
    const TYPE_potx = 'application/vnd.openxmlformats-officedocument.presentationml.template';
    const TYPE_ppsx = 'application/vnd.openxmlformats-officedocument.presentationml.slideshow';
    const TYPE_ppt = 'application/vnd.ms-powerpointtd>';
    const TYPE_pptx = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    const TYPE_ps = 'application/postscript';
    const TYPE_qt = 'video/quicktime';
    const TYPE_ra = 'audio/x-pn-realaudio, audio/vnd.rn-realaudio';
    const TYPE_ram = 'audio/x-pn-realaudio, audio/vnd.rn-realaudio';
    const TYPE_rdf = 'application/rdf, application/rdf+xml';
    const TYPE_rtf = 'application/rtf';
    const TYPE_sgml = 'text/sgml';
    const TYPE_json = 'application/json';
    const TYPE_sit = 'application/x-stuffit';
    const TYPE_sldx = 'application/vnd.openxmlformats-officedocument.presentationml.slide';
    const TYPE_svg = 'image/svg+xml';
    const TYPE_swf = 'application/x-shockwave-flash';
    const TYPE_tar_gz = 'application/x-tar';
    const TYPE_tgz = 'application/x-tar';
    const TYPE_tiff = 'image/tiff';
    const TYPE_tsv = 'text/tab-separated-values';
    const TYPE_txt = 'text/plain';
    const TYPE_wav = 'audio/wav, audio/x-wav';
    const TYPE_xlam = 'application/vnd.ms-excel.addin.macroEnabled.12';
    const TYPE_xls = 'application/vnd.ms-excel';
    const TYPE_xlsb = 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';
    const TYPE_xlsx = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    const TYPE_xltx = 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
    const TYPE_xml = 'application/xml';
    const TYPE_zip = 'application/zip, application/x-compressed-zip';

    public static function setHeader($name, $value) {
        header("$name: $value");
    }

    /**
     * redirect to <pre>$location</pre> or any Error page
     * @param  string|Code $location url to move to 
     * @param  array $with params sent with the url
     * @param  int $after num of second to wait before redirecting
     * @return void
     */
    public static function redirectTo($location, $with = [], $after = 0) {

        if (!empty($with)) {
            foreach ($with as $k => $v) {
                Request::appendParam($k, $v);
            }
        }
        if (empty($location)) {
            $location = Url::app();
        } else if (!empty($location) && $after > 0) {
            // Redriect with a after:
            header("Refresh: $after; url=$location");
            return;
        }
        header("Location: $location");
    }

    /**
     * redirect back or to home page if the previous url not found
     * @param  array $with params sent with the url
     * @param  int $after num of second to wait before redirecting
     * @return void
     */
    public static function redirectBack($with = [], $after = 0) {
        if (isset($_SERVER['HTTP_REFERER'])) {
            self::redirectTo($_SERVER['HTTP_REFERER'], $with, $after);
        } else {
            self::redirectTo(Url::app(), $with, $after);
        }
    }

    /**
     * Refresh the current page after <code>$after</code> sec(s)
     * @param  integer $after num of second to wait before redirecting
     * @return void        
     */
    public static function refresh($after = 0) {
        self::after($after, $_SERVER['SCRIPT_URI']);
    }

    public static function json($data) {
        self::setContentType(self::TYPE_json);
        echo json_encode($data);
    }

    public static function error($code) {
        if (is_numeric($code)) {
            $txt = '';
            switch ($code) {
                case 404:
                    // Page was not found:
                    header('HTTP/1.1 404 Not Found');
                    $txt = twig(Config::app('url.error.404'));
                    break;
                case 403:
                    // Access forbidden:
                    header('HTTP/1.1 403 Forbidden');
                    $txt = twig(Config::app('url.error.403'));

                    break;
                case 500:
                    // Server error
                    header('HTTP/1.1 500 Internal Server Error');
                    $txt = twig(Config::app('url.error.500'));

                    break;
                case 301:
                    // The page moved permanently should be used for
                    // all redrictions, because search engines know
                    // what's going on and can easily update their urls.
                    header('HTTP/1.1 301 Page Moved Permanently');
                    $txt = twig(Config::app('url.error.301'));

                    break;
                case 401:
                    header('HTTP/1.1 401 Unauthorized: Access is denied due to invalid credentials');
                    $txt = twig(Config::app('url.error.401'));

                    break;
            }
            die($txt);
        }
    }

    public static function setContentLength($long) {
        header("Content-Length: $long");
    }

    public static function setContentType($type, $charset = 'utf-8') {
        header("Content-Type: $type; charset=$charset");
    }

    public static function setContentTypeForFile($filename) {
        header("Content‐Type: " . mime_content_type($filename));
    }

    public static function download($filepath, $filename = '', $open_in_browser = false) {
        $filename = (empty($filename)) ? basename($filepath) : $filename;
        $filetype = ($open_in_browser) ? mime_content_type($filepath) : self::TYPE_exe;
        $dispostion = ($open_in_browser) ? "inline" : "attachment";
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        if (stristr($filename, $ext) == FALSE) {
            $filename .= ".$ext";
        }
        ob_clean(); // Clear any previously written headers in the output buffer

        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        $content = file_get_contents($filepath);
        header('Content-Type: ' . $filetype);
        header('Content-Length: ' . strlen($content));
        header('Content-disposition: ' . $dispostion . '; filename="' . $filename . '"');
        header('Cache-Control: private,false, must-revalidate,post-check=0, pre-check=0, max-age=0');
        header("Content-Transfer-Encoding: binary");
        header('Pragma: public');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        echo $content;
    }

}
