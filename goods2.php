<?php

/**
 * ECSHOP 商品详情
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: goods.php 17063 2010-03-25 06:35:46Z liuhui $
*/

echo "<pre>";
var_dump($_COOKIE);die();

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$smarty->assign('JYXL_BL', $GLOBALS['_CFG']['JYXL_BL']);
$smarty->assign('JYXL_MYF', $GLOBALS['_CFG']['JYXL_MYF']);

//if(isset($_GET['vardump'])){
//	echo "<pre>";
//	var_dump($GLOBALS['_CFG']);die();
//}

echo "<pre>";
$admin_status = file_get_contents("./admin/check_admin_online.php");


var_dump(eval($admin_status));die();

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;

/*------------------------------------------------------ */
//-- 改变属性、数量时重新计算商品价格
/*------------------------------------------------------ */

if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'price')
{
    include('includes/cls_json.php');

    $json   = new JSON;
    $res    = array('err_msg' => '', 'result' => '', 'qty' => 1);

    $attr_id    = isset($_REQUEST['attr']) ? explode(',', $_REQUEST['attr']) : array();
    $number     = (isset($_REQUEST['number'])) ? intval($_REQUEST['number']) : 1;

    if ($goods_id == 0)
    {
        $res['err_msg'] = $_LANG['err_change_attr'];
        $res['err_no']  = 1;
    }
    else
    {
        if ($number == 0)
        {
            $res['qty'] = $number = 1;
        }
        else
        {
            $res['qty'] = $number;
        }

        $shop_price  = get_final_price($goods_id, $number, true, $attr_id);
        $res['result'] = price_format($shop_price * $number);
    }

    die($json->encode($res));
}

/* 代码增加_start   By www.ecshop120.com  多城市多仓库*/

if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'check_storeroom')
{
	include('includes/cls_json.php');

    $json   = new JSON;
    $res    = array('err_msg' => '', 'result' => '');

	/* 重新组织 goods_attr 字符串 */
	$attr_wwwECSHOP120com = !empty($_REQUEST['attr']) ? $_REQUEST['attr'] : "0";
	$sql_qq2211707 = "select * from ". $ecs->table("goods_attr") ." where goods_attr_id in ($attr_wwwECSHOP120com) order by attr_id  ";
	$res_qq2211707 =  $db->query($sql_qq2211707);
	$attr_wwwECSHOP120com="";
	$kkk=0;
	while($row_qq2211707 = $db->fetchRow($res_qq2211707))
	{
		if($kkk)
		{
			$attr_wwwECSHOP120com .= "|";
		}
		$attr_wwwECSHOP120com .= $row_qq2211707['goods_attr_id'] ;
		$kkk++;
	}

	$goods_id_wwwECSHOP120com = (isset($_REQUEST['goods_id'])) ? intval($_REQUEST['goods_id']) : 0;
    $province_id_wwwECSHOP120com = (isset($_REQUEST['province_id'])) ? intval($_REQUEST['province_id']) : 0;
	$province_name_wwwECSHOP120com = $db->getOne("select region_name from ". $ecs->table('region') . " where region_id='$province_id_wwwECSHOP120com' ");
	//$store_id_wwwECSHOP120com = $db->getOne("select store_id  from ". $ecs->table('goods_storeroom') . " where store_province like '%#". $province_id_wwwECSHOP120com ."#%' ");

// 修改 by pgge
	$level = get_parent_regionids($province_id_wwwECSHOP120com);
	$level_arr = explode('|', $level);
	$level_arr = array_reverse($level_arr);
	$str = '';
	foreach ($level_arr as $key => $value){
		$str = $str ? $value.'|'.$str : $value;
		$ext_sql .= ($key == 0) ? "store_province like '%#". $value ."#%' " : "OR store_province like '%#".$str."#%' ";
		
//		echo $str ; echo "<br>";
	}
//	var_dump($ext_sql);die();
	$store_id_wwwECSHOP120com = $db->getOne("select store_id  from ". $ecs->table('goods_storeroom') . " where $ext_sql ");
//	var_dump($store_id_wwwECSHOP120com);die();
    $sql_attr_wwwECSHOP120com =  $attr_wwwECSHOP120com ? " and goods_attr='$attr_wwwECSHOP120com'  " : " ";
	$product_number_qq2211707 = $db->getOne("select sum(store_number) as store_number_total from ". $ecs->table('products_storeroom') ."  where goods_id='$goods_id_wwwECSHOP120com' and store_id='$store_id_wwwECSHOP120com' $sql_attr_wwwECSHOP120com ");

	$youhuo = $product_number_qq2211707 ? '有货' : '无货';
	$res['error'] = $product_number_qq2211707;
//	$res['result'] = $province_name_wwwECSHOP120com. '（'. $youhuo . '）';
	$res['result'] = $youhuo;
	$res['store_province_id'] = $province_id_wwwECSHOP120com;
    die($json->encode($res));
}
/* 代码增加_end   By www.ecshop120.com */


