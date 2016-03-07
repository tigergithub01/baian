<?php

/**
 * ECSHOP 商品分类
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: category.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

// clear_cache_files('promote');


/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

//ajax 加载数据
$is_ajax_fetch = isset($_REQUEST['is_ajax_fetch']) ? intval($_REQUEST['is_ajax_fetch']) : 0;

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
setcookie('ECS[display]', $display, gmtime() + 86400 * 7);

/* 页面的缓存ID */
$cache_id = sprintf('%X', crc32($page . '-' . $_CFG['lang']));

//ajax加载数据的时候不使用缓存
if (1)
// if (!$smarty->is_cached('promote.dwt', $cache_id))
{
    /* 如果页面没有被缓存则重新获取页面的内容 */

    assign_template('p', array());//TODO:？

    $position = assign_ur_here();
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
	$smarty->assign('cat_name',   $cat['cat_name']);

	
	//限时抢购记录数
	$count  = get_promote_goods_list_count('');
	$pages  = ($count > 0) ? ceil($count / $size) : 1;
	
	if ($page > $pages)
	{
		$page = $pages;
	}
	
	//限时抢购记录
	$goodslist = get_promote_goods_list('',$page,$size);
	
    if($display == 'grid')
    {
        if(count($goodslist) % 2 != 0)
        {
            $goodslist[] = array();
        }
    }
    
    $smarty->assign('goods_list',       $goodslist);
    $smarty->assign('helps',                get_shop_help());        // 网店帮助

    assign_pager('promote', '', $count, $size, '', '',$page);
//     assign_dynamic('category'); // 动态内容
    
    //猜你喜欢 &　看了又看
    $may_like_goods = com_sale_get_may_like_goods();
    $smarty->assign('may_like_goods',$may_like_goods);
}



if($is_ajax_fetch==1){
	include_once('includes/cls_json.php');
	$json = new JSON;
	$result = array('error' => '', 'content' => '');
	$result['content'] = $smarty->fetch('library/promote_goods_list.lbi');
	$result['filter'] = array('category' => 0, 'brand' => 0,
			'price_min'=>0, "price_max"=>0,
			"filter_attr" =>'',"filter_ext" =>'',
			"page" => $page, "size" =>$size,"sort"=>'',"order"=>'',
			"record_count" => $count,
			"page_count"=>($count = $count > 0 ? intval(ceil($count / $size)) : 1)
	);
	die($json->encode($result));
}else{
	$smarty->display('promote.dwt');
	$smarty->display('promote.dwt', $cache_id);
}



?>
