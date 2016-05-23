<?php

/**
 * ECSHOP 商品相关函数库
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_goods.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 商品推荐usort用自定义排序行数
 */
function goods_sort($goods_a, $goods_b)
{
    if ($goods_a['sort_order'] == $goods_b['sort_order']) {
        return 0;
    }
    return ($goods_a['sort_order'] < $goods_b['sort_order']) ? -1 : 1;

}

/**
 * 根据商品分类获取同级分类列表
 * @param number $goods_id
 */
function get_sibling_category($cat_id = 0){
	$sql = "SELECT parent_id FROM ". $GLOBALS['ecs']->table('category'). " WHERE cat_id = '$cat_id'";;
	$parent_id = $GLOBALS['db']->getOne($sql);
	return get_direct_children_category($parent_id);
}


/**
 * 获取直接分类
 * @param number $cat_id
 * @return Ambigous <multitype:, void>
 */
function get_direct_children_category($cat_id = 0){
	$sql = 'SELECT cat_id,cat_name ,parent_id,is_show ' .
			'FROM ' . $GLOBALS['ecs']->table('category') .
			"WHERE parent_id = '$cat_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";
	
	$res = $GLOBALS['db']->getAll($sql);
	$cat_arr = array();
	foreach ($res AS $row)
	{
		$cat_arr[$row['cat_id']]['cat_id']   = $row['cat_id'];
		$cat_arr[$row['cat_id']]['cat_name'] = $row['cat_name'];
		$cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);
	}
	return $cat_arr;
}




/**
 * 获取某分类下的所有品牌
 * @param unknown $cat_id
 * @return unknown
 */
function get_cat_brand_list($cat_id){
	$children = get_children($cat_id);
	$sql = "SELECT distinct b.brand_id, b.brand_name, b.brand_logo, b.brand_desc  ".
			"FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ".
			$GLOBALS['ecs']->table('goods') . " AS g LEFT JOIN ". $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
			"WHERE g.brand_id = b.brand_id AND ($children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), array_keys(cat_list($cat_id, 0, false))))) . ") AND b.is_show = 1 " .
			"AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
			"ORDER BY b.sort_order, b.brand_id ASC";
	
	$brands = $GLOBALS['db']->getAll($sql);
	foreach ($brands as $key => $row) {
		$brands[$key]['url'] = build_uri('brand', ['bid'=>$row['brand_id']]);
	}
	return $brands;
}

/**
 * 获取直接分类-包含分类下的所有品牌雷彪
 * @param unknown $cat_id
 */
function get_children_cat_brand_list($cat_id){
	$cat_list = get_direct_children_category($cat_id);
	foreach ($cat_list as $key => $cat) {
		$sub_cat_id = $cat['cat_id'];
		$cat_list[$key]['brands'] = get_cat_brand_list($sub_cat_id);
	}
	return $cat_list;
}


/**
 * 获得指定分类同级的所有分类以及该分类下的子分类 - 同时获取相关品牌信息
 * @param number $cat_id
 * @return multitype:
 */
function get_categories_tree_with_brand($cat_id = 0)
{
	$categories_tree = get_categories_tree($cat_id);
	foreach ($categories_tree as $key => $cat) {
		$sub_cat_id = $cat['cat_id'];
		$categories_tree[$key]['brands'] = get_cat_brand_list($sub_cat_id);
	}
	return $categories_tree;
}


/**
 * 获得指定分类同级的所有分类以及该分类下的子分类
 *
 * @access  public
 * @param   integer     $cat_id     分类编号
 * @return  array
 */
function get_categories_tree($cat_id = 0)
{
    if ($cat_id > 0)
    {
        $sql = 'SELECT parent_id FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
        $parent_id = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $parent_id = 0;
    }

    /*
     判断当前分类中全是是否是底级分类，
     如果是取出底级分类上级分类，
     如果不是取当前分类及其下的子分类
    */
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$parent_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $parent_id == 0)
    {
        /* 获取当前分类及其子分类 */
        $sql = 'SELECT cat_id,cat_name ,parent_id,is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$parent_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";

        $res = $GLOBALS['db']->getAll($sql);

        foreach ($res AS $row)
        {
            if ($row['is_show'])
            {
                $cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
                $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
                $cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

                if (isset($row['cat_id']) != NULL)
                {
                    $cat_arr[$row['cat_id']]['cat_id'] = get_child_tree($row['cat_id']);
                }
            }
        }
    }
    if(isset($cat_arr))
    {
        return $cat_arr;
    }
}

function get_child_tree($tree_id = 0)
{
    $three_arr = array();
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('category') . " WHERE parent_id = '$tree_id' AND is_show = 1 ";
    if ($GLOBALS['db']->getOne($sql) || $tree_id == 0)
    {
        $child_sql = 'SELECT cat_id, cat_name, parent_id, is_show ' .
                'FROM ' . $GLOBALS['ecs']->table('category') .
                "WHERE parent_id = '$tree_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";
        $res = $GLOBALS['db']->getAll($child_sql);
        foreach ($res AS $row)
        {
            if ($row['is_show'])

               $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
               $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
               $three_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);

               if (isset($row['cat_id']) != NULL)
                   {
                       $three_arr[$row['cat_id']]['cat_id'] = get_child_tree($row['cat_id']);

            }
        }
    }
    return $three_arr;
}

/**
 * 获取分类下的精品商品
 * @param number $cat_id
 * @return Ambigous <multitype:, void>
 */
function get_direct_children_bet_goods($cat_id = 0){
	/* $sql = 'SELECT cat_id,cat_name ,parent_id,is_show ' .
			'FROM ' . $GLOBALS['ecs']->table('category') .
			"WHERE parent_id = '$cat_id' AND is_show = 1 ORDER BY sort_order ASC, cat_id ASC";

	$res = $GLOBALS['db']->getAll($sql);
	$cat_arr = array();
	foreach ($res AS $row)
	{
		$cat_arr[$row['cat_id']]['cat_id']   = $row['cat_id'];
		$cat_arr[$row['cat_id']]['cat_name'] = $row['cat_name'];
		$cat_arr[$row['cat_id']]['url']  = build_uri('category', array('cid' => $row['cat_id']), $row['cat_name']);
	} */
	
	$cat_arr = get_direct_children_category($cat_id,$num = 10);
	foreach ($cat_arr as $key => $value) {
		$cat_arr[$key]['best_goods'] = index_get_cat_id_goods_best_list($cat_arr[$key]['cat_id'],$num);
	}
	
	return $cat_arr;
}

/**
 * 调用当前分类的销售排行榜
 *
 * @access  public
 * @param   string  $cats   查询的分类
 * @return  array
 */