/* 代码增加_start  By www.68ecshop.com */
if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'get_products_qq800007396')
{

include('includes/cls_json.php');

$json_www_68ecshop_com = new JSON;
// $res = array('err_msg' => '', 'result' => '', 'qty' => 1);

$spce_id_www_68ecshop_com = $_GET['id'];
$goods_id_www_68ecshop_com = $_GET['goods_id'];
$row_www_68ecshop_com = get_products_info($goods_id_www_68ecshop_com,explode(",",$spce_id_www_68ecshop_com));
//$res = array('err_msg'=>$goods_id,'id'=>$spce_id);
die($json_www_68ecshop_com->encode($row_www_68ecshop_com));

}
/* 代码增加_end  By www.68ecshop.com */


/*------------------------------------------------------ */
//-- 商品购买记录ajax处理
/*------------------------------------------------------ */

if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'gotopage')
{
    include('includes/cls_json.php');

    $json   = new JSON;
    $res    = array('err_msg' => '', 'result' => '');

    $goods_id   = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $page    = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 1;

    if (!empty($goods_id))
    {
        $need_cache = $GLOBALS['smarty']->caching;
        $need_compile = $GLOBALS['smarty']->force_compile;

        $GLOBALS['smarty']->caching = false;
        $GLOBALS['smarty']->force_compile = true;

        /* 商品购买记录 */
        $sql = 'SELECT u.user_name, og.goods_number, oi.add_time, IF(oi.order_status IN (2, 3, 4), 0, 1) AS order_status ' .
               'FROM ' . $ecs->table('order_info') . ' AS oi LEFT JOIN ' . $ecs->table('users') . ' AS u ON oi.user_id = u.user_id, ' . $ecs->table('order_goods') . ' AS og ' .
               'WHERE oi.order_id = og.order_id AND ' . time() . ' - oi.add_time < 2592000 AND og.goods_id = ' . $goods_id . ' ORDER BY oi.add_time DESC LIMIT ' . (($page > 1) ? ($page-1) : 0) * 5 . ',5';
        $bought_notes = $db->getAll($sql);

        foreach ($bought_notes as $key => $val)
        {
            $bought_notes[$key]['add_time'] = local_date("Y-m-d G:i:s", $val['add_time']);
        }

        $sql = 'SELECT count(*) ' .
               'FROM ' . $ecs->table('order_info') . ' AS oi LEFT JOIN ' . $ecs->table('users') . ' AS u ON oi.user_id = u.user_id, ' . $ecs->table('order_goods') . ' AS og ' .
               'WHERE oi.order_id = og.order_id AND ' . time() . ' - oi.add_time < 2592000 AND og.goods_id = ' . $goods_id;
        $count = $db->getOne($sql);


        /* 商品购买记录分页样式 */
        $pager = array();
        $pager['page']         = $page;
        $pager['size']         = $size = 5;
        $pager['record_count'] = $count;
        $pager['page_count']   = $page_count = ($count > 0) ? intval(ceil($count / $size)) : 1;;
        $pager['page_first']   = "javascript:gotoBuyPage(1,$goods_id)";
        $pager['page_prev']    = $page > 1 ? "javascript:gotoBuyPage(" .($page-1). ",$goods_id)" : 'javascript:;';
        $pager['page_next']    = $page < $page_count ? 'javascript:gotoBuyPage(' .($page + 1) . ",$goods_id)" : 'javascript:;';
        $pager['page_last']    = $page < $page_count ? 'javascript:gotoBuyPage(' .$page_count. ",$goods_id)"  : 'javascript:;';

        $smarty->assign('notes', $bought_notes);
        $smarty->assign('pager', $pager);

        $res['result'] = $GLOBALS['smarty']->fetch('library/bought_notes.lbi');

        $GLOBALS['smarty']->caching = $need_cache;
        $GLOBALS['smarty']->force_compile = $need_compile;
    }

    die($json->encode($res));
}


