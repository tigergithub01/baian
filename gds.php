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

$gid   = !empty($_REQUEST['gid'])   ? intval($_REQUEST['gid'])   : 1;
$provinceid   = !empty($_REQUEST['provinceid'])   ? intval($_REQUEST['provinceid'])   : 6;
$cityid   = !empty($_REQUEST['cityid'])   ? intval($_REQUEST['cityid'])   : 77;
$areaid   = !empty($_REQUEST['areaid'])   ? intval($_REQUEST['areaid'])   : 707;
$townid   = !empty($_REQUEST['townid'])   ? intval($_REQUEST['townid'])   : 0;
$callback = !empty($_REQUEST['callback']) ? $_REQUEST['callback'] : 'getAreaListcallback';

$res = get_Gds($gid, $provinceid, $cityid, $areaid, $townid);

$json = new JSON;
echo $callback.'('.$json->encode($res).')';die();

file_put_contents('log_gds.txt',$callback.'('.$json->encode($res).')');die('ok!');

?>