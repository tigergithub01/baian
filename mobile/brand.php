<?php

/**
 * ECSHOP 品牌列表
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: brand.php 17217 2011-01-19 06:29:08Z liubo $
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

/* 获得请求的分类 ID */
if (!empty($_REQUEST['id']))
{
    $brand_id = intval($_REQUEST['id']);
}
if (!empty($_REQUEST['brand']))
{
    $brand_id = intval($_REQUEST['brand']);
}
if (empty($brand_id))
{
// 	clear_cache_files();
    /* 缓存编号 */
    $cache_id = sprintf('%X', crc32($_CFG['lang']));
    if (!$smarty->is_cached('brand_list.dwt', $cache_id))
    {
        assign_template();
        $position = assign_ur_here('', $_LANG['all_brand']);
        $smarty->assign('page_title',      $position['title']);    // 页面标题
        $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置
	
        //获取顶级分类-包含分类下的所有品牌
        $categories = get_children_cat_brand_list();
        
        $smarty->assign('categories',      $categories); 
        $smarty->assign('helps',           get_shop_help());       // 网店帮助
    }
    $smarty->display('brand_list.dwt', $cache_id);
    exit();
}

/* 初始化分页信息 */
$page = !empty($_REQUEST['page'])  && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
$cate = !empty($_REQUEST['cat'])   && intval($_REQUEST['cat'])   > 0 ? intval($_REQUEST['cat'])   : 0;

$price_max = isset($_REQUEST['price_max']) && intval($_REQUEST['price_max']) > 0 ? intval($_REQUEST['price_max']) : 0;
$price_min = isset($_REQUEST['price_min']) && intval($_REQUEST['price_min']) > 0 ? intval($_REQUEST['price_min']) : 0;

//属性过滤
$filter_attr_str = isset($_REQUEST['filter_attr']) ? htmlspecialchars(trim($_REQUEST['filter_attr'])) : '0';
$filter_attr_str = trim(urldecode($filter_attr_str));
$filter_attr_str = preg_match('/^((([0-9]+[,]?)+)[.]?)+$/',$filter_attr_str) ? $filter_attr_str : '';
$filter_attr = empty($filter_attr_str) ? '' : explode('.', $filter_attr_str);

/* 促销产品，可用积分，可用红包 ,参数值1.1.1用点号分隔*/
$filter_ext_str = isset($_REQUEST['filter_ext']) ? htmlspecialchars(trim($_REQUEST['filter_ext'])) : '0';
$filter_ext_str = trim(urldecode($filter_ext_str));
$filter_ext_str = preg_match('/^((([0-9]+[,]?)+)[.]?)+$/',$filter_ext_str) ? $filter_ext_str : '';

$filter_ext = empty($filter_ext_str) ? '' : explode('.', $filter_ext_str);
$promote = (!empty($filter_ext) && count($filter_ext)>=1)?intval($filter_ext[0]):0;
$integral = (!empty($filter_ext) && count($filter_ext)>=2)?intval($filter_ext[1]):0;
$bonus = (!empty($filter_ext) && count($filter_ext)>=3)?intval($filter_ext[2]):0;
$filter_ext_str= implode(".",array($promote,$integral,$bonus));

/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
setcookie('ECS[display]', $display, gmtime() + 86400 * 7);


$is_ajax = isset($_REQUEST['is_ajax']) ? intval($_REQUEST['is_ajax']) : 0;
if($is_ajax==1){
	$build_uri = build_uri('brand', array('cid'=>$cate, 'bid'=>$brand_id,/*  'price_min'=>$price_min, 'price_max'=> $price_max,  'filter_attr'=>$filter_attr_str,*/'sort' => $sort, 'order' => $order,'filter_ext'=>$filter_ext_str), '');
	lib_main_make_json_result('',$build_uri);
	exit;
}


$is_ajax_fetch = isset($_REQUEST['is_ajax_fetch']) ? intval($_REQUEST['is_ajax_fetch']) : 0;

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */
/* 页面的缓存ID */
$cache_id = sprintf('%X', crc32($brand_id . '-' . $display . '-' . $sort . '-' . $order . '-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' . $_CFG['lang'] . '-' . $cate . '-'.$filter_ext_str));