/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

$cache_id = $goods_id . '-' . $_SESSION['user_rank'].'-'.$_CFG['lang'];
$cache_id = sprintf('%X', crc32($cache_id));
if (!$smarty->is_cached('goods.dwt', $cache_id))
{
    $smarty->assign('image_width',  $_CFG['image_width']);
    $smarty->assign('image_height', $_CFG['image_height']);
    $smarty->assign('helps',        get_shop_help()); // 网店帮助
    $smarty->assign('id',           $goods_id);
    $smarty->assign('type',         0);
    $smarty->assign('cfg',          $_CFG);
    $smarty->assign('promotion',       get_promotion_info($goods_id));//促销信息
    $smarty->assign('promotion_info', get_promotion_info());

    /* 获得商品的信息 */
    $goods = get_goods_info($goods_id);

    if ($goods === false)
    {
        /* 如果没有找到任何记录则跳回到首页 */
        ecs_header("Location: ./\n");
        exit;
    }
    else
    {
    	/* 代码添加 start  by pgge */
	    $sql="select * from ". $GLOBALS['ecs']->table('content_key') ;
		$res_k=$GLOBALS['db']->query($sql);
		while ($row_k=$GLOBALS['db']->fetchRow($res_k))
		{
				if($row_k['replace_num'])
				{
					if($row_k['key_url'])
					{
					$goods['goods_desc']=preg_replace('/(?!<[^>]*)'.$row_k['key_name'].'(?![^<]*>)/i', '<a href="' . $row_k['key_url'] . '" target="_blank" >'.$row_k['key_name']."</a>", $goods['goods_desc'], $row_k['replace_num']);
					}else{
					$goods['goods_desc']=preg_replace('/(?!<[^>]*)'.$row_k['key_name'].'(?![^<]*>)/i', '<a href="search.php?keywords=' .$row_k['key_name']. '" target="_blank" >'.$row_k['key_name']."</a>", $goods['goods_desc'], $row_k['replace_num']);
					}
				}
				else
				{
					if($row_k['key_url'])
					{
					$goods['goods_desc']=preg_replace('/(?!<[^>]*)'.$row_k['key_name'].'(?![^<]*>)/i', '<a href="' . $row_k['key_url'] . '" target="_blank" >'.$row_k['key_name']."</a>", $goods['goods_desc']);
					}else{
					$goods['goods_desc']=preg_replace('/(?!<[^>]*)'.$row_k['key_name'].'(?![^<]*>)/i', '<a href="search.php?keywords=' .$row_k['key_name']. '" target="_blank" >'.$row_k['key_name']."</a>", $goods['goods_desc']);
					}
				}
				$goods['goods_desc']=preg_replace('/(?!<[^>]*)'.$row_k['key_name'].'(?![^<]*>)/i', '<strong>' . $row_k['key_name'] . '</strong>', $goods['goods_desc'],1 );
		}
		/* 代码添加 end  by pgge */
        if ($goods['brand_id'] > 0)
        {
            $goods['goods_brand_url'] = build_uri('brand', array('bid'=>$goods['brand_id']), $goods['goods_brand']);
        }

        $shop_price   = $goods['shop_price'];
        $linked_goods = get_linked_goods($goods_id);

        $goods['goods_style_name'] = add_style($goods['goods_name'], $goods['goods_name_style']);
/*  代码  添加 start  by pgge */
    if($goods['qr_link']){
		$goods['desc_qr_code'] = $goods ['qr_link'];
		}else{
		$goods['desc_qr_code'] = 'http://'.$_SERVER ['HTTP_HOST'].'/'.build_uri('goods', array('gid'=>$goods['goods_id']), $goods['goods_name']);
		}
		
		include "phpqrcode/qrlib.php"; 
		$data = 'http://'.$_SERVER ['HTTP_HOST'].'/'.build_uri('goods', array('gid'=>$goods['goods_id']), $goods['goods_name']);
		$errorCorrectionLevel = 'L';
		$matrixPointSize = 4;
		$path = "qrcode/";
	   if (!file_exists($path)){
			mkdir($path);
	   }
	   $filename = $path.$errorCorrectionLevel.'-'.$matrixPointSize.'-'.md5($goods['goods_id']).'.png';
	   if(!file_exists($filename)){
	   		QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 3);
	   }
	   $goods['img_qrcode'] = $filename;
	   
	   //生成微信购买二维码
	   
	   $wxbuy_filename = $path.$errorCorrectionLevel.'-'.$matrixPointSize.'-'.md5('weixin_add_to_cart'.$goods['goods_id']).'.png';
	   $data = 'http://'.$_SERVER ['HTTP_HOST'].'/weixin_add_to_cart.php?goods_id='.$goods_id;
	   if(!file_exists($wxbuy_filename)){
	   		QRcode::png($data, $wxbuy_filename, $errorCorrectionLevel, $matrixPointSize, 3);
	   }
	   $goods['wxbuy_img_qrcode'] = $wxbuy_filename;
	   
		
/*  代码  添加 end  by pgge */
        /* 购买该商品可以得到多少钱的红包 */
        if ($goods['bonus_type_id'] > 0)
        {
            $time = gmtime();
            $sql = "SELECT type_money FROM " . $ecs->table('bonus_type') .
                    " WHERE type_id = '$goods[bonus_type_id]' " .
                    " AND send_type = '" . SEND_BY_GOODS . "' " .
                    " AND send_start_date <= '$time'" .
                    " AND send_end_date >= '$time'";
            $goods['bonus_money'] = floatval($db->getOne($sql));
            if ($goods['bonus_money'] > 0)
            {
                $goods['bonus_money'] = price_format($goods['bonus_money']);
            }
        }
        //本商品可使用红包金额
        if($goods['bonus'] > 0){
        	$goods['bonus'] = price_format($goods['bonus']);
        }
        
        $goods_id = $goods['goods_id'];
        $cat_id = $goods['cat_id'];
        $brand_id = $goods['brand_id'];
        $sql = "select type_name,type_money,goods_ids,cat_ids,brand_ids from ecs_bonus_type where goods_ids <> '' or cat_ids <> '' or brand_ids <> ''";
        $bonus_type = $GLOBALS['db']->getAll($sql);
//        echo "<pre>";
//        var_dump($bonus_type);die();
        $available_bonus = array();
        foreach($bonus_type as $key=>$val){
        	if(strpos($val['goods_ids'],$goods_id) !== false || strpos($val['cat_ids'],$cat_id) !== false || strpos($val['brand_ids'],$brand_id) !== false){
        		$available_bonus = $val;
        	}
        }
        
        if(!empty($available_bonus)){
        	$goods['bonus_type_name'] = $available_bonus['type_name'];
        	$goods['bonus_type_money'] = price_format($available_bonus['type_money']);
        }
        
        //goods_ids	cat_ids	brand_ids

        $smarty->assign('goods',              $goods);
        $smarty->assign('goods_id',           $goods['goods_id']);
        $smarty->assign('promote_end_time',   $goods['gmt_end_time']);
        $smarty->assign('categories',         get_categories_tree($goods['cat_id']));  // 分类树
		$smarty->assign('attr_url',           get_attachment_www_68ecshop_com($goods_id));  // 自定义属性图片  www.68ecshop.com

        /* meta */
        $smarty->assign('keywords',           htmlspecialchars($goods['keywords']));
        $smarty->assign('description',        htmlspecialchars($goods['goods_brief']));


        $catlist = array();
        foreach(get_parent_cats($goods['cat_id']) as $k=>$v)
        {
            $catlist[] = $v['cat_id'];
        }

        assign_template('c', $catlist);

         /* 上一个商品下一个商品 */
        $prev_gid = $db->getOne("SELECT goods_id FROM " .$ecs->table('goods'). " WHERE cat_id=" . $goods['cat_id'] . " AND goods_id > " . $goods['goods_id'] . " AND is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 LIMIT 1");
        if (!empty($prev_gid))
        {
            $prev_good['url'] = build_uri('goods', array('gid' => $prev_gid), $goods['goods_name']);
            $smarty->assign('prev_good', $prev_good);//上一个商品
        }

        $next_gid = $db->getOne("SELECT max(goods_id) FROM " . $ecs->table('goods') . " WHERE cat_id=".$goods['cat_id']." AND goods_id < ".$goods['goods_id'] . " AND is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0");
        if (!empty($next_gid))
        {
            $next_good['url'] = build_uri('goods', array('gid' => $next_gid), $goods['goods_name']);
            $smarty->assign('next_good', $next_good);//下一个商品
        }

        $position = assign_ur_here($goods['cat_id'], $goods['goods_name']);
        
//        echo '<pre>';
//        var_dump(get_parent_cats($goods['cat_id']));die();
        
        $parent_cats = get_parent_cats($goods['cat_id']);
        $top_parent_cat = $parent_cats[count($parent_cats)-1]['cat_id'];
        
        //详情页顶级分类与广告位id的对应关系
        $rel_arr = array(332=>117,333=>118,336=>119,335=>120,334=>121,337=>122,339=>123);
        
        $position_id = $rel_arr[(int)$top_parent_cat];
        $smarty->assign('position_id',$position_id);
        
//        echo "<pre>";
//        var_dump($top_parent_cat);
//        var_dump($position_id);die();
        

        /* current position */
        $smarty->assign('page_title',          trim($goods['goods_title']) ? $goods['goods_title'] : $position['title']);                    // 页面标题
        $smarty->assign('ur_here',             $position['ur_here']);                  // 当前位置

        $properties = get_goods_properties($goods_id);  // 获得商品的规格和属性

        $smarty->assign('properties',          $properties['pro']);                              // 商品属性
        $smarty->assign('specification',       $properties['spe']);                              // 商品规格
        $smarty->assign('attribute_linked',    get_same_attribute_goods($properties));           // 相同属性的关联商品
        $smarty->assign('related_goods',       $linked_goods);                                   // 关联商品
        $smarty->assign('goods_article_list',  get_linked_articles($goods_id));                  // 关联文章
        $smarty->assign('fittings',            get_goods_fittings(array($goods_id)));                   // 配件
        $smarty->assign('rank_prices',         get_user_rank_prices($goods_id, $shop_price));    // 会员等级价格
        $smarty->assign('pictures',            get_goods_gallery($goods_id));                    // 商品相册
        $smarty->assign('bought_goods',        get_also_bought($goods_id));                      // 购买了该商品的用户还购买了哪些商品
        $smarty->assign('goods_rank',          get_goods_rank($goods_id));                       // 商品的销售排名
        
        $pictures = get_goods_gallery($goods_id);
        $img_desc_arr = array();
        foreach($pictures as $key=>$val){
        	$img_desc_arr[] = $val['img_desc'];
        }
        $img_desc_str = implode(',',$img_desc_arr);
        $smarty->assign('img_desc_str', $img_desc_str);
        
        /*扫描二维码购买*/
//        $url = "http://www.123121.com/weixin_add_to_cart.php?goods_id=".$goods_id;
//        $wxbuy_img_qrcode = generateQRfromGoogle($url);
//        $smarty->assign('wxbuy_img_qrcode',$wxbuy_img_qrcode);

		/* 代码增加_start  By www.ecshop120.com  多城市多仓库*/
		$sql_qq2211707 = "select * from ". $ecs->table("region") ." where region_type =1 ";
		$res_qq2211707= $db->query($sql_qq2211707);
		$regions_wwwecshop120com =array();
		while($row_qq2211707 = $db->fetchRow($res_qq2211707))
		{
		   $regions_wwwecshop120com[$row_qq2211707['region_id']] = $row_qq2211707['region_name'] ;
		}

		$sql_wwwecshop120com="select * from ". $ecs->table('goods_storeroom') ." order by sort_order";
		$res_wwwECSHOP120com = $db->query($sql_wwwecshop120com);
		$array_wwwECSHOP120com =array();
		while($row_wwwECSHOP120com = $db->fetchRow($res_wwwECSHOP120com))
		{
			if($row_wwwECSHOP120com['store_province'])
			{
				$province_wwwECSHOP120com = explode("#", $row_wwwECSHOP120com['store_province']);
				$province_array = array();
				foreach($province_wwwECSHOP120com as $province)
				{
					if($province)
					{
						$province_array[$province] = $regions_wwwecshop120com[$province];
					}
				}
			}
			$array_wwwECSHOP120com[$row_wwwECSHOP120com['store_name']] =  $province_array ;
		}

		//echo '<pre>';
		//print_r($array_wwwECSHOP120com);
		//echo '</pre>';
		$smarty->assign('province_list', $array_wwwECSHOP120com);
		/* 代码增加_end  By www.ecshop120.com */        
        
		//yyy添加start
			$count = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('comment') . " where comment_type=0 and id_value ='$goods_id' and status=1");
        $smarty->assign('review_count',       $count); 
		
			//yyy添加end

        //获取tag
        $tag_array = get_tags($goods_id);
        $smarty->assign('tags',                $tag_array);                                       // 商品的标记

        //获取关联礼包
        $package_goods_list = get_package_goods_list($goods['goods_id']);
        $smarty->assign('package_goods_list',$package_goods_list);    // 获取关联礼包
        
		//组合套餐名
		$taocan = array('一', '二', '三','四','五','六','七','八','九','十');
		$smarty->assign('taocan',$taocan);

		/* 代码增加_start By www.ecshop120.com */
		include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
		$smarty->assign('lang',  $_LANG);
		$smarty->assign('country_list',       get_regions());
		$smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));
		/* 代码增加_end By www.ecshop120.com */

        assign_dynamic('goods');
        $volume_price_list = get_volume_price_list($goods['goods_id'], '1');
        $smarty->assign('volume_price_list',$volume_price_list);    // 商品优惠价格区间
    }
}

