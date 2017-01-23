<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//require 'vendor/autoload.php';

function load_config($path = '/mira', $configs = null) {
    if(empty($zk)) {
        $zk = new \Kyoz\ZookeeperClient('zk01:2181,zk02:2181,zk03:2181');
    }
    if($configs === null) $configs = [];
    $list = $zk->getChildren($path);
    if(!empty($list) && is_array($list)) {
        foreach($list as $key) {
            $configs[$key] = load_config($path . "/$key");
        }
    }else {
        $configs = $zk->get($path);
    }
    return $configs;
}

function get_env() {
    $eth0Ip = \Kyoz\Utils\Network::getEthIp();
    $zk = new \Kyoz\ZookeeperClient('zk01:2181,zk02:2181,zk03:2181');
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