// if (!$smarty->is_cached('brand.dwt', $cache_id))
//ajax加载数据的时候不使用缓存
if (1)
{
    
	$brand_info = get_brand_info($brand_id);

    if (empty($brand_info))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    $smarty->assign('data_dir',    DATA_DIR);
    $smarty->assign('keywords',    htmlspecialchars($brand_info['brand_desc']));
    $smarty->assign('description', htmlspecialchars($brand_info['brand_desc']));

    /* 赋值固定内容 */
    assign_template();
    $position = assign_ur_here($cate, $brand_info['brand_name']);
    $smarty->assign('page_title',     $position['title']);   // 页面标题
    $smarty->assign('ur_here',        $position['ur_here']); // 当前位置
    $smarty->assign('brand_id',       $brand_id);
    $smarty->assign('category',       $cate);

    $smarty->assign('categories',     get_categories_tree());        // 分类树
    $smarty->assign('helps',          get_shop_help());              // 网店帮助
    $smarty->assign('top_goods',      get_top10());                  // 销售排行
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('brand_cat_list', brand_related_cat($brand_id)); // 相关分类
    $smarty->assign('feed_url',       ($_CFG['rewrite'] == 1) ? "feed-b$brand_id.xml" : 'feed.php?brand=' . $brand_id);
    
    
    //促销产品,可用积分，可用红包，排序url
    $promote_url = build_uri ( 'brand', array (
    		'cid' => $cate,
    		'bid' => $brand_id,
    		'sort' => $sort,
    		'order' => $order,
    		'filter_ext' => implode ( ".", array (
    				$promote == 1 ? 0 : 1,
    				$integral,
    				$bonus
    		) )
    ), $brand_info['brand_name'] );
    
    $integral_url = build_uri ( 'brand', array (
    		'cid' => $cate,
    		'bid' => $brand_id,
    		'sort' => $sort,
    		'order' => $order,
    		'filter_ext' => implode ( ".", array (
    				$promote,
    				$integral == 1 ? 0 : 1,
    				$bonus
    		) )
    ), $brand_info['brand_name'] );
    
    $bonus_url = build_uri ( 'brand', array (
    		'cid' => $cate,
    		'bid' => $brand_id,
    		'sort' => $sort,
    		'order' => $order,
    		'filter_ext' => implode ( ".", array (
    				$promote,
    				$integral,
    				$bonus == 1 ? 0 : 1
    		) )
    ), $brand_info['brand_name'] );
    
    
    $price_sort_url = build_uri ( 'brand', array (
    		'cid' => $cate,
    		'bid' => $brand_id,
    		'sort' => 'shop_price',
    		'order' => ($order=='ASC'?"DESC":"ASC"),
    		'filter_ext' => $filter_ext_str
    ), $brand_info['brand_name'] );
    
    $default_sort_url = build_uri ( 'brand', array (
    		'cid' => $cate,
    		'bid' => $brand_id,
    		'filter_ext' => $filter_ext_str
    ), $brand_info['brand_name'] );
    
    $sale_sort_url = build_uri ( 'brand', array (
    		'cid' => $cate,
    		'bid' => $brand_id,
    		'sort' => 'last_update',
    		'order' => ($order=='ASC'?"DESC":"ASC"),
    		'filter_ext' => $filter_ext_str
    ), $brand_info['brand_name'] );
    
    
    $smarty->assign('promote_flag',    $promote);
    $smarty->assign('integral_flag',    $integral);
    $smarty->assign('bonus_flag',    $bonus);
    $smarty->assign('promote_url',    $promote_url);
    $smarty->assign('integral_url',    $integral_url);
    $smarty->assign('bonus_url',    $bonus_url);
    $smarty->assign('price_sort_url',    $price_sort_url);
    $smarty->assign('default_sort_url',    $default_sort_url);
    $smarty->assign('sale_sort_url',    $sale_sort_url);

    /* 调查 */
    $vote = get_vote();
    if (!empty($vote))
    {
        $smarty->assign('vote_id',     $vote['id']);
        $smarty->assign('vote',        $vote['content']);
    }

    $smarty->assign('best_goods',      brand_recommend_goods('best', $brand_id, $cate));
//     $smarty->assign('promotion_goods', brand_recommend_goods('promote', $brand_id, $cate));
    $smarty->assign('promotion_goods', get_promote_goods()); // 特价商品,限时抢购
    $smarty->assign('brand',           $brand_info);
    $smarty->assign('promotion_info', get_promotion_info());

    $count = goods_count_by_brand($brand_id, $cate,$promote,$integral,$bonus);

    $goodslist = brand_get_goods($brand_id, $cate, $size, $page, $sort, $order,$promote,$integral,$bonus);

    if($display == 'grid')
    {
        if(count($goodslist) % 2 != 0)
        {
            $goodslist[] = array();
        }
    }
    $smarty->assign('goods_list',      $goodslist);
    $smarty->assign('script_name', 'brand');

    assign_pager('brand',              $cate, $count, $size, $sort, $order, $page, '', $brand_id, 0, 0, $display, '','','',$filter_ext_str); // 分页
    assign_dynamic('brand'); // 动态内容
    
    //猜你喜欢，看了又看
    $may_like_goods = com_sale_get_may_like_goods(null, null, $brand_id);
    $smarty->assign('may_like_goods',$may_like_goods);
    
    //底部导航,关键词语标签 2015-10-04 added by tiger.guo
    include_once ('includes/extend/cls_article.php');
    $cls_article = new cls_article();
    $nav_bottom_article = $cls_article->get_article(157);
    $smarty->assign('nav_bottom',$nav_bottom_article);
    
}