/* 记录浏览历史 */
if (!empty($_COOKIE['ECS']['history']))
{
    $history = explode(',', $_COOKIE['ECS']['history']);

    array_unshift($history, $goods_id);
    $history = array_unique($history);

    while (count($history) > $_CFG['history_number'])
    {
        array_pop($history);
    }

    setcookie('ECS[history]', implode(',', $history), gmtime() + 3600 * 24 * 30);
}
else
{
    setcookie('ECS[history]', $goods_id, gmtime() + 3600 * 24 * 30);
}

//猜你喜欢
$sql = "select goods_id, goods_name, shop_price, goods_thumb, market_price from ".$ecs->table('goods').
		" where is_on_sale = 1 and is_delete = 0 order by rand() limit 8";
$may_like_goods = $db->getAll($sql);
foreach($may_like_goods as $key=>$val){
	$may_like_goods[$key]['url'] = build_uri('goods', array('gid'=>$val['goods_id']));
	$may_like_goods[$key]['shop_price_formated'] = '￥'.number_format(floatval($val['shop_price']),2);
	$may_like_goods[$key]['market_price_formated'] = '￥'.number_format(floatval($val['market_price']),2);
}
$smarty->assign('may_like_goods',$may_like_goods);

/* 更新点击次数 */
$db->query('UPDATE ' . $ecs->table('goods') . " SET click_count = click_count + 1 WHERE goods_id = '$_REQUEST[id]'");

