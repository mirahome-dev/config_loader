<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//require 'vendor/autoload.php';

function config_loader($path = '/mira/api') {
    global $zk;
    $zk = new \Kyoz\ZookeeperClient('zk01:2181,zk02:2181,zk03:2181');
    $env = config_loader_getEnvName();
    define('ENV', $env);
    //echo "\n\n执行：$env ".date("Y-m-d H:i:s")."\n";
    config_loader_loadConfig($path . '/common/const', "const");
    config_loader_loadConfig($path . '/common/global', "global");
    config_loader_loadConfig($path . '/'. $env .'/const', "const");
    config_loader_loadConfig($path . '/'. $env .'/global', "global");
}

function config_loader_loadConfig($path = '', $type = 'const') {
    global $zk;
    $list = @$zk->getChildren($path);
    if(!empty($list)) {
        foreach($list as $key) {
            $value = $zk->get($path . '/' . $key);
            if($type == 'const') {
                define("$key", "$value");
            }else {
                global $$key;
                $$key=json_decode("$value", true);
            }
        }
    }
}

function config_loader_getEnvName() {
    $eth0Ip = \Kyoz\Utils\Network::getEthIp();
    global $zk;
    $mapJson = $zk->get('/mira/envEth0Map');
    $map = json_decode($mapJson);
    foreach($map as $env => $ips) {
        foreach($ips as $ip) {
            if($ip == $eth0Ip) {
                return $env;
            }
        }
    }
    return false;
}