function get_top10($cats = '')
{
    $cats = get_children($cats);
    $where = !empty($cats) ? "AND ($cats OR " . get_extension_goods($cats) . ") " : '';

    /* 排行统计的时间 */
    switch ($GLOBALS['_CFG']['top10_time'])
    {
        case 1: // 一年
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 365 * 86400) . "'";
        break;
        case 2: // 半年
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 180 * 86400) . "'";
        break;
        case 3: // 三个月
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 90 * 86400) . "'";
        break;
        case 4: // 一个月
            $top10_time = "AND o.order_sn >= '" . date('Ymd', gmtime() - 30 * 86400) . "'";
        break;
        default:
            $top10_time = '';
    }

    $sql = 'SELECT g.goods_id, g.goods_name, g.shop_price, g.goods_thumb, SUM(og.goods_number) as goods_number ' .
           'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g, ' .
                $GLOBALS['ecs']->table('order_info') . ' AS o, ' .
                $GLOBALS['ecs']->table('order_goods') . ' AS og ' .
           "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 $where $top10_time " ;
    //判断是否启用库存，库存数量是否大于0
    if ($GLOBALS['_CFG']['use_storage'] == 1)
    {
        $sql .= " AND g.goods_number > 0 ";
    }
    $sql .= ' AND og.order_id = o.order_id AND og.goods_id = g.goods_id ' .
           "AND (o.order_status = '" . OS_CONFIRMED .  "' OR o.order_status = '" . OS_SPLITED . "') " .
           "AND (o.pay_status = '" . PS_PAYED . "' OR o.pay_status = '" . PS_PAYING . "') " .
           "AND (o.shipping_status = '" . SS_SHIPPED . "' OR o.shipping_status = '" . SS_RECEIVED . "') " .
           'GROUP BY g.goods_id ORDER BY goods_number DESC, g.goods_id DESC LIMIT ' . $GLOBALS['_CFG']['top_number'];
           
    $arr = $GLOBALS['db']->getAll($sql);

    for ($i = 0, $count = count($arr); $i < $count; $i++)
    {
        $arr[$i]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                    sub_str($arr[$i]['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $arr[$i]['goods_name'];
        $arr[$i]['url']        = build_uri('goods', array('gid' => $arr[$i]['goods_id']), $arr[$i]['goods_name']);
        $arr[$i]['thumb'] = get_image_path($arr[$i]['goods_id'], $arr[$i]['goods_thumb'],true);
        $arr[$i]['price'] = price_format($arr[$i]['shop_price']);
    }

    return $arr;
}

/**
 * 获得推荐商品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot
 * @return  array
 */
function get_recommend_goods($type = '', $cats = '')
{
    if (!in_array($type, array('best', 'new', 'hot')))
    {
        return array();
    }

    //取不同推荐对应的商品
    static $type_goods = array();
    if (empty($type_goods[$type]))
    {
        //初始化数据
        $type_goods['best'] = array();
        $type_goods['new'] = array();
        $type_goods['hot'] = array();
        $data = read_static_cache('recommend_goods');
        if ($data === false)
        {
            $sql = 'SELECT g.goods_id, g.is_best, g.is_new, g.is_hot, g.is_promote, b.brand_name,g.sort_order ' .
               ' FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
               ' LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
               ' WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND (g.is_best = 1 OR g.is_new =1 OR g.is_hot = 1)'.
               ' ORDER BY g.sort_order, g.last_update DESC';
            $goods_res = $GLOBALS['db']->getAll($sql);
            //定义推荐,最新，热门，促销商品
            $goods_data['best'] = array();
            $goods_data['new'] = array();
            $goods_data['hot'] = array();
            $goods_data['brand'] = array();
            if (!empty($goods_res))
            {
                foreach($goods_res as $data)
                {
                    if ($data['is_best'] == 1)
                    {
                        $goods_data['best'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['is_new'] == 1)
                    {
                        $goods_data['new'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['is_hot'] == 1)
                    {
                        $goods_data['hot'][] = array('goods_id' => $data['goods_id'], 'sort_order' => $data['sort_order']);
                    }
                    if ($data['brand_name'] != '')
                    {
                        $goods_data['brand'][$data['goods_id']] = $data['brand_name'];
                    }
                }
            }
            write_static_cache('recommend_goods', $goods_data);
        }
        else
        {
            $goods_data = $data;
        }

        $time = gmtime();
        $order_type = $GLOBALS['_CFG']['recommend_order'];

        //按推荐数量及排序取每一项推荐显示的商品 order_type可以根据后台设定进行各种条件显示
        static $type_array = array();
        $type2lib = array('best'=>'recommend_best', 'new'=>'recommend_new', 'hot'=>'recommend_hot');
        if (empty($type_array))
        {
            foreach($type2lib as $key => $data)
            {
                if (!empty($goods_data[$key]))
                {
                    $num = get_library_number($data);
                    $data_count = count($goods_data[$key]);
                    $num = $data_count > $num  ? $num : $data_count;
                    if ($order_type == 0)
                    {
                        //usort($goods_data[$key], 'goods_sort');
                        $rand_key = array_slice($goods_data[$key], 0, $num);
                        foreach($rand_key as $key_data)
                        {
                            $type_array[$key][] = $key_data['goods_id'];
                        }
                    }
                    else
                    {
                        $rand_key = array_rand($goods_data[$key], $num);
                        if ($num == 1)
                        {
                            $type_array[$key][] = $goods_data[$key][$rand_key]['goods_id'];
                        }
                        else
                        {
                            foreach($rand_key as $key_data)
                            {
                                $type_array[$key][] = $goods_data[$key][$key_data]['goods_id'];
                            }
                        }
                    }
                }
                else
                {
                    $type_array[$key] = array();
                }
            }
        }
		

        //取出所有符合条件的商品数据，并将结果存入对应的推荐类型数组中
        $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img, RAND() AS rnd " .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ";
        $type_merge = array_merge($type_array['new'], $type_array['best'], $type_array['hot']);
        $type_merge = array_unique($type_merge);
        $sql .= ' WHERE g.goods_id ' . db_create_in($type_merge);
        $sql .= ' ORDER BY g.sort_order, g.last_update DESC';

        $result = $GLOBALS['db']->getAll($sql);
        foreach ($result AS $idx => $row)
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
            $goods[$idx]['brand_name']   = isset($goods_data['brand'][$row['goods_id']]) ? $goods_data['brand'][$row['goods_id']] : '';
            $goods[$idx]['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);

            $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                               sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $goods[$idx]['short_style_name']   = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
            $goods[$idx]['market_price'] = price_format($row['market_price']);
            $goods[$idx]['shop_price']   = price_format($row['shop_price']);
            $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
            $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
            $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
			/***** 促销商品倒计时  Start add by  www.68ecshop.com****/

        $time = gmtime();

        if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date'])

        {

             $goods[$idx]['gmt_end_time']  = local_date('M d, Y H:i:s',$row['promote_end_date']);

        }

        else

        {

            $goods[$idx]['gmt_end_time'] = 0;

        }

	   /*****促销商品倒计时 End add by  www.68ecshop.com****/
            if (in_array($row['goods_id'], $type_array['best']))
            {
                $type_goods['best'][] = $goods[$idx];
            }
            if (in_array($row['goods_id'], $type_array['new']))
            {
                $type_goods['new'][] = $goods[$idx];
            }
            if (in_array($row['goods_id'], $type_array['hot']))
            {
                $type_goods['hot'][] = $goods[$idx];
            }
        }
    }
    return $type_goods[$type];
}

/**
 * 获得促销商品
 *
 * @access  public
 * @return  array
 */
function get_promote_goods($cats = '')
{
    /* $time = gmtime();
    $order_type = $GLOBALS['_CFG']['recommend_order'];

//     $num = get_library_number("recommend_promotion"); //取得促销lbi的数量限制
	$num = 20;
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name, " .
                "g.is_best, g.is_new, g.is_hot, g.is_promote, RAND() AS rnd " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' .
            " AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' ";
    $sql .= $order_type == 0 ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY rnd';
    $sql .= " LIMIT $num ";
    $result = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($result AS $idx => $row)
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
        $goods[$idx]['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['short_style_name']   = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
		$time = gmtime();
         if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date'])
        {
        $goods[$idx]['gmt_end_time'] = local_date('M d, Y H:i:s',$row['promote_end_date']);
       }
       else
        {
       $goods[$idx]['gmt_end_time'] = 0;
     
      }
    } */

	$goods = get_promote_goods_list($cats);
    return $goods;
}



/**
 * 限时抢购列表 
 * @param string $cats
 * @param number $page
 * @param number $size
 * @return Ambigous <multitype:, number, void>
 */
function get_promote_goods_list($cats = '',$page = 1, $size = 20, $date = null)
{
	$time = isset($date)? $date:gmtime();
	$order_type = $GLOBALS['_CFG']['recommend_order'];
	$sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
			"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
			"promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name, " .
			"g.is_best, g.is_new, g.is_hot, g.is_promote, RAND() AS rnd " .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
			"LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
			"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
			'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 '.
			" AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' ";
	
	$sql .= $order_type == 0 ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY rnd';
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page-1) * $size);
	$goods = array();
	while ($row = $GLOBALS['db']->fetchRow($res)){
		$idx = $row['goods_id'];
		if ($row['promote_price'] > 0)
		{
			$promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date'],$time);
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
		$goods[$idx]['goods_style_name']   = add_style($row['goods_name'],$row['goods_name_style']);
		$goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
		$goods[$idx]['short_style_name']   = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
		$goods[$idx]['market_price'] = price_format($row['market_price']);
		$goods[$idx]['shop_price']   = price_format($row['shop_price']);
		$goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
		$goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
		$goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
		$goods[$idx]['goods_store'] = get_goods_store($row['goods_id']);
		
		/* $time = gmtime(); */
		if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date'])
		{
// 			$goods[$idx]['gmt_end_time'] = local_date('M d, Y H:i:s',$row['promote_end_date']);
// 			$goods['gmt_end_time']  = local_date('M d, Y 0:0:0',$row['promote_end_date']);
			$goods[$idx]['gmt_end_time'] = local_date('M d, Y H:i:s',$row['promote_end_date']);
		}
		else
		{
			$goods[$idx]['gmt_end_time'] = 0;
		
		}
		
		//是否是当天抢购
		$goods[$idx]['curr'] = (local_date('M d, Y 0:0:0',$time) === local_date('M d, Y 0:0:0',gmtime()));
		
		
	}
	return $goods;
}



/**
 * 限时抢购列表 - 记录条数
 * @param string $cats
 * @return unknown
 */
function get_promote_goods_list_count($cats = '',$date = null)
{
	$time = isset($date)? $date:gmtime();
	$order_type = $GLOBALS['_CFG']['recommend_order'];
	$sql = "SELECT COUNT(1) " .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
			"LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
			"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
			'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' .
			" AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' ";
	$sql .= $order_type == 0 ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY rnd';
	$count = $GLOBALS['db']->getOne($sql);
	return $count;
}

/**
 * 获得指定分类下的推荐商品
 *
 * @access  public
 * @param   string      $type       推荐类型，可以是 best, new, hot, promote
 * @param   string      $cats       分类的ID
 * @param   integer     $brand      品牌的ID
 * @param   integer     $min        商品价格下限
 * @param   integer     $max        商品价格上限
 * @param   string      $ext        商品扩展查询
 * @return  array
 */
function get_category_recommend_goods($type = '', $cats = '', $brand = 0, $min =0,  $max = 0, $ext='')
{
    $brand_where = ($brand > 0) ? " AND g.brand_id = '$brand'" : '';

    $price_where = ($min > 0) ? " AND g.shop_price >= $min " : '';
    $price_where .= ($max > 0) ? " AND g.shop_price <= $max " : '';

    $sql =  'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' . $brand_where . $price_where . $ext;
    $num = 0;
    $type2lib = array('best'=>'recommend_best', 'new'=>'recommend_new', 'hot'=>'recommend_hot', 'promote'=>'recommend_promotion');
    $num = get_library_number($type2lib[$type]);

    switch ($type)
    {
        case 'best':
            $sql .= ' AND is_best = 1';
            break;
        case 'new':
            $sql .= ' AND is_new = 1';
            break;
        case 'hot':
            $sql .= ' AND is_hot = 1';
            break;
        case 'promote':
            $time = gmtime();
            $sql .= " AND is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time'";
            break;
    }

    if (!empty($cats))
    {
        $sql .= " AND (" . $cats . " OR " . get_extension_goods($cats) .")";
    }

    $order_type = $GLOBALS['_CFG']['recommend_order'];
    $sql .= ($order_type == 0) ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY RAND()';
    $res = $GLOBALS['db']->selectLimit($sql, $num);

    $idx = 0;
    $goods = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
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
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                       sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        $goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'], $row['goods_name_style']);
        $idx++;
    }

    return $goods;
}

/**
 * 获得商品的详细信息
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  void
 */
function get_goods_info($goods_id)
{
    $time = gmtime();
    $sql = 'SELECT g.*, c.measure_unit, b.brand_id, b.brand_name AS goods_brand, m.type_money AS bonus_money,c.give_integral_rate AS give_integral_rate, ' .
                'IFNULL(AVG(r.comment_rank), 0) AS comment_rank, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS rank_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS c ON g.cat_id = c.cat_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON g.brand_id = b.brand_id ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('comment') . ' AS r '.
                'ON r.id_value = g.goods_id AND comment_type = 0 AND r.parent_id = 0 AND r.status = 1 ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('bonus_type') . ' AS m ' .
                "ON g.bonus_type_id = m.type_id AND m.send_start_date <= '$time' AND m.send_end_date >= '$time'" .
            " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE g.goods_id = '$goods_id' AND g.is_delete = 0 " .
            "GROUP BY g.goods_id";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        /* 用户评论级别取整 */
        $row['comment_rank']  = ceil($row['comment_rank']) == 0 ? 5 : ceil($row['comment_rank']);

        /* 获得商品的销售价格 */
        $row['market_price']        = price_format($row['market_price']);
        $row['shop_price_formated'] = price_format($row['shop_price']);
        
        

        /* 修正促销价格 */
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        /* 处理商品水印图片 */
        $watermark_img = '';

        if ($promote_price != 0)
        {
            $watermark_img = "watermark_promote";
        }
        elseif ($row['is_new'] != 0)
        {
            $watermark_img = "watermark_new";
        }
        elseif ($row['is_best'] != 0)
        {
            $watermark_img = "watermark_best";
        }
        elseif ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot';
        }

        if ($watermark_img != '')
        {
            $row['watermark_img'] =  $watermark_img;
        }

        $row['promote_price_org'] =  $promote_price;
        $row['promote_price'] =  price_format($promote_price);

        /* 修正重量显示 */
        $row['goods_weight']  = (intval($row['goods_weight']) > 0) ?
            $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
            ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正上架时间显示 */
        $row['add_time']      = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);

      

        /* 是否显示商品库存数量 */
        $row['goods_number']  = ($GLOBALS['_CFG']['use_storage'] == 1) ? $row['goods_number'] : '';

        /**TODO:购买该商品最多可以使用的积分金额，此处逻辑存在问题**/
        /* 修正积分：转换为可使用多少积分（原来是可以使用多少钱的积分） */
        $row['integral']      = $GLOBALS['_CFG']['integral_scale'] ? round($row['integral'] * 100 / $GLOBALS['_CFG']['integral_scale']) : 0;
        
        
        /***修正赠送消费积分，如果商品中无设置，则根据类别来进行设置 **/
        if($row['give_integral']==-1){
        	//查询类别中的消费积分赠送设置
        	$row['give_integral'] = $GLOBALS['db']->getOne("SELECT give_integral FROM " . $GLOBALS['ecs']->table('category'). " WHERE cat_id = '$row[cat_id]' LIMIT 1" );
        }
        
        /* 如果商品和类别中"消费积分赠送设置"都为-1时，类别中的“消费多少元赠送消费积分1分”规则生效 */
        /*购买该商品时赠送消费积分数,-1表示按商品价格赠送*/
        $row['give_integral'] = ($row['give_integral']==-1)?(round($row['shop_price']/$row['give_integral_rate'])):$row['give_integral'];
        
        /*赠送的积分折算成的购买金额， [积分换算比例，每100积分可抵多少元现金]*/
        $row['give_integral_amt'] = round($row['give_integral'] * $GLOBALS['_CFG']['integral_scale']  / 100);
        

        /* 赠送红包金额 */
        $row['bonus_money_formatted']   = ($row['bonus_money'] == 0) ? 0 : price_format($row['bonus_money'], false);

        /* 修正商品图片 */
        $row['goods_img']   = get_image_path($goods_id, $row['goods_img']);
        $row['goods_thumb'] = get_image_path($goods_id, $row['goods_thumb'], true);
        
        /*商品抢购倒计时*/
        $time = gmtime();
        if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date']){