$smarty->assign('now_time',  gmtime());           // 当前系统时间
$smarty->display('goods.dwt',      $cache_id);

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得指定商品的关联商品
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_linked_goods($goods_id)
{
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'g.market_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
            'FROM ' . $GLOBALS['ecs']->table('link_goods') . ' lg ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = lg.link_goods_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE lg.goods_id = '$goods_id' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
            "LIMIT " . $GLOBALS['_CFG']['related_goods_number'];
    $res = $GLOBALS['db']->query($sql);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$row['goods_id']]['goods_id']     = $row['goods_id'];
        $arr[$row['goods_id']]['goods_name']   = $row['goods_name'];
        $arr[$row['goods_id']]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $arr[$row['goods_id']]['goods_thumb']  = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['market_price'] = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']   = price_format($row['shop_price']);
        $arr[$row['goods_id']]['url']          = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);

        if ($row['promote_price'] > 0)
        {
            $arr[$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $arr[$row['goods_id']]['formated_promote_price'] = price_format($arr[$row['goods_id']]['promote_price']);
        }
        else
        {
            $arr[$row['goods_id']]['promote_price'] = 0;
        }
    }

    return $arr;
}

/**
 * 获得指定商品的关联文章
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  void
 */
function get_linked_articles($goods_id)
{
    $sql = 'SELECT a.article_id, a.title, a.file_url, a.open_type, a.add_time ' .
            'FROM ' . $GLOBALS['ecs']->table('goods_article') . ' AS g, ' .
                $GLOBALS['ecs']->table('article') . ' AS a ' .
            "WHERE g.article_id = a.article_id AND g.goods_id = '$goods_id' AND a.is_open = 1 " .
            'ORDER BY a.add_time DESC';
    $res = $GLOBALS['db']->query($sql);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['url']         = $row['open_type'] != 1 ?
            build_uri('article', array('aid'=>$row['article_id']), $row['title']) : trim($row['file_url']);
        $row['add_time']    = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);
        $row['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ?
            sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];

        $arr[] = $row;
    }

    return $arr;
}

