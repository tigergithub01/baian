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

//文件格式
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

//转换图片大小
$sql = "SELECT original_img, goods_thumb FROM " .$GLOBALS['ecs']->table('goods') ;
$where = " WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 ";

// $where.=" AND brand_id=60 ";

$sql.=$where;


// " WHERE goods_id LIKE " . "2008"."";
$rows = $GLOBALS['db']->getAll($sql);
foreach ($rows as $key => $value) {
	$max=$GLOBALS['_CFG']['thumb_width'];
	
	$original_img = $value['original_img'];
	$goods_thumb = $value['goods_thumb'];
	
	$size_src=getimagesize($original_img);
	if($size_src){
		$w=$size_src['0'];
		$h=$size_src['1'];
		if($w > $h){
			$w=$max;
			$h=$h*($max/$size_src['0']);
		}else{
			$h=$max;
			$w=$w*($max/$size_src['1']);
		}
	}else{
		/* $w= $GLOBALS['_CFG']['thumb_width'];
		$h= $GLOBALS['_CFG']['thumb_height']; */
		$w= $GLOBALS['_CFG']['thumb_width'];
		$h= $GLOBALS['_CFG']['thumb_height'];
	}	
	
	
	$dir = dirname(ROOT_PATH . $goods_thumb);
	
	
	if (file_exists(ROOT_PATH.'/'.$original_img)){
		$thumb_img = $image->make_thumb(ROOT_PATH.'/'.$original_img , $w,  $h, $dir+"/");
		// 	unlink(ROOT_PATH.'/'. $thumb_img);
		
		// 	unlink(ROOT_PATH.'/'. $goods_thumb);
		
		rename(ROOT_PATH.'/'. $thumb_img, ROOT_PATH.'/'.$goods_thumb);
// 		print_r(ROOT_PATH. $thumb_img);
		echo "<p>".ROOT_PATH.'/'.$goods_thumb."</p>";
	}
		
	
	

	
}
    
?>