//         	$row['gmt_end_time']  = local_date('M d, Y H:i:s',$row['promote_end_date']);
        	$row['gmt_end_time']  = local_date('M d, Y H:i:s',$row['promote_end_date']);
        }else{
        	$row['gmt_end_time'] = null;
        }
        
        //根据时间段修正是否买几送几
        $row['is_buy_gift'] = ($row['is_buy_gift']==1 && gmtime() >= $row['gift_start_date'] && gmtime() <= $row['gift_end_date'])?1:0;
        
        return $row;
    }
    else
    {
        return false;
    }
}

/**
 * 获得商品的属性和规格
 *
 * @access  public
 * @param   integer $goods_id
 * @return  array
 */
function get_goods_properties($goods_id)
{
    /* 对属性进行重新排序和分组 */
    $sql = "SELECT attr_group ".
            "FROM " . $GLOBALS['ecs']->table('goods_type') . " AS gt, " . $GLOBALS['ecs']->table('goods') . " AS g ".
            "WHERE g.goods_id='$goods_id' AND gt.cat_id=g.goods_type";
    $grp = $GLOBALS['db']->getOne($sql);
  
    if (!empty($grp))
    {
        $groups = explode("\n", strtr($grp, "\r", ''));
    }
  
    /* 获得商品的规格 */
    $sql = "SELECT a.attr_id, a.attr_name, a.attr_group, a.is_linked, a.attr_type, ".
                "g.goods_attr_id, g.attr_value, g.attr_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
            "WHERE g.goods_id = '$goods_id' " .
            'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';
    $res = $GLOBALS['db']->getAll($sql);
  
    $arr['pro'] = array();     // 属性
    $arr['spe'] = array();     // 规格
    $arr['lnk'] = array();     // 关联的属性
  
    foreach ($res AS $row)
    {
        $row['attr_value'] = str_replace("\n", '<br />', $row['attr_value']);
  
        if ($row['attr_type'] == 0)
        {
            $group = (isset($groups[$row['attr_group']])) ? $groups[$row['attr_group']] : $GLOBALS['_LANG']['goods_attr'];
  
            $arr['pro'][$group][$row['attr_id']]['name']  = $row['attr_name'];
            $arr['pro'][$group][$row['attr_id']]['value'] = $row['attr_value'];
        }
        else
        {
            $arr['spe'][$row['attr_id']]['attr_type'] = $row['attr_type'];
            $arr['spe'][$row['attr_id']]['name']     = $row['attr_name'];
            $arr['spe'][$row['attr_id']]['values'][] = array(
                                                        'label'     => $row['attr_value'],
                                                        'price'     => $row['attr_price'],
                                                        'format_price' => price_format(abs($row['attr_price']), false),
                                                        'id'            => $row['goods_attr_id'],
                                                        //获取颜色图片
                                                        'attr'          => "../".DATA_DIR.'/color/'.get_attachment_by_id($goods_id)."/0-0-".$row['goods_attr_id'].".jpg");
        }
  
        if ($row['is_linked'] == 1)
        {
            /* 如果该属性需要关联，先保存下来 */
            $arr['lnk'][$row['attr_id']]['name']  = $row['attr_name'];
            $arr['lnk'][$row['attr_id']]['value'] = $row['attr_value'];
        }
    }
  
    return $arr;
}

/**
 * 获得属性相同的商品
 *
 * @access  public
 * @param   array   $attr   // 包含了属性名称,ID的数组
 * @return  array
 */
function get_same_attribute_goods($attr)
{
    $lnk = array();

    if (!empty($attr))
    {
        foreach ($attr['lnk'] AS $key => $val)
        {
            $lnk[$key]['title'] = sprintf($GLOBALS['_LANG']['same_attrbiute_goods'], $val['name'], $val['value']);

            /* 查找符合条件的商品 */
            $sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                        "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                        'g.market_price, g.promote_price, g.promote_start_date, g.promote_end_date ' .
                    'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_attr') . ' as a ON g.goods_id = a.goods_id ' .
                    "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                        "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
                    "WHERE a.attr_id = '$key' AND g.is_on_sale=1 AND a.attr_value = '$val[value]' AND g.goods_id <> '$_REQUEST[id]' " .
                    'LIMIT ' . $GLOBALS['_CFG']['attr_related_number'];
            $res = $GLOBALS['db']->getAll($sql);

            foreach ($res AS $row)
            {
                $lnk[$key]['goods'][$row['goods_id']]['goods_id']      = $row['goods_id'];
                $lnk[$key]['goods'][$row['goods_id']]['goods_name']    = $row['goods_name'];
                $lnk[$key]['goods'][$row['goods_id']]['short_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
                $lnk[$key]['goods'][$row['goods_id']]['goods_thumb']     = (empty($row['goods_thumb'])) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
                $lnk[$key]['goods'][$row['goods_id']]['market_price']  = price_format($row['market_price']);
                $lnk[$key]['goods'][$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
                $lnk[$key]['goods'][$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'],
                    $row['promote_start_date'], $row['promote_end_date']);
                $lnk[$key]['goods'][$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
            }
        }
    }

    return $lnk;
}

/**
 * 获得指定商品的相册
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_goods_gallery($goods_id)
{
    $sql = 'SELECT img_id, img_url, thumb_url, img_desc' .
        ' FROM ' . $GLOBALS['ecs']->table('goods_gallery') .
        " WHERE goods_id = '$goods_id' LIMIT " . $GLOBALS['_CFG']['goods_gallery_number'];
    $row = $GLOBALS['db']->getAll($sql);
    /* 格式化相册图片路径 */
    foreach($row as $key => $gallery_img)
    {
        $row[$key]['img_url'] = get_image_path($goods_id, $gallery_img['img_url'], false, 'gallery');
        $row[$key]['thumb_url'] = get_image_path($goods_id, $gallery_img['thumb_url'], true, 'gallery');
    }    
    return $row;
}

/**
 * 获取指定产品的相册
 * @param unknown $product_id
 * @return Ambigous <string, mixed, string>
 */
function get_product_gallery($product_id)
{
	$sql = 'SELECT img_id, img_url, thumb_url,img_original, img_desc' .
			' FROM ' . $GLOBALS['ecs']->table('products_gallery') .
			" WHERE product_id = '$product_id' LIMIT " . $GLOBALS['_CFG']['goods_gallery_number'];
	$rows = $GLOBALS['db']->getAll($sql);
	/* 格式化相册图片路径 */
	foreach($rows as $key => $gallery_img)
	{
		$rows[$key]['img_url'] = get_image_path($product_id, $gallery_img['img_url'], false, 'gallery');
		$rows[$key]['thumb_url'] = get_image_path($product_id, $gallery_img['thumb_url'], true, 'gallery');
		$rows[$key]['original_img'] = get_image_path($product_id, $gallery_img['img_original'], true, 'gallery');
	}
	return $rows;
}

/**
 * 获得指定分类下的商品
 *
 * @access  public
 * @param   integer     $cat_id     分类ID
 * @param   integer     $num        数量
 * @param   string      $from       来自web/wap的调用
 * @param   string      $order_rule 指定商品排序规则
 * @return  array
 */
function assign_cat_goods($cat_id, $num = 0, $from = 'web', $order_rule = '')
{
    $children = get_children($cat_id);

    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
               'g.promote_price, promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .
            "FROM " . $GLOBALS['ecs']->table('goods') . ' AS g '.
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND '.
                'g.is_delete = 0 AND (' . $children . 'OR ' . get_extension_goods($children) . ') ';

    $order_rule = empty($order_rule) ? 'ORDER BY g.sort_order, g.goods_id DESC' : $order_rule;
    $sql .= $order_rule;
    if ($num > 0)
    {
        $sql .= ' LIMIT ' . $num;
    }
    $res = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($res AS $idx => $row)
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
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                        sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['shop_price']   = price_format($row['shop_price']);
        $goods[$idx]['thumb']        = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    }

    if ($from == 'web')
    {
        $GLOBALS['smarty']->assign('cat_goods_' . $cat_id, $goods);
    }
    elseif ($from == 'wap')
    {
        $cat['goods'] = $goods;
    }

    /* 分类信息 */
    $sql = 'SELECT cat_name FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";
    $cat['name'] = $GLOBALS['db']->getOne($sql);
    $cat['url']  = build_uri('category', array('cid' => $cat_id), $cat['name']);
    $cat['id']   = $cat_id;

    return $cat;
}