/**
 * 获得指定商品的各会员等级对应的价格
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_user_rank_prices($goods_id, $shop_price)
{
    $sql = "SELECT rank_id, IFNULL(mp.user_price, r.discount * $shop_price / 100) AS price, r.rank_name, r.discount " .
            'FROM ' . $GLOBALS['ecs']->table('user_rank') . ' AS r ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = '$goods_id' AND mp.user_rank = r.rank_id " .
            "WHERE r.show_price = 1 OR r.rank_id = '$_SESSION[user_rank]'";
    $res = $GLOBALS['db']->query($sql);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {

        $arr[$row['rank_id']] = array(
                        'rank_name' => htmlspecialchars($row['rank_name']),
                        'price'     => price_format($row['price']));
    }

    return $arr;
}

/**
 * 获得购买过该商品的人还买过的商品
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_also_bought($goods_id)
{
    $sql = 'SELECT COUNT(b.goods_id ) AS num, g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
            'FROM ' . $GLOBALS['ecs']->table('order_goods') . ' AS a ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('order_goods') . ' AS b ON b.order_id = a.order_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = b.goods_id ' .
            "WHERE a.goods_id = '$goods_id' AND b.goods_id <> '$goods_id' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 " .
            'GROUP BY b.goods_id ' .
            'ORDER BY num DESC ' .
            'LIMIT ' . $GLOBALS['_CFG']['bought_goods'];
    $res = $GLOBALS['db']->query($sql);

    $key = 0;
    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$key]['goods_id']    = $row['goods_id'];
        $arr[$key]['goods_name']  = $row['goods_name'];
        $arr[$key]['short_name']  = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $arr[$key]['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$key]['goods_img']   = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$key]['shop_price']  = price_format($row['shop_price']);
        $arr[$key]['url']         = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);

        if ($row['promote_price'] > 0)
        {
            $arr[$key]['promote_price'] = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $arr[$key]['formated_promote_price'] = price_format($arr[$key]['promote_price']);
        }
        else
        {
            $arr[$key]['promote_price'] = 0;
        }

        $key++;
    }

    return $arr;
}

/**
 * 获得指定商品的销售排名
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  integer
 */
