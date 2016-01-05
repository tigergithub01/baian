<?php

/**
 * ECSHOP 会员中心
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');
include_once(ROOT_PATH . 'includes/lib_order.php');


$order_status_list = array(OS_UNCONFIRMED,OS_CONFIRMED);
$shipping_status_list = array(SS_UNSHIPPED);
$pay_status_list = array(PS_UNPAYED);


//查询24小时未支付订单,24小时内未支付的订单自动取消
$sql = "SELECT order_id, order_sn FROM " .$GLOBALS['ecs']->table('order_info') .
" WHERE order_status " . db_create_in($order_status_list).
" AND shipping_status ". db_create_in($shipping_status_list) . 
" AND pay_status " . db_create_in($pay_status_list) .
" AND (" . gmtime() ." - add_time)  > 24*3600 ";
$rows = $GLOBALS['db']->getAll($sql);
foreach ($rows as $key => $value) {
	$order_id = $value['order_id'];
	
	if (cancel_order($order_id, 'system' ,1 ,'超过24小时未支付，系统自动取消订单'))
    {
        //ecs_header("Location: user.php?act=order_list\n");
        echo "订单取消成功-'$order_id'\n";
//         exit;
    }
    else
    {
    	echo "订单取消失败-'$order_id'";
    	//$err->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }
}
    
?>