/**
 * 获得指定的品牌下的商品
 *
 * @access  public
 * @param   integer     $brand_id       品牌的ID
 * @param   integer     $num            数量
 * @param   integer     $cat_id         分类编号
 * @param   string      $order_rule     指定商品排序规则
 * @return  void
 */
function assign_brand_goods($brand_id, $num = 0, $cat_id = 0,$order_rule = '')
{
    $sql =  'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
                'g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand_id'";

    if ($cat_id > 0)
    {
        $sql .= get_children($cat_id);
    }

    $order_rule = empty($order_rule) ? ' ORDER BY g.sort_order, g.goods_id DESC' : $order_rule;
    $sql .= $order_rule;
    if ($num > 0)
    {
        $res = $GLOBALS['db']->selectLimit($sql, $num);
    }
    else
    {
        $res = $GLOBALS['db']->query($sql);
    }

    $idx = 0;
    $goods = array();
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

        $goods[$idx]['id']            = $row['goods_id'];
        $goods[$idx]['name']          = $row['goods_name'];
        $goods[$idx]['short_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['market_price']  = price_format($row['market_price']);
        $goods[$idx]['shop_price']    = price_format($row['shop_price']);
        $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        $goods[$idx]['brief']         = $row['goods_brief'];
        $goods[$idx]['thumb']         = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        $idx++;
    }

    /* 分类信息 */
    $sql = 'SELECT brand_name FROM ' . $GLOBALS['ecs']->table('brand') . " WHERE brand_id = '$brand_id'";

    $brand['id']   = $brand_id;
    $brand['name'] = $GLOBALS['db']->getOne($sql);
    $brand['url']  = build_uri('brand', array('bid' => $brand_id), $brand['name']);

    $brand_goods = array('brand' => $brand, 'goods' => $goods);

    return $brand_goods;
}

/**
 * 获得所有扩展分类属于指定分类的所有商品ID
 *
 * @access  public
 * @param   string $cat_id     分类查询字符串
 * @return  string
 */
function get_extension_goods($cats)
{
    $extension_goods_array = '';
    $sql = 'SELECT goods_id FROM ' . $GLOBALS['ecs']->table('goods_cat') . " AS g WHERE $cats";
    $extension_goods_array = $GLOBALS['db']->getCol($sql);
    return db_create_in($extension_goods_array, 'g.goods_id');
}

/**
 * 判断某个商品是否正在特价促销期
 *
 * @access  public
 * @param   float   $price      促销价格
 * @param   string  $start      促销开始日期
 * @param   string  $end        促销结束日期
 * @return  float   如果还在促销期则返回促销价，否则返回0
 */
function bargain_price($price, $start, $end, $time = null)
{
    if ($price == 0)
    {
        return 0;
    }
    else
    {
        $time = isset($time)? $time : gmtime();
        if ($time >= $start && $time <= $end)
        {
            return $price;
        }
        else
        {
            return 0;
        }
    }
}



/**
 * 获得指定的规格的价格
 *
 * @access  public
 * @param   mix     $spec   规格ID的数组或者逗号分隔的字符串
 * @return  void
 */
function spec_price($spec)
{
    if (!empty($spec))
    {
        $where = db_create_in($spec, 'goods_attr_id');

        $sql = 'SELECT SUM(attr_price) AS attr_price FROM ' . $GLOBALS['ecs']->table('goods_attr') . " WHERE $where";
        $price = floatval($GLOBALS['db']->getOne($sql));
    }
    else
    {
        $price = 0;
    }

    return $price;
}

/**
 * 取得团购活动信息
 * @param   int     $group_buy_id   团购活动id
 * @param   int     $current_num    本次购买数量（计算当前价时要加上的数量）
 * @return  array
 *                  status          状态：
 */
function group_buy_info($group_buy_id, $current_num = 0)
{
    /* 取得团购活动信息 */
    $group_buy_id = intval($group_buy_id);
    $sql = "SELECT *, act_id AS group_buy_id, act_desc AS group_buy_desc, start_time AS start_date, end_time AS end_date " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') .
            "WHERE act_id = '$group_buy_id' " .
            "AND act_type = '" . GAT_GROUP_BUY . "'";
    $group_buy = $GLOBALS['db']->getRow($sql);

    /* 如果为空，返回空数组 */
    if (empty($group_buy))
    {
        return array();
    }

    $ext_info = unserialize($group_buy['ext_info']);
    $group_buy = array_merge($group_buy, $ext_info);

    /* 格式化时间 */
    $group_buy['formated_start_date'] = local_date('Y-m-d H:i', $group_buy['start_time']);
    $group_buy['formated_end_date'] = local_date('Y-m-d H:i', $group_buy['end_time']);

    /* 格式化保证金 */
    $group_buy['formated_deposit'] = price_format($group_buy['deposit'], false);

    /* 处理价格阶梯 */
    $price_ladder = $group_buy['price_ladder'];
    if (!is_array($price_ladder) || empty($price_ladder))
    {
        $price_ladder = array(array('amount' => 0, 'price' => 0));
    }
    else
    {
        foreach ($price_ladder as $key => $amount_price)
        {
            $price_ladder[$key]['formated_price'] = price_format($amount_price['price'], false);
        }
    }
    $group_buy['price_ladder'] = $price_ladder;

    /* 统计信息 */
    $stat = group_buy_stat($group_buy_id, $group_buy['deposit']);
    $group_buy = array_merge($group_buy, $stat);

    /* 计算当前价 */
    $cur_price  = $price_ladder[0]['price']; // 初始化
    $cur_amount = $stat['valid_goods'] + $current_num; // 当前数量
    foreach ($price_ladder as $amount_price)
    {
        if ($cur_amount >= $amount_price['amount'])
        {
            $cur_price = $amount_price['price'];
        }
        else
        {
            break;
        }
    }
    $group_buy['cur_price'] = $cur_price;
    $group_buy['formated_cur_price'] = price_format($cur_price, false);

    /* 最终价 */
    $group_buy['trans_price'] = $group_buy['cur_price'];
    $group_buy['formated_trans_price'] = $group_buy['formated_cur_price'];
    $group_buy['trans_amount'] = $group_buy['valid_goods'];

    /* 状态 */
    $group_buy['status'] = group_buy_status($group_buy);
    if (isset($GLOBALS['_LANG']['gbs'][$group_buy['status']]))
    {
        $group_buy['status_desc'] = $GLOBALS['_LANG']['gbs'][$group_buy['status']];
    }

    $group_buy['start_time'] = $group_buy['formated_start_date'];
    $group_buy['end_time'] = $group_buy['formated_end_date'];

    return $group_buy;
}

/*
 * 取得某团购活动统计信息
 * @param   int     $group_buy_id   团购活动id
 * @param   float   $deposit        保证金
 * @return  array   统计信息
 *                  total_order     总订单数
 *                  total_goods     总商品数
 *                  valid_order     有效订单数
 *                  valid_goods     有效商品数
 */
function group_buy_stat($group_buy_id, $deposit)
{
    $group_buy_id = intval($group_buy_id);

    /* 取得团购活动商品ID */
    $sql = "SELECT goods_id " .
           "FROM " . $GLOBALS['ecs']->table('goods_activity') .
           "WHERE act_id = '$group_buy_id' " .
           "AND act_type = '" . GAT_GROUP_BUY . "'";
    $group_buy_goods_id = $GLOBALS['db']->getOne($sql);

    /* 取得总订单数和总商品数 */
    $sql = "SELECT COUNT(*) AS total_order, SUM(g.goods_number) AS total_goods " .
            "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
                $GLOBALS['ecs']->table('order_goods') . " AS g " .
            " WHERE o.order_id = g.order_id " .
            "AND o.extension_code = 'group_buy' " .
            "AND o.extension_id = '$group_buy_id' " .
            "AND g.goods_id = '$group_buy_goods_id' " .
            "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "')";
    $stat = $GLOBALS['db']->getRow($sql);
    if ($stat['total_order'] == 0)
    {
        $stat['total_goods'] = 0;
    }

    /* 取得有效订单数和有效商品数 */
    $deposit = floatval($deposit);
    if ($deposit > 0 && $stat['total_order'] > 0)
    {
        $sql .= " AND (o.money_paid + o.surplus) >= '$deposit'";
        $row = $GLOBALS['db']->getRow($sql);
        $stat['valid_order'] = $row['total_order'];
        if ($stat['valid_order'] == 0)
        {
            $stat['valid_goods'] = 0;
        }
        else
        {
            $stat['valid_goods'] = $row['total_goods'];
        }
    }
    else
    {
        $stat['valid_order'] = $stat['total_order'];
        $stat['valid_goods'] = $stat['total_goods'];
    }

    return $stat;
}

/**
 * 获得团购的状态
 *
 * @access  public
 * @param   array
 * @return  integer
 */