function get_goods_rank($goods_id)
{
    /* 统计时间段 */
    $period = intval($GLOBALS['_CFG']['top10_time']);
    if ($period == 1) // 一年
    {
        $ext = " AND o.add_time > '" . local_strtotime('-1 years') . "'";
    }
    elseif ($period == 2) // 半年
    {
        $ext = " AND o.add_time > '" . local_strtotime('-6 months') . "'";
    }
    elseif ($period == 3) // 三个月
    {
        $ext = " AND o.add_time > '" . local_strtotime('-3 months') . "'";
    }
    elseif ($period == 4) // 一个月
    {
        $ext = " AND o.add_time > '" . local_strtotime('-1 months') . "'";
    }
    else
    {
        $ext = '';
    }

    /* 查询该商品销量 */
    $sql = 'SELECT IFNULL(SUM(g.goods_number), 0) ' .
        'FROM ' . $GLOBALS['ecs']->table('order_info') . ' AS o, ' .
            $GLOBALS['ecs']->table('order_goods') . ' AS g ' .
        "WHERE o.order_id = g.order_id " .
        "AND o.order_status = '" . OS_CONFIRMED . "' " .
        "AND o.shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) .
        " AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) .
        " AND g.goods_id = '$goods_id'" . $ext;
    $sales_count = $GLOBALS['db']->getOne($sql);

    if ($sales_count > 0)
    {
        /* 只有在商品销售量大于0时才去计算该商品的排行 */
        $sql = 'SELECT DISTINCT SUM(goods_number) AS num ' .
                'FROM ' . $GLOBALS['ecs']->table('order_info') . ' AS o, ' .
                    $GLOBALS['ecs']->table('order_goods') . ' AS g ' .
                "WHERE o.order_id = g.order_id " .
                "AND o.order_status = '" . OS_CONFIRMED . "' " .
                "AND o.shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) .
                " AND o.pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) . $ext .
                " GROUP BY g.goods_id HAVING num > $sales_count";
        $res = $GLOBALS['db']->query($sql);

        $rank = $GLOBALS['db']->num_rows($res) + 1;

        if ($rank > 10)
        {
            $rank = 0;
        }
    }
    else
    {
        $rank = 0;
    }

    return $rank;
}

/**
 * 获得商品选定的属性的附加总价格
 *
 * @param   integer     $goods_id
 * @param   array       $attr
 *
 * @return  void
 */
