<?php

/**
 * ECSHOP 地区切换程序
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: Laiguangming $
 * $Id: getArea.php 17217 2014-01-15 12:13:23Z Laiguangming $
*/

define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

header('Content-type: text/html; charset=' . EC_CHARSET);

$pid   = !empty($_REQUEST['pid'])   ? intval($_REQUEST['pid'])   : 1;
$type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 1;
$callback = !empty($_REQUEST['callback']) ? $_REQUEST['callback'] : 'getAreaListcallback';

//$arr = array();
//$arr2 = array();
$res = get_Area($type, $pid);


//foreach($res as $ks=>$vs)
//{
//  if ($type == 1)
//  {
//    //$arr2 = array();
//    $arr2[$ks]['id'] = $vs['region_id'];
//    $arr2[$ks]['name'] = $vs['region_name'];
//    //$arr2[$ks]['root'] = '0';
//    //$arr2[$ks]['djd'] = '1';
//    //$arr2[$ks]['c'] = '0';
    
//    //$arr[$vs['region_name']] = $arr2;
//  }
//}
//$arr['options'] = $arr2;

$json = new JSON;
echo $callback.'('.$json->encode($res).')';die();

file_put_contents('log.txt',$callback.'('.$json->encode($res).')');die('ok');
/**
[
    {
        "id": "2",
        "name": "北京"
    },
    {
        "id": "6",
        "name": "广东"
    },
    {
        "id": "32",
        "name": "重庆"
    },
    {
        "id": "13934",

	getAreaListcallback2

*/
?>