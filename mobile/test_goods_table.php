<?php

/**
 * ECSHOP 标签云
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: tag_cloud.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$all_1 = $db->getAll("select goods_id ,purchase_price from test.ecs_goods where goods_id>0 and goods_id<1884");//goodsback.ecs_goods

foreach($all_1 as $val){
$goods_id = $val['goods_id'];
$purchase_price = $val['purchase_price'];

$sql = "update baianshop.ecs_goods set purchase_price ='$purchase_price' where goods_id = '$goods_id'";
$rs = $db->query($sql);
echo "<pre>";
var_dump($rs);
}
die();

$all_2 = $db->getAll("select count(*) from baianshop.ecs_goods ");

echo "<pre>";
var_dump($all_1);
var_dump($all_2);die();