function group_buy_status($group_buy)
{
    $now = gmtime();
    if ($group_buy['is_finished'] == 0)
    {
        /* 未处理 */
        if ($now < $group_buy['start_time'])
        {
            $status = GBS_PRE_START;
        }
        elseif ($now > $group_buy['end_time'])
        {
            $status = GBS_FINISHED;
        }
        else
        {
            if ($group_buy['restrict_amount'] == 0 || $group_buy['valid_goods'] < $group_buy['restrict_amount'])
            {
                $status = GBS_UNDER_WAY;
            }
            else
            {
                $status = GBS_FINISHED;
            }
        }
    }
    elseif ($group_buy['is_finished'] == GBS_SUCCEED)
    {
        /* 已处理，团购成功 */
        $status = GBS_SUCCEED;
    }
    elseif ($group_buy['is_finished'] == GBS_FAIL)
    {
        /* 已处理，团购失败 */
        $status = GBS_FAIL;
    }

    return $status;
}

/**
 * 取得拍卖活动信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function auction_info($act_id, $config = false)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('goods_activity') . " WHERE act_id = '$act_id'";
    $auction = $GLOBALS['db']->getRow($sql);
    if ($auction['act_type'] != GAT_AUCTION)
    {
        return array();
    }
    $auction['status_no'] = auction_status($auction);
    if ($config == true)
    {

        $auction['start_time'] = local_date('Y-m-d H:i', $auction['start_time']);
        $auction['end_time'] = local_date('Y-m-d H:i', $auction['end_time']);
    }
    else
    {
        $auction['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['start_time']);
        $auction['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $auction['end_time']);
    }
    $ext_info = unserialize($auction['ext_info']);
    $auction = array_merge($auction, $ext_info);
    $auction['formated_start_price'] = price_format($auction['start_price']);
    $auction['formated_end_price'] = price_format($auction['end_price']);
    $auction['formated_amplitude'] = price_format($auction['amplitude']);
    $auction['formated_deposit'] = price_format($auction['deposit']);

    /* 查询出价用户数和最后出价 */
    $sql = "SELECT COUNT(DISTINCT bid_user) FROM " . $GLOBALS['ecs']->table('auction_log') .
            " WHERE act_id = '$act_id'";
    $auction['bid_user_count'] = $GLOBALS['db']->getOne($sql);
    if ($auction['bid_user_count'] > 0)
    {
        $sql = "SELECT a.*, u.user_name " .
                "FROM " . $GLOBALS['ecs']->table('auction_log') . " AS a, " .
                        $GLOBALS['ecs']->table('users') . " AS u " .
                "WHERE a.bid_user = u.user_id " .
                "AND act_id = '$act_id' " .
                "ORDER BY a.log_id DESC";
        $row = $GLOBALS['db']->getRow($sql);
        $row['formated_bid_price'] = price_format($row['bid_price'], false);
        $row['bid_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['bid_time']);
        $auction['last_bid'] = $row;
    }

    /* 查询已确认订单数 */
    if ($auction['status_no'] > 1)
    {
        $sql = "SELECT COUNT(*)" .
                " FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE extension_code = 'auction'" .
                " AND extension_id = '$act_id'" .
                " AND order_status " . db_create_in(array(OS_CONFIRMED, OS_UNCONFIRMED));
        $auction['order_count'] = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        $auction['order_count'] = 0;
    }

    /* 当前价 */
    $auction['current_price'] = isset($auction['last_bid']) ? $auction['last_bid']['bid_price'] : $auction['start_price'];
    $auction['formated_current_price'] = price_format($auction['current_price'], false);

    return $auction;
}

/**
 * 取得拍卖活动出价记录
 * @param   int     $act_id     活动id
 * @return  array
 */
function auction_log($act_id)
{
    $log = array();
    $sql = "SELECT a.*, u.user_name " .
            "FROM " . $GLOBALS['ecs']->table('auction_log') . " AS a," .
                      $GLOBALS['ecs']->table('users') . " AS u " .
            "WHERE a.bid_user = u.user_id " .
            "AND act_id = '$act_id' " .
            "ORDER BY a.log_id DESC";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['bid_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['bid_time']);
        $row['formated_bid_price'] = price_format($row['bid_price'], false);
        $log[] = $row;
    }

    return $log;
}

/**
 * 计算拍卖活动状态（注意参数一定是原始信息）
 * @param   array   $auction    拍卖活动原始信息
 * @return  int
 */
function auction_status($auction)
{
    $now = gmtime();
    if ($auction['is_finished'] == 0)
    {
        if ($now < $auction['start_time'])
        {
            return PRE_START; // 未开始
        }
        elseif ($now > $auction['end_time'])
        {
            return FINISHED; // 已结束，未处理
        }
        else
        {
            return UNDER_WAY; // 进行中
        }
    }
    elseif ($auction['is_finished'] == 1)
    {
        return FINISHED; // 已结束，未处理
    }
    else
    {
        return SETTLED; // 已结束，已处理
    }
}

/**
 * 取得商品信息
 * @param   int     $goods_id   商品id
 * @return  array
 */
function goods_info($goods_id)
{
    $sql = "SELECT g.*, b.brand_name " .
            "FROM " . $GLOBALS['ecs']->table('goods') . " AS g " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('brand') . " AS b ON g.brand_id = b.brand_id " .
            "WHERE g.goods_id = '$goods_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        /* 修正重量显示 */
        $row['goods_weight'] = (intval($row['goods_weight']) > 0) ?
            $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
            ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正图片 */
        $row['goods_img'] = get_image_path($goods_id, $row['goods_img']);
    }

    return $row;
}

/**
 * 取得优惠活动信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function favourable_info($act_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE act_id = '$act_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        $row['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['start_time']);
        $row['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['end_time']);
        $row['formated_min_amount'] = price_format($row['min_amount']);
        $row['formated_max_amount'] = price_format($row['max_amount']);
        $row['gift'] = unserialize($row['gift']);
        if ($row['act_type'] == FAT_GOODS)
        {
            $row['act_type_ext'] = round($row['act_type_ext']);
        }
    }

    return $row;
}

/**
 * 批发信息
 * @param   int     $act_id     活动id
 * @return  array
 */
function wholesale_info($act_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('wholesale') .
            " WHERE act_id = '$act_id'";
    $row = $GLOBALS['db']->getRow($sql);
    if (!empty($row))
    {
        $row['price_list'] = unserialize($row['prices']);
    }

    return $row;
}

/**
 * 添加商品名样式
 * @param   string     $goods_name     商品名称
 * @param   string     $style          样式参数
 * @return  string
 */
function add_style($goods_name, $style)
{
    $goods_style_name = $goods_name;

    $arr   = explode('+', $style);

    $font_color     = !empty($arr[0]) ? $arr[0] : '';
    $font_style = !empty($arr[1]) ? $arr[1] : '';

    if ($font_color!='')
    {
        $goods_style_name = '<font color=' . $font_color . '>' . $goods_style_name . '</font>';
    }
    if ($font_style != '')
    {
        $goods_style_name = '<' . $font_style .'>' . $goods_style_name . '</' . $font_style . '>';
    }
    return $goods_style_name;
}

/**
 * 取得商品属性
 * @param   int     $goods_id   商品id
 * @return  array
 */
function get_goods_attr($goods_id)
{
    $attr_list = array();
    $sql = "SELECT a.attr_id, a.attr_name " .
            "FROM " . $GLOBALS['ecs']->table('goods') . " AS g, " . $GLOBALS['ecs']->table('attribute') . " AS a " .
            "WHERE g.goods_id = '$goods_id' " .
            "AND g.goods_type = a.cat_id " .
            "AND a.attr_type = 1";
    $attr_id_list = $GLOBALS['db']->getCol($sql);
    $res = $GLOBALS['db']->query($sql);
    while ($attr = $GLOBALS['db']->fetchRow($res))
    {
        if (defined('ECS_ADMIN'))
        {
            $attr['goods_attr_list'] = array(0 => $GLOBALS['_LANG']['select_please']);
        }
        else
        {
            $attr['goods_attr_list'] = array();
        }
        $attr_list[$attr['attr_id']] = $attr;
    }

    $sql = "SELECT attr_id, goods_attr_id, attr_value " .
            "FROM " . $GLOBALS['ecs']->table('goods_attr') .
            " WHERE goods_id = '$goods_id' " .
            "AND attr_id " . db_create_in($attr_id_list);
    $res = $GLOBALS['db']->query($sql);
    while ($goods_attr = $GLOBALS['db']->fetchRow($res))
    {
        $attr_list[$goods_attr['attr_id']]['goods_attr_list'][$goods_attr['goods_attr_id']] = $goods_attr['attr_value'];
    }

    return $attr_list;
}

/**
 * 获得购物车中商品的配件
 *
 * @access  public
 * @param   array     $goods_list
 * @return  array
 */
function get_goods_fittings($goods_list = array())
{
    $temp_index = 0;
    $arr        = array();

    $sql = 'SELECT gg.parent_id, ggg.goods_name AS parent_name, gg.goods_id, gg.goods_price, g.goods_name, g.goods_thumb, g.goods_img, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price ".
            'FROM ' . $GLOBALS['ecs']->table('group_goods') . ' AS gg ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . 'AS g ON g.goods_id = gg.goods_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = gg.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS ggg ON ggg.goods_id = gg.parent_id ".
            "WHERE gg.parent_id " . db_create_in($goods_list) . " AND g.is_delete = 0 AND g.is_on_sale = 1 ".
            "ORDER BY gg.parent_id, gg.goods_id";

    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$temp_index]['parent_id']         = $row['parent_id'];//配件的基本件ID
        $arr[$temp_index]['parent_name']       = $row['parent_name'];//配件的基本件的名称
        $arr[$temp_index]['parent_short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['parent_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['parent_name'];//配件的基本件显示的名称
        $arr[$temp_index]['goods_id']          = $row['goods_id'];//配件的商品ID
        $arr[$temp_index]['goods_name']        = $row['goods_name'];//配件的名称
        $arr[$temp_index]['short_name']        = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];//配件显示的名称
        $arr[$temp_index]['fittings_price']    = price_format($row['goods_price']);//配件价格
        $arr[$temp_index]['shop_price']        = price_format($row['shop_price']);//配件原价格
        $arr[$temp_index]['goods_thumb']       = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$temp_index]['goods_img']         = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$temp_index]['url']               = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $temp_index ++;
    }

    return $arr;
}

/**
 * 取指定规格的货品信息
 *
 * @access      public
 * @param       string      $goods_id
 * @param       array       $spec_goods_attr_id
 * @return      array
 */