function get_attr_amount($goods_id, $attr)
{
    $sql = "SELECT SUM(attr_price) FROM " . $GLOBALS['ecs']->table('goods_attr') .
        " WHERE goods_id='$goods_id' AND " . db_create_in($attr, 'goods_attr_id');

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 取得跟商品关联的礼包列表
 *
 * @param   string  $goods_id    商品编号
 *
 * @return  礼包列表
 */
function get_package_goods_list($goods_id)
{
    $now = gmtime();
    $sql = "SELECT pg.goods_id, ga.act_id, ga.act_name, ga.act_desc, ga.goods_name, ga.start_time,
                   ga.end_time, ga.is_finished, ga.ext_info
            FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS ga, " . $GLOBALS['ecs']->table('package_goods') . " AS pg
            WHERE pg.package_id = ga.act_id
            AND ga.start_time <= '" . $now . "'
            AND ga.end_time >= '" . $now . "'
            AND pg.goods_id = " . $goods_id . "
            GROUP BY ga.act_id
            ORDER BY ga.act_id ";
    $res = $GLOBALS['db']->getAll($sql);

    foreach ($res as $tempkey => $value)
    {
        $subtotal = 0;
        $row = unserialize($value['ext_info']);
        unset($value['ext_info']);
        if ($row)
        {
            foreach ($row as $key=>$val)
            {
                $res[$tempkey][$key] = $val;
            }
        }

        $sql = "SELECT pg.package_id, pg.goods_id, pg.goods_number, pg.admin_id, p.goods_attr, g.goods_sn, g.goods_name, g.market_price, g.goods_thumb, IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS rank_price
                FROM " . $GLOBALS['ecs']->table('package_goods') . " AS pg
                    LEFT JOIN ". $GLOBALS['ecs']->table('goods') . " AS g
                        ON g.goods_id = pg.goods_id
                    LEFT JOIN ". $GLOBALS['ecs']->table('products') . " AS p
                        ON p.product_id = pg.product_id
                    LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp
                        ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]'
                WHERE pg.package_id = " . $value['act_id']. "
                ORDER BY pg.package_id, pg.goods_id";

        $goods_res = $GLOBALS['db']->getAll($sql);

        foreach($goods_res as $key => $val)
        {
            $goods_id_array[] = $val['goods_id'];
            $goods_res[$key]['goods_thumb']  = get_image_path($val['goods_id'], $val['goods_thumb'], true);
            $goods_res[$key]['market_price'] = price_format($val['market_price']);
            $goods_res[$key]['rank_price']   = price_format($val['rank_price']);
            $subtotal += $val['rank_price'] * $val['goods_number'];
        }

        /* 取商品属性 */
        $sql = "SELECT ga.goods_attr_id, ga.attr_value
                FROM " .$GLOBALS['ecs']->table('goods_attr'). " AS ga, " .$GLOBALS['ecs']->table('attribute'). " AS a
                WHERE a.attr_id = ga.attr_id
                AND a.attr_type = 1
                AND " . db_create_in($goods_id_array, 'goods_id');
        $result_goods_attr = $GLOBALS['db']->getAll($sql);

        $_goods_attr = array();
        foreach ($result_goods_attr as $value)
        {
            $_goods_attr[$value['goods_attr_id']] = $value['attr_value'];
        }

        /* 处理货品 */
        $format = '[%s]';
        foreach($goods_res as $key => $val)
        {
            if ($val['goods_attr'] != '')
            {
                $goods_attr_array = explode('|', $val['goods_attr']);

                $goods_attr = array();
                foreach ($goods_attr_array as $_attr)
                {
                    $goods_attr[] = $_goods_attr[$_attr];
                }

                $goods_res[$key]['goods_attr_str'] = sprintf($format, implode('，', $goods_attr));
            }
        }

        $res[$tempkey]['goods_list']    = $goods_res;
        $res[$tempkey]['subtotal']      = price_format($subtotal);
        $res[$tempkey]['saving']        = price_format(($subtotal - $res[$tempkey]['package_price']));
        $res[$tempkey]['package_price'] = price_format($res[$tempkey]['package_price']);
    }

    return $res;
}

/**
 * google api 生成二维码
 */
function generateQRfromGoogle($chl,$widhtHeight ='120',$EC_level='L',$margin='0'){
    return '<img src="http://chart.apis.google.com/chart?chs='.$widhtHeight.'x'.$widhtHeight.
    		'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl.'" rel="nofollow"/>';

}

?>