if($is_ajax_fetch==1){
	include_once('includes/cls_json.php');
	$json = new JSON;
	$result = array('error' => '', 'content' => '');
	$result['content'] = $smarty->fetch('library/goods_list.lbi');
	$result['filter'] = array('category' => $cate, 'brand' => $brand_id,
			'price_min'=>$price_min, "price_max"=>$price_max,
			"filter_attr" =>$filter_attr_str,"filter_ext" =>$filter_ext_str,
			"page" => $page, "size" =>$size,"sort"=>$sort,"order"=>$order,
			"record_count" => $count,
			"page_count"=>($count = $count > 0 ? intval(ceil($count / $size)) : 1)
	);
	die($json->encode($result));
}else{
	$smarty->display('brand.dwt');
// 	$smarty->display('brand.dwt', $cache_id);
}


/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得指定品牌的详细信息
 *
 * @access  private
 * @param   integer $id
 * @return  void
 */
function get_brand_info($id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('brand') . " WHERE brand_id = '$id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 获得指定品牌下的推荐和促销商品
 *
 * @access  private
 * @param   string  $type
 * @param   integer $brand
 * @return  array
 */
function brand_recommend_goods($type, $brand, $cat = 0)
{
    static $result = NULL;

    $time = gmtime();

    if ($result === NULL)
    {
        if ($cat > 0)
        {
            $cat_where = "AND " . get_children($cat);
        }
        else
        {
            $cat_where = '';
        }

        $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                    "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                    'promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, ' .
                    'b.brand_name, g.is_best, g.is_new, g.is_hot, g.is_promote ' .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp '.
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
                "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand' AND " .
                    "(g.is_best = 1 OR (g.is_promote = 1 AND promote_start_date <= '$time' AND ".
                    "promote_end_date >= '$time')) $cat_where" .
               'ORDER BY g.sort_order, g.last_update DESC';
        $result = $GLOBALS['db']->getAll($sql);
    }

    /* 取得每一项的数量限制 */
    $num = 0;
    $type2lib = array('best'=>'recommend_best', 'new'=>'recommend_new', 'hot'=>'recommend_hot', 'promote'=>'recommend_promotion');
    $num = get_library_number($type2lib[$type]);

    $idx = 0;
    $goods = array();
    foreach ($result AS $row)
    {
        if ($idx >= $num)
        {
            break;
        }

        if (($type == 'best' && $row['is_best'] == 1) ||
            ($type == 'promote' && $row['is_promote'] == 1 &&
            $row['promote_start_date'] <= $time && $row['promote_end_date'] >= $time))
        {
            if ($row['promote_price'] > 0)
            {
                $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
                $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
            }
            else
            {
                $goods[$idx]['promote_price'] = '';
            }

            $goods[$idx]['id']           = $row['goods_id'];
            $goods[$idx]['name']         = $row['goods_name'];
            $goods[$idx]['brief']        = $row['goods_brief'];
            $goods[$idx]['brand_name']   = $row['brand_name'];
            $goods[$idx]['short_style_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                               sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $goods[$idx]['market_price'] = price_format($row['market_price']);
            $goods[$idx]['shop_price']   = price_format($row['shop_price']);
            $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
            $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
            $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

            $idx++;
        }
    }

    return $goods;
}

/**
 * 获得指定的品牌下的商品总数
 *
 * @access  private
 * @param   integer     $brand_id
 * @param   integer     $cate
 * @return  integer
 */
function goods_count_by_brand($brand_id, $cate = 0,$promote,$integral,$bonus)
{
	$where=" brand_id = '$brand_id' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ";
	
	if ($cate > 0)
	{
		$where .= " AND " . get_children($cate);
	}
	
	if($promote==1){
		$where .= " AND (g.is_buy_gift = 1 and g.gift_start_date <=".gmtime().' AND g.gift_end_date >='.gmtime().') ';
	}
	
	if($integral==1){
		$where .= " AND g.integral > 0 ";
	}
	
	if($bonus==1){
		$where .= " AND g.bonus > 0 ";
	}
	
	$sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('goods'). ' AS g '.
            "WHERE $where";

    

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 获得品牌下的商品
 *
 * @access  private
 * @param   integer  $brand_id
 * @return  array
 */
function brand_get_goods($brand_id, $cate, $size, $page, $sort, $order,$promote,$integral,$bonus)
{
    $where = ($cate > 0) ? 'AND ' . get_children($cate) : '';
    
    /* if($promote==1){
    	$where .= " AND (g.is_promote = 1 and g.promote_price >0 and g.promote_start_date <=".gmtime().' AND g.promote_end_date >='.gmtime().') ';
    } */
if($promote==1){
		$where .= " AND (g.is_buy_gift = 1 and g.gift_start_date <=".gmtime().' AND g.gift_end_date >='.gmtime().') ';
	}
    
    if($integral==1){
    	$where .= " AND g.integral > 0 ";
    }
    
    if($bonus==1){
    	$where .= " AND g.bonus > 0 ";
    }

    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand_id' $where ".
            "ORDER BY $sort $order";

    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        $arr[$row['goods_id']]['goods_id']      = $row['goods_id'];
        if($GLOBALS['display'] == 'grid')
        {
            $arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
        $arr[$row['goods_id']]['market_price']  = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
        $arr[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$row['goods_id']]['goods_brief']   = $row['goods_brief'];
        $arr[$row['goods_id']]['goods_thumb']   = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
        
        //评论数
        $count = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('comment') . " where comment_type=0 and id_value ='$row[goods_id]' and status=1");
        $arr[$row['goods_id']]['review_count']        = $count;
    }

    return $arr;
}

/**
 * 获得与指定品牌相关的分类
 *
 * @access  public
 * @param   integer $brand
 * @return  array
 */
function brand_related_cat($brand)
{
    $arr[] = array('cat_id' => 0,
                 'cat_name' => $GLOBALS['_LANG']['all_category'],
                 'url'      => build_uri('brand', array('bid' => $brand), $GLOBALS['_LANG']['all_category']));

    $sql = "SELECT c.cat_id, c.cat_name, COUNT(g.goods_id) AS goods_count FROM ".
            $GLOBALS['ecs']->table('category'). " AS c, ".
            $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE g.brand_id = '$brand' AND c.cat_id = g.cat_id ".
            "GROUP BY g.cat_id";
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['url'] = build_uri('brand', array('cid' => $row['cat_id'], 'bid' => $brand), $row['cat_name']);
        $arr[] = $row;
    }

    return $arr;
}

?>