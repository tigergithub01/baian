<?php

/**
 * ECSHOP 购物流程
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: flow.php 17164 2010-05-24 01:42:50Z liuhui $
 */

/**
 * 微信扫描加入购物车
 * @var unknown_type
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');

$goods_id = intval($_GET['goods_id']);

if(addto_cart($goods_id)){
	$sql = "select * from ".$ecs->table('goods')." where goods_id='$goods_id'";
	$goods_info = $db->getRow($sql);
	$wxbuy_cut = $goods_info['wxbuy_cut'];
	
	//初始化session:cut_relation
	if(!isset($_SESSION['cut_relation']) || empty($_SESSION['cut_relation'])){
		$_SESSION['cut_relation'] = serialize(array());
	}
	$ser_str = $_SESSION['cut_relation'];
	$cut_relation_arr = unserialize($ser_str);
	if(!empty($wxbuy_cut)){
		if(!array_key_exists($goods_id, $cut_relation_arr)){
			$cut_relation_arr[$goods_id] = array('goods_id'=>$goods_id,'wxbuy_num'=>1,'wxbuy_cut'=>$wxbuy_cut);
		}else{
			//更新购买数量，+1
			$cut_relation_arr[$goods_id]['wxbuy_num'] += 1;
		}
	}
	//更新为新年购买数量后，更新session:cut_relation
	$_SESSION['cut_relation'] = serialize($cut_relation_arr);
	
	//更新微信购买优惠价
	$_SESSION['total_cut'] = 0;
	foreach($cut_relation_arr as $key=>$val){
		$_SESSION['total_cut'] += $val['wxbuy_num']*$val['wxbuy_cut'];
	}
	$_SESSION['total_cut'] = number_format($_SESSION['total_cut'],2);
	
	header("Location:flow.php?step=cart");
}else{
	show_message('扫描二维码出错！','回到首页！');
	header("Location:index.php");
}
die('success!');

?>