function get_products_info($goods_id, $spec_goods_attr_id)
{
    $return_array = array();

    if (empty($spec_goods_attr_id) || !is_array($spec_goods_attr_id) || empty($goods_id))
    {
        return $return_array;
    }

    $goods_attr_array = sort_goods_attr_id_array($spec_goods_attr_id);

    if(isset($goods_attr_array['sort']))
    {
        $goods_attr = implode('|', $goods_attr_array['sort']);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '$goods_id' AND goods_attr = '$goods_attr' LIMIT 0, 1";
        $return_array = $GLOBALS['db']->getRow($sql);
    }
    return $return_array;
}
function index_get_cat_id_goods_best_list($cat_id = '', $num = '')
{
$sql = 'Select g.goods_id, g.cat_id,c.parent_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".
"promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, " .
"g.is_best, g.is_new, g.is_hot, g.is_promote " .
'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS c ON c.cat_id = g.cat_id ' .
"LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
"Where g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_best = 1 AND g.is_delete = 0 ".
$sql .= " AND (c.parent_id =" . $cat_id. " OR g.cat_id = " . $cat_id ." OR g.cat_id ". db_create_in(array_unique(array_merge(array($cat_id),array_keys(cat_list($cat_id, 0, false))))) .")". "order by g.cat_id asc";
$sql .= " LIMIT $num";
$res = $GLOBALS['db']->getAll($sql);
$goods = array();
foreach ($res AS $idx => $row)
{
$goods[$idx]['id'] = $row['article_id'];
$goods[$idx]['id'] = $row['goods_id'];
$goods[$idx]['name'] = $row['goods_name'];
$goods[$idx]['brief'] = $row['goods_brief'];
$goods[$idx]['brand_name'] = $row['brand_name'];
$goods[$idx]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
$goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
$goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'],$row['goods_name_style']);
$goods[$idx]['market_price'] = price_format($row['market_price']);
$goods[$idx]['shop_price'] = price_format($row['shop_price']);
$goods[$idx]['thumb'] = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];
$goods[$idx]['goods_img'] = empty($row['goods_img']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];
$goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
}
return $goods;
}
/**

* 获得指定分类促销商品，带倒计时信息

*

* @access public

* @return array

*/

function myget_promote_goods($cats = '')

{

$time = gmtime();

$order_type = $GLOBALS['_CFG']['recommend_order'];

/* 取得促销lbi的数量限制 */

$num = get_library_number("recommend_promotion");

$sql = 'SELECT g.goods_id, g.goods_name, g.goods_number,g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .

"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".

"promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name, " .

"g.is_best, g.is_new, g.is_hot, g.is_promote, RAND() AS rnd " .

'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .

'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .

"LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".

"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".

'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' .

" AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' ";

$sql .= $order_type == 0 ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY rnd';

$sql .= " LIMIT $num ";

$result = $GLOBALS['db']->getAll($sql);

$goods = array();

foreach ($result AS $idx => $row)

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

$goods[$idx]['id'] = $row['goods_id'];

$goods[$idx]['name'] = $row['goods_name'];

$goods[$idx]['num'] = $row['goods_number'];

$goods[$idx]['brief'] = $row['goods_brief'];

$goods[$idx]['brand_name'] = $row['brand_name'];

$goods[$idx]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);

$goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];

$goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'],$row['goods_name_style']);

$goods[$idx]['market_price'] = price_format($row['market_price']);

$goods[$idx]['shop_price'] = price_format($row['shop_price']);

$goods[$idx]['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);

$goods[$idx]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);

$goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

//设置时间

if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date'])

{

$goods[$idx]['gmt_end_time'] = $row['promote_end_date'];

}

else

{

$goods[$idx]['gmt_end_time'] = 0;

}

}

return $goods;

}
function get_top_class_cat( $nid )
{
		$sql = "select parent_id from ".$GLOBALS['ecs']->table( "category" )." where cat_id = ".$nid."";
		$temp_id = 0;
		$pid = $GLOBALS['db']->getOne( $sql );
		if ( 0 < $pid )
		{
				$temp_id = get_top_class_cat( $pid );
				return $temp_id;
		}
		$temp_id = $nid;
		return $temp_id;
}

function get_parent_cat_id( $nid )
{
		$sql = "select parent_id from ".$GLOBALS['ecs']->table( "category" )." where cat_id = ".$nid."";
		return $GLOBALS['db']->getOne( $sql );
}

function get_hot_cat_tree( $pid = 0, $rec = 3 )
{
		$arr = array( );
		$sql = "select c.*, rc.recommend_type from ".$GLOBALS['ecs']->table( "category" )." as c left join ".$GLOBALS['ecs']->table( "cat_recommend" ).( " as rc on c.cat_id = rc.cat_id where c.parent_id=".$pid." order by c.sort_order asc, c.cat_id asc" );
		$res = $GLOBALS['db']->getAll( $sql );
		foreach ( $res as $row )
		{
				if ( $row['recommend_type'] == $rec )
				{
						$arr[$row['cat_id']]['id'] = $row['cat_id'];
						$arr[$row['cat_id']]['name'] = $row['cat_name'];
						$arr[$row['cat_id']]['url'] = build_uri( "category", array(
								"cid" => $row['cat_id']
						), $row['cat_name'] );
				}
		}
		foreach ( $res as $row )
		{
				$arr2 = get_hot_cat_tree2( $row['cat_id'], $rec );
				if ( $arr2 )
				{
						$arr[$row['cat_id']]['child'] = $arr2;
				}
		}
		return $arr;
}

function get_hot_cat_tree2( $pid = 0, $rec = 3 )
{
		$arr = array( );
		$sql = "select c.*, rc.recommend_type from ".$GLOBALS['ecs']->table( "category" )." as c left join ".$GLOBALS['ecs']->table( "cat_recommend" ).( " as rc on c.cat_id = rc.cat_id where rc.recommend_type=".$rec." and c.parent_id={$pid} order by c.sort_order asc, c.cat_id asc" );
		$res = $GLOBALS['db']->getAll( $sql );
		foreach ( $res as $row )
		{
				$arr[$row['cat_id']]['id'] = $row['cat_id'];
				$arr[$row['cat_id']]['name'] = $row['cat_name'];
				$arr[$row['cat_id']]['url'] = build_uri( "category", array(
						"cid" => $row['cat_id']
				), $row['cat_name'] );
		}
		return $arr;
}

function get_adv( $position )
{
		$sql = "select ap.ad_width,ap.ad_height,ad.ad_id,ad.ad_name,ad.ad_code,ad.ad_link from ".$GLOBALS['ecs']->table( "ad_position" )." as ap left join ".$GLOBALS['ecs']->table( "ad" )." as ad on ad.position_id = ap.position_id where ap.position_name='".$position."' and ad.media_type=0 and UNIX_TIMESTAMP()>ad.start_time and UNIX_TIMESTAMP()<ad.end_time and ad.enabled=1 order by ad.ad_id desc limit 1";
		$res = $GLOBALS['db']->getRow( $sql );
		if ( $res )
		{
				return "<a href='affiche.php?ad_id=".$res['ad_id']."&uri=".$res['ad_link']."' target='_blank'><img src='data/afficheimg/".$res['ad_code']."' width='".$res['ad_width']."' height='".$res['ad_height']."' /></a>";
		}
		return "";
}
function get_advlist( $position, $num )
{
		$arr = array( );
		$sql = "select ap.ad_width,ap.ad_height,ad.ad_id,ad.ad_name,ad.ad_code,ad.ad_link,ad.ad_id from ".$GLOBALS['ecs']->table( "ad_position" )." as ap left join ".$GLOBALS['ecs']->table( "ad" )." as ad on ad.position_id = ap.position_id where ap.position_name='".$position.( "'  and ad.enabled=1 order by ad.ad_id desc limit ".$num );
		$res = $GLOBALS['db']->getAll( $sql );
		foreach ( $res as $idx => $row )
		{
				$arr[$row['ad_id']]['name'] = $row['ad_name'];
				$arr[$row['ad_id']]['url'] = "affiche.php?ad_id=".$row['ad_id']."&uri=".$row['ad_link'];
				$arr[$row['ad_id']]['image'] = "data/afficheimg/".$row['ad_code'];
				$arr[$row['ad_id']]['content'] = "<a href='".$arr[$row['ad_id']]['url']."' target='_blank'><img src='data/afficheimg/".$row['ad_code']."' width='".$row['ad_width']."' height='".$row['ad_height']."' /></a>";
				$arr[$row['ad_id']]['ad_code'] = $row['ad_code'];
		}
		return $arr;
}
function getads($cat,$num)
{
$time = gmtime();
$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('ad') . " where position_id=".$cat." and start_time <= '" . $time . "' AND end_time >= '" . $time . "' ORDER BY ad_id desc limit ".$num;
$res = $GLOBALS['db']->getAll($sql);
$arr = array();
foreach ($res AS $idx => $row)
{
$arr[$idx]['id'] = $row['ad_id'];
$arr[$idx]['title'] = $row['ad_name'];
$arr[$idx]['ad_link'] = $row['ad_link'];
$arr[$idx]['ad_code'] = "data/afficheimg/".$row['ad_code'];
$arr[$idx]['description'] = $row['description'];
}
return $arr;
}
/** 
* 获得指定栏目最新的文章列表。 
* 
* @access private 
* @return array 
*/ 
function index_get_class_articles($cat_aid, $cat_num) 
{ 
$sql = "SELECT article_id, title,open_type,cat_id,file_url,add_time FROM " .$GLOBALS['ecs']->table('article'). " WHERE cat_id = ".$cat_aid." and is_open = 1 ORDER BY add_time DESC LIMIT " . $cat_num; 
$res = $GLOBALS['db']->getAll($sql); 
$arr = array(); 
foreach ($res AS $idx => $row) 
{ 
$arr[$idx]['id'] = $row['article_id']; 
$arr[$idx]['title'] = $row['title']; 
$arr[$idx]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ? 
sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title']; 
$arr[$idx]['cat_name'] = $row['cat_name']; 
$arr[$idx]['add_time'] = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']); 
$arr[$idx]['url'] = $row['open_type'] != 1 ? 
build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']); 
$arr[$idx]['cat_url'] = build_uri('article_cat', array('acid' => $row['cat_id'])); 
$arr[$idx]['file_url'] = $row['file_url'];
} 
return $arr; 
} 
/*读取下级栏目
$catid 为父栏目id  0 为一级栏目
*/
function get_childname($catid) {
	$a = get_categories_tree ( $catid );
	$i = 0;
	foreach ( $a as $value ) {
		$pname [$i] ['id'] = $value ['id'];
		$pname [$i] ['name'] = $value ['name'];
		$pname [$i] ['url'] = $value ['url'];
		
		$i ++;
	}
	return $pname;
}

