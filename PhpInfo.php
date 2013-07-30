<?php

class PhpInfo 
{
    public $phpinfos = [];
    /*
     * constructor
     */
    public function __construct() {
        ob_start();
        phpinfo(INFO_CONFIGURATION);
        $phpinfo = explode(PHP_EOL, ob_get_contents());
        ob_end_clean();
        $unsets = [];
        foreach ($phpinfo as $key => $val) {
            if (strpos($val, '=>') === false) {
                array_push($unsets, $key); 
            }
            if (strpos($val, 'Directive => Local Value => Master Value') !== false) {
                array_push($unsets, $key); 
            }
        }
        foreach ($unsets as $key => $val) {
            unset($phpinfo[$val]);
        }
        $phpinfo = array_merge($phpinfo);
        foreach ($phpinfo as $key => $val) {
            $info = explode('=>', $val);
            $this->phpinfos[trim($info[0])]['local'] = trim($info[1]);
            $this->phpinfos[trim($info[0])]['master'] = trim($info[2]);
        }
    }
    /*
     * find
     */
    public function find($directive = 'all', $primary = 'local') {
        switch ($directive) {
            case 'all':
                return $this->phpinfos;
                break;
            default:
                return $this->phpinfos[$directive][$primary];
                break;
        }
    }
}

$pInfo = new PhpInfo();
echo var_export($pInfo->find(), 1).PHP_EOL;
echo var_export($pInfo->find('all'), 1).PHP_EOL;
echo var_export($pInfo->find('zend.detect_unicode', 'local'), 1).PHP_EOL; // => 'On'
echo var_export($pInfo->find('post_max_size', 'master'), 1).PHP_EOL; // => '8M'
echo var_export($pInfo->find('max_execution_time', 'local'), 1).PHP_EOL; // '0'
echo var_export($pInfo->find('memory_limit', 'local'), 1).PHP_EOL; // '128M'
echo var_export($pInfo->find('upload_max_filesize', 'local'), 1).PHP_EOL; // => '2M'
echo var_export($pInfo->find('variables_order', 'local'), 1).PHP_EOL; // => 'GPCS'
echo var_export($pInfo->find('post_max_size', 'local'), 1).PHP_EOL; // => '8M'
echo var_export($pInfo->find('include_path', 'local'), 1).PHP_EOL; // => '.:/usr/local/lib/php'