/**
 * 根据价格获取价格区间
 * @param unknown $cat_id
 * @param unknown $price
 * @return unknown|NULL
 */
function get_price_grade_by_price($cat_id,$price){
	$price_grade = get_price_grade($cat_id);
	if(!empty($price_grade)){
		foreach ($price_grade as $key => $value) {
			if($value['start']<=$price && $value['end']>=$price){
				return $value;
			}
		}
	}
	return null;
}

/**
 * 获得分类的信息
 *
 * @param   integer $cat_id
 *
 * @return  void
 */
function com_sale_get_cat_info_by_cat_id($cat_id)
{
	return $GLOBALS['db']->getRow('SELECT cat_name, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
			" WHERE cat_id = '$cat_id'");
}

/**
 * 取得最近的上级分类的grade值
 *
 * @access  public
 * @param   int     $cat_id    //当前的cat_id
 *
 * @return int
 */
function com_sale_get_parent_grade_by_cat_id($cat_id)
{
	static $res = NULL;

	if ($res === NULL)
	{
		$data = read_static_cache('cat_parent_grade');
		if ($data === false)
		{
			$sql = "SELECT parent_id, cat_id, grade ".
					" FROM " . $GLOBALS['ecs']->table('category');
			$res = $GLOBALS['db']->getAll($sql);
			write_static_cache('cat_parent_grade', $res);
		}
		else
		{
			$res = $data;
		}
	}

	if (!$res)
	{
		return 0;
	}

	$parent_arr = array();
	$grade_arr = array();

	foreach ($res as $val)
	{
		$parent_arr[$val['cat_id']] = $val['parent_id'];
		$grade_arr[$val['cat_id']] = $val['grade'];
	}

	while ($parent_arr[$cat_id] >0 && $grade_arr[$cat_id] == 0)
	{
		$cat_id = $parent_arr[$cat_id];
	}

	return $grade_arr[$cat_id];

}
	
function get_price_grade($cat_id){
	$children = get_children($cat_id);

	$cat = com_sale_get_cat_info_by_cat_id($cat_id);   // 获得分类的相关信息

	/* 获取价格分级 */
	if ($cat['grade'] == 0  && $cat['parent_id'] != 0)
	{
		$cat['grade'] = com_sale_get_parent_grade_by_cat_id($cat_id); //如果当前分类级别为空，取最近的上级分类
	}

	if ($cat['grade'] > 1)
	{
		/* 需要价格分级 */

		/*
		 算法思路：
		 1、当分级大于1时，进行价格分级
		 2、取出该类下商品价格的最大值、最小值
		 3、根据商品价格的最大值来计算商品价格的分级数量级：
		 价格范围(不含最大值)    分级数量级
		 0-0.1                   0.001
		 0.1-1                   0.01
		 1-10                    0.1
		 10-100                  1
		 100-1000                10
		 1000-10000              100
		 4、计算价格跨度：
		 取整((最大值-最小值) / (价格分级数) / 数量级) * 数量级
		 5、根据价格跨度计算价格范围区间
		 6、查询数据库

		 可能存在问题：
		 1、
		 由于价格跨度是由最大值、最小值计算出来的
		 然后再通过价格跨度来确定显示时的价格范围区间
		 所以可能会存在价格分级数量不正确的问题
		 该问题没有证明
		 2、
		 当价格=最大值时，分级会多出来，已被证明存在
		 */

		$sql = "SELECT min(g.shop_price) AS min, max(g.shop_price) as max ".
				" FROM " . $GLOBALS['ecs']->table('goods'). " AS g ".
				" WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1  ';
		//获得当前分类下商品价格的最大值、最小值

		$row = $GLOBALS['db']->getRow($sql);

		// 取得价格分级最小单位级数，比如，千元商品最小以100为级数
		$price_grade = 0.0001;
		for($i=-2; $i<= log10($row['max']); $i++)
		{
			$price_grade *= 10;
		}

		//跨度
		$dx = ceil(($row['max'] - $row['min']) / ($cat['grade']) / $price_grade) * $price_grade;
		if($dx == 0)
		{
			$dx = $price_grade;
		}

		for($i = 1; $row['min'] > $dx * $i; $i ++);

		for($j = 1; $row['min'] > $dx * ($i-1) + $price_grade * $j; $j++);
		$row['min'] = $dx * ($i-1) + $price_grade * ($j - 1);

		for(; $row['max'] >= $dx * $i; $i ++);
		$row['max'] = $dx * ($i) + $price_grade * ($j - 1);

		$sql = "SELECT (FLOOR((g.shop_price - $row[min]) / $dx)) AS sn, COUNT(*) AS goods_num  ".
				" FROM " . $GLOBALS['ecs']->table('goods') . " AS g ".
				" WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
				" GROUP BY sn ";

		$price_grade = $GLOBALS['db']->getAll($sql);

		foreach ($price_grade as $key=>$val)
		{
			$temp_key = $key + 1;
			$price_grade[$temp_key]['goods_num'] = $val['goods_num'];
			$price_grade[$temp_key]['start'] = $row['min'] + round($dx * $val['sn']);
			$price_grade[$temp_key]['end'] = $row['min'] + round($dx * ($val['sn'] + 1));
			$price_grade[$temp_key]['price_range'] = $price_grade[$temp_key]['start'] . '&nbsp;-&nbsp;' . $price_grade[$temp_key]['end'];
			$price_grade[$temp_key]['formated_start'] = price_format($price_grade[$temp_key]['start']);
			$price_grade[$temp_key]['formated_end'] = price_format($price_grade[$temp_key]['end']);
			$price_grade[$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_grade[$temp_key]['start'], 'price_max'=> $price_grade[$temp_key]['end'], 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

			/* 判断价格区间是否被选中 */
			/* if (isset($_REQUEST['price_min']) && $price_grade[$temp_key]['start'] == $price_min && $price_grade[$temp_key]['end'] == $price_max)
			 {
				$price_grade[$temp_key]['selected'] = 1;
				}
				else
				{
				$price_grade[$temp_key]['selected'] = 0;
			} */
		}

		$price_grade[0]['start'] = 0;
		 $price_grade[0]['end'] = 0;
		 $price_grade[0]['price_range'] = $_LANG['all_attribute'];
		 $price_grade[0]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>0, 'price_max'=> 0, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
		 $price_grade[0]['selected'] = empty($price_max) ? 1 : 0;
		return $price_grade;
	}
}	

/**
 * 根据商品编号获取分类编号
 * @param unknown $goods_id
 */
function get_cat_id($goods_id){
	$sql = 'SELECT cat_id FROM ' .$GLOBALS['ecs']->table('goods')." WHERE goods_id = '$goods_id'";
	return $GLOBALS['db']->getOne($sql);
}


/**
 * 猜你喜欢 &　看了又看
 * @param unknown $goods_id
 * @param unknown $cat_id
 * @param unknown $brand_id
 * @param string $article_id
 * @return Ambigous <multitype:, number, void>
 */
function com_sale_get_may_like_goods($goods_id,$cat_id,$brand_id,$article_id=null)
{
	$where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
			"g.is_delete = 0 ";
	
	if($goods_id){
		$cat_id = get_cat_id($goods_id);
		$children = get_children($cat_id);
		$where = $where . " AND ($children) ";
	}
	
	if($cat_id){
		$children = get_children($cat_id);
		$where = $where . " AND ($children) ";
	}
	
	if($brand_id){
		$where = $where . " AND (g.brand_id = '$brand_id') ";
	}
	
	$sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
			"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
			'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
			"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
			"WHERE $where ORDER BY rand() limit 20";
	$res = $GLOBALS['db']->query($sql);
	$arr = array();//www.zuimoban.com
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

function com_sale_goods_get_related_brands_by_cat_id($cat_id){
	$children = get_children($cat_id);
	
	$sql = "SELECT b.brand_id, b.brand_name, COUNT(*) AS goods_num ".
			"FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ".
			$GLOBALS['ecs']->table('goods') . " AS g LEFT JOIN ". $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
			"WHERE g.brand_id = b.brand_id AND ($children) AND b.is_show = 1 " .
			" AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
			"GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.sort_order, b.brand_id ASC";
	
	$brands = $GLOBALS['db']->getAll($sql);
	
	foreach ($brands AS $key => $val)
	{
		$brands[$key]['brand_url'] = build_uri('brand', array('cid' => $cat_id, 'bid' => $val['brand_id']), $val['brand_name']);
		$brands[$key]['brand_name'] = $val['brand_name'];
	}
	return $brands;
}

function com_sale_goods_get_related_cats_by_cat_id($cat_id){
	$children = get_children($cat_id);

	$sql = "SELECT b.brand_id, b.brand_name, COUNT(*) AS goods_num ".
			"FROM " . $GLOBALS['ecs']->table('brand') . "AS b, ".
			$GLOBALS['ecs']->table('goods') . " AS g LEFT JOIN ". $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
			"WHERE g.brand_id = b.brand_id AND ($children) AND b.is_show = 1 " .
			" AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ".
			"GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.sort_order, b.brand_id ASC";

	$brands = $GLOBALS['db']->getAll($sql);

	foreach ($brands AS $key => $val)
	{
		$brands[$key]['url'] = build_uri('category', array('cid' => $cat, 'bid' => $val['brand_id']), $val['brand_name']);
		$brands[$key]['brand_name'] = $val['brand_name'];
	}
	return $brands;
}


/**
 * 获得商品已添加的规格列表
 * modified by tiger.guo 20160503 产品表products不再使用
 *
 * @access      public
 * @params      integer         $goods_id
 * @return      array
 */
function get_products_specifications_list($goods_id)
{
	if (empty($goods_id))
	{
		return array();  //$goods_id不能为空
	}

	/* $sql = "SELECT DISTINCT g.goods_attr_id, g.attr_value, g.attr_id, a.attr_name
            FROM " . $GLOBALS['ecs']->table('goods_attr') . " AS g
                INNER JOIN " . $GLOBALS['ecs']->table('attribute') . " AS a ON a.attr_id = g.attr_id
                INNER JOIN " . $GLOBALS['ecs']->table('products') . " AS p ON p.goods_id = g.goods_id
                INNER JOIN " . $GLOBALS['ecs']->table('products_attr') . " AS pattr ON p.product_id = pattr.product_id
                WHERE pattr.goods_attr_id = g.goods_attr_id AND p.goods_id = '$goods_id'
                AND a.attr_type = 1
                ORDER BY g.attr_id ASC"; */
	
	$virtual_goods_id = $GLOBALS['db']->getOne("SELECT virtual_goods_id FROM ". $GLOBALS['ecs']->table('goods') ." WHERE goods_id = '$goods_id'");
	if(empty($virtual_goods_id)){
		return null;
	}
	
	$goods_ids = $GLOBALS['db']->getCol("SELECT goods_id FROM ". $GLOBALS['ecs']->table('goods') ." WHERE virtual_goods_id = '$virtual_goods_id'");
	if(empty($goods_ids)){
		return null;
	}	
	
	$sql = "SELECT DISTINCT g.attr_id, g.attr_value, g.attr_id, a.attr_name
            FROM " . $GLOBALS['ecs']->table('goods_attr') . " AS g ".
            " INNER JOIN " . $GLOBALS['ecs']->table('attribute') . " AS a ON a.attr_id = g.attr_id ".
	        " WHERE g.goods_id ".db_create_in($goods_ids) .
	        " AND a.attr_type = 1 ORDER BY g.attr_id ASC";
	$results = $GLOBALS['db']->getAll($sql);

	return $results;
}

/**
 * 同一虚拟商品下的所有商品编号
 * @param unknown $goods_id
 * @return NULL|unknown
 */
function get_goods_list_same_virtual($goods_id){
	$virtual_goods_id = $GLOBALS['db']->getOne("SELECT virtual_goods_id FROM ". $GLOBALS['ecs']->table('goods') ." WHERE goods_id = '$goods_id'");
	if(empty($virtual_goods_id)){
		return null;
	}
	
	$goods_ids = $GLOBALS['db']->getCol("SELECT goods_id FROM ". $GLOBALS['ecs']->table('goods') ." WHERE virtual_goods_id = '$virtual_goods_id'");
	
	return $goods_ids;
}

/**
 * 获取商品对应的产品列表
 * @param unknown $goods_id
 * @return unknown
 */
function get_products_list($goods_id)
{
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('products') . " WHERE goods_id = '$goods_id' ORDER BY is_default DESC";
	$rows = $GLOBALS['db']->getAll($sql);
	
	
	return $rows;
}


/**
 * 获取商品默认的产品（主要用来处理商品没有选择产品的情况）
 * @param unknown $goods_id
 * @return unknown
 */
function get_default_product($goods_id)
{
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('products') . " WHERE goods_id = '$goods_id' ORDER BY seq_index ASC LIMIT 1";
	$rows = $GLOBALS['db']->getRow($sql);
	return $rows;
}



/**
 * 获取产品对应的属性列表
 * @param unknown $product_id
 * @return unknown
 */
function get_products_attr_list($product_id)
{
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('products_attr') . " WHERE product_id = '$product_id'";
	$rows = $GLOBALS['db']->getAll($sql);
	return $rows;
}


/**
 * 获取产品信息
 * @param unknown $product_id
 * @return unknown
 */
function get_product($product_id,$goods_id=null)
{
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('products') . " WHERE product_id = '$product_id'";
	if($goods_id){
		$sql = $sql." AND goods_id='$goods_id'";
	}
	
	$row = $GLOBALS['db']->getRow($sql);
	return $row;
}


/**
 * 获取商品的库存情况
 * @param unknown $goods_id
 * @param unknown $product_id
 * @param unknown $address
 */
function get_goods_store($goods_id,$product_id = null,$address=null){
	$sql = "SELECT IFNULL(goods_number,0) AS goods_number FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id = '$goods_id' LIMIT 1";
	$goods_number = $GLOBALS['db']->getOne($sql);
	
	
	if($product_id){
		$sql = "SELECT IFNULL(product_number,0) AS product_number FROM " . $GLOBALS['ecs']->table('products') . " WHERE product_id = '$product_id' LIMIT 1";
		$goods_number = $GLOBALS['db']->getOne($sql);
		
		if($address){
			$count = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM ".$GLOBALS['ecs']->table('products_store'). " WHERE product_id = '$product_id'");
			if($count>0){
				$storeroom = get_storeroom($address);
				if($storeroom){//store_id
					$sql = "SELECT IFNULL(ps.product_number,0) AS product_number FROM " . $GLOBALS['ecs']->table('products') . " AS p
				INNER JOIN ". $GLOBALS['ecs']->table('products_store') . " AS ps ON (p.product_id = ps.product_id)
								WHERE p.product_id = '$product_id' AND ps.store_id = '".$storeroom['store_id']."' LIMIT 1";
					$goods_number = $GLOBALS['db']->getOne($sql);
				}else{
					$goods_number = 0;
				}
			}
		}
	}else{
		if($address){
			$count = $GLOBALS['db']->getOne("SELECT COUNT(1) FROM ".$GLOBALS['ecs']->table('goods_store'). " WHERE goods_id = '$goods_id'");
			if($count>0){
				$storeroom = get_storeroom($address);
				if($storeroom){//store_id
					$sql = "SELECT IFNULL(gs.goods_number,0) AS goods_number FROM " . $GLOBALS['ecs']->table('goods_store') . " AS gs
					WHERE gs.goods_id = '$goods_id' AND gs.store_id = '".$storeroom['store_id']."' LIMIT 1";
					$goods_number = $GLOBALS['db']->getOne($sql);
				}else{
					$goods_number = 0;
				}
			}
		}		
	}
	
	//TODO:应该扣减掉当前session中加入购物车的数量	
	
	return isset($goods_number)?intval($goods_number):0;
}

/*同分类下随机推荐商品*/
function category_related_random_goods($category_id)
{
	$where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
			"g.is_delete = 0 AND g.cat_id=$category_id ";
	$sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
			"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
			'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
			"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
			"WHERE $where ORDER BY rand() limit 6";
	$res = $GLOBALS['db']->query($sql);
	$arr = array();//www.zuimoban.com
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

/*同品牌下随机推荐商品*/
function brand_related_random_goods($brand_id)
{
	$where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
			"g.is_delete = 0";
	
	if($brand_id){
		$where .= " AND g.brand_id=$brand_id";
	}
	
	$sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
			"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
			'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
			"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
			"WHERE $where ORDER BY rand() limit 6";
	$res = $GLOBALS['db']->query($sql);
	$arr = array();//www.zuimoban.com
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

/*同价位随机推荐商品*/
function price_grade_related_random_goods($category_id,$min_price,$max_price)
{
	$where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
			"g.is_delete = 0 AND g.cat_id=$category_id and (g.shop_price>=$min_price and g.shop_price<=$max_price) ";
	$sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
			"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
			'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
			"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
			"WHERE $where ORDER BY rand() limit 6";
	$res = $GLOBALS['db']->query($sql);
	$arr = array();//www.zuimoban.com
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
 * 查找商品的买赠规则
 * @param unknown $goods_id
 */
function get_buy_give_activity_list($goods_id){
	//计算可以赠送的商品
	$sql = "SELECT bga.buy_give_id, bga.goods_id, bga.buy_number_activity, bga.give_number_activity, bga.max_give_number, ".
			" bga.is_double_give, bga.other_goods_id, gift.goods_name AS other_goods_name, " .
			" gift.goods_thumb AS gift_goods_thumb, gift.goods_img AS gift_goods_img  FROM "
			. $GLOBALS['ecs']->table('buy_give_activity') . " AS bga " .
					" INNER JOIN  " . $GLOBALS['ecs']->table('goods') . " AS g ON (bga.goods_id = g.goods_id)" .
					" LEFT JOIN  " . $GLOBALS['ecs']->table('goods') . " AS gift ON (bga.other_goods_id = gift.goods_id)" . 
					" WHERE bga.goods_id = '$goods_id'" .
					" AND (g.is_buy_gift = 1 and g.gift_start_date <=".gmtime().' AND g.gift_end_date >='.gmtime().') '.
					" ORDER BY bga.is_double_give DESC,bga.buy_number_activity";
	$buy_give_activity_list = $GLOBALS['db']->getAll($sql);
	foreach ($buy_give_activity_list as $key => $row) {
		$buy_give_activity_list[$key]['gift_goods_url']          = build_uri('goods', array('gid'=>$row['other_goods_id']), $row['other_goods_name']);
		
		
		//赠送组合商品
		$sql = "SELECT pkg.package_id, pkg.buy_give_id, pkg.give_number_activity, pkg.other_goods_id ,g.goods_name AS other_goods_name, " .
			" g.goods_thumb AS gift_goods_thumb, g.goods_img AS gift_goods_img  FROM "
				. $GLOBALS['ecs']->table('buy_give_package') . " AS pkg " .
				" LEFT JOIN  " . $GLOBALS['ecs']->table('goods') . " AS g  ON (pkg.other_goods_id = g.goods_id)" .
				" WHERE pkg.buy_give_id = '".$row['buy_give_id']."'" .
				" ORDER BY pkg.other_goods_id";
		$buy_give_package_list = $GLOBALS['db']->getAll($sql);
		foreach ($buy_give_package_list as $i => $v) {
			$buy_give_package_list[$i]['gift_goods_url']          = build_uri('goods', array('gid'=>$v['other_goods_id']), $v['other_goods_name']);
		}
		
		$buy_give_activity_list[$key]['package'] = $buy_give_package_list;
		
	}
	
	
	
	return $buy_give_activity_list;
}

/* 分类广告图 */
function get_cat_ads($cat_id){
	$sql = "SELECT ad_img_url, ad_thumb_url, ad_img_original, ad_link " .
			" FROM " . $GLOBALS['ecs']->table('category') .
			" WHERE cat_id = '$cat_id'" ;
	$cat_ads = $GLOBALS['db']->getRow($sql);
	return $cat_ads;
}


?>