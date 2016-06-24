<?php

/**
 * ECSHOP 管理中心优惠活动管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: favourable.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_goods.php');

$exc = new exchange($ecs->table('favourable_activity'), $db, 'act_id', 'act_name');

/*------------------------------------------------------ */
//-- 活动列表页
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    admin_priv('favourable');

    /* 模板赋值 */
    $smarty->assign('full_page',   1);
    $smarty->assign('ur_here',     $_LANG['favourable_list']);
    $smarty->assign('action_link', array('href' => 'favourable.php?act=add', 'text' => $_LANG['add_favourable']));

    $list = favourable_list();

    $smarty->assign('favourable_list', $list['item']);
    $smarty->assign('filter',          $list['filter']);
    $smarty->assign('record_count',    $list['record_count']);
    $smarty->assign('page_count',      $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('favourable_list.htm');
}

/*------------------------------------------------------ */
//-- 分页、排序、查询
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $list = favourable_list();

    $smarty->assign('favourable_list', $list['item']);
    $smarty->assign('filter',          $list['filter']);
    $smarty->assign('record_count',    $list['record_count']);
    $smarty->assign('page_count',      $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('favourable_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 删除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('favourable');

    $id = intval($_GET['id']);
    
    //删除赠送组合商品
    $sql = "DELETE FROM ". $GLOBALS['ecs']->table('fav_act_detail_package') ." WHERE act_detail_id IN ( SELECT act_detail_id FROM ". $GLOBALS['ecs']->table('fav_act_detail')." WHERE act_id = '".$id."')";
    $GLOBALS['db']->query($sql);
    
    //删除赠送，满减，折扣详情
    $sql = "DELETE FROM ". $GLOBALS['ecs']->table('fav_act_detail') ." WHERE act_id = '".$id."'";
    $GLOBALS['db']->query($sql);
    
    $favourable = favourable_info($id);
    if (empty($favourable))
    {
        make_json_error($_LANG['favourable_not_exist']);
    }
    $name = $favourable['act_name'];
    $exc->drop($id);

    /* 记日志 */
    admin_log($name, 'remove', 'favourable');

    /* 清除缓存 */
    clear_cache_files();

    $url = 'favourable.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch')
{
    /* 取得要操作的记录编号 */
    if (empty($_POST['checkboxes']))
    {
        sys_msg($_LANG['no_record_selected']);
    }
    else
    {
        /* 检查权限 */
        admin_priv('favourable');

        $ids = $_POST['checkboxes'];

        if (isset($_POST['drop']))
        {
            /* 删除记录 */
            $sql = "DELETE FROM " . $ecs->table('favourable_activity') .
                    " WHERE act_id " . db_create_in($ids);
            $db->query($sql);

            /* 记日志 */
            admin_log('', 'batch_remove', 'favourable');

            /* 清除缓存 */
            clear_cache_files();

            $links[] = array('text' => $_LANG['back_favourable_list'], 'href' => 'favourable.php?act=list&' . list_link_postfix());
            sys_msg($_LANG['batch_drop_ok']);
        }
    }
}

/*------------------------------------------------------ */
//-- 修改排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('favourable');

    $id  = intval($_POST['id']);
    $val = intval($_POST['val']);

    $sql = "UPDATE " . $ecs->table('favourable_activity') .
            " SET sort_order = '$val'" .
            " WHERE act_id = '$id' LIMIT 1";
    $db->query($sql);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 添加、编辑
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('favourable');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'add';
    $smarty->assign('form_action', $is_add ? 'insert' : 'update');

    /* 初始化、取得优惠活动信息 */
    if ($is_add)
    {
        $favourable = array(
            'act_id'        => 0,
            'act_name'      => '',
            'start_time'    => date('Y-m-d', time() + 86400),
            'end_time'      => date('Y-m-d', time() + 4 * 86400),
            'user_rank'     => '',
            'act_range'     => FAR_ALL,
            'act_range_ext' => '',
            'min_amount'    => 0,
            'max_amount'    => 0,
            'act_type'      => FAT_GOODS,
            'act_type_ext'  => 0,
            'gift'          => array()
        );
    }
    else
    {
        if (empty($_GET['id']))
        {
            sys_msg('invalid param');
        }
        $id = intval($_GET['id']);
        $favourable = favourable_info($id);
        if (empty($favourable))
        {
            sys_msg($_LANG['favourable_not_exist']);
        }
    }
    $smarty->assign('favourable', $favourable);

    /* 取得用户等级 */
    $user_rank_list = array();
    $user_rank_list[] = array(
        'rank_id'   => 0,
        'rank_name' => $_LANG['not_user'],
        'checked'   => strpos(',' . $favourable['user_rank'] . ',', ',0,') !== false
    );
    $sql = "SELECT rank_id, rank_name FROM " . $ecs->table('user_rank');
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $row['checked'] = strpos(',' . $favourable['user_rank'] . ',', ',' . $row['rank_id']. ',') !== false;
        $user_rank_list[] = $row;
    }
    $smarty->assign('user_rank_list', $user_rank_list);

    /* 取得优惠范围 */
    $act_range_ext = array();
    if ($favourable['act_range'] != FAR_ALL && !empty($favourable['act_range_ext']))
    {
        if ($favourable['act_range'] == FAR_CATEGORY)
        {
            $sql = "SELECT cat_id AS id, cat_name AS name FROM " . $ecs->table('category') .
                " WHERE cat_id " . db_create_in($favourable['act_range_ext']);
        }
        elseif ($favourable['act_range'] == FAR_BRAND)
        {
            $sql = "SELECT brand_id AS id, brand_name AS name FROM " . $ecs->table('brand') .
                " WHERE brand_id " . db_create_in($favourable['act_range_ext']);
        }
        else
        {
            $sql = "SELECT goods_id AS id, goods_name AS name FROM " . $ecs->table('goods') .
                " WHERE goods_id " . db_create_in($favourable['act_range_ext']);
        }
        $act_range_ext = $db->getAll($sql);
    }
    $smarty->assign('act_range_ext', $act_range_ext);
    
    
    //买几送几促销，满减促销，打折促销
    $sql = "SELECT bga.act_detail_id, bga.act_id, bga.buy_amt_number, bga.act_amt_number, bga.max_act_number, bga.is_double_give, bga.other_goods_id ,g.goods_name AS other_goods_name FROM "
    		. $ecs->table('fav_act_detail') . " AS bga " .
    		" LEFT JOIN  " . $ecs->table('goods') . " AS g  ON (bga.other_goods_id = g.goods_id)" .
    		" WHERE bga.act_id = '".$_REQUEST[id]."' ORDER BY bga.is_double_give,bga.buy_amt_number ";
    $buy_give_activity_list = $db->getAll($sql);
    if(empty($buy_give_activity_list)){
    	$buy_give_activity_list = array('0'=>array('buy_amt_number'=>0,'act_amt_number'=>0,'max_act_number'=>-1,'is_double_give'=>0)); //为空时插入一条初始记录
    }else{
    	foreach ($buy_give_activity_list as $key => $value) {
    		$sql = "SELECT pkg.package_id, pkg.act_detail_id, pkg.act_amt_number, pkg.other_goods_id ,g.goods_name AS other_goods_name FROM "
    				. $ecs->table('fav_act_detail_package') . " AS pkg " .
    				" LEFT JOIN  " . $ecs->table('goods') . " AS g  ON (pkg.other_goods_id = g.goods_id)" .
    				" WHERE pkg.act_detail_id = '".$value['act_detail_id']."'";
    		$buy_give_package_list = $db->getAll($sql);
    		$buy_give_activity_list[$key]['package'] = $buy_give_package_list;
    	}
    }
    
    //买几送几活动
    $smarty->assign('buy_give_activity_list', $buy_give_activity_list);
    $smarty->assign('buy_give_activity_list_count', empty($buy_give_activity_list)?0:count($buy_give_activity_list));
    

    /* 赋值时间控件的语言 */
    $smarty->assign('cfg_lang', $_CFG['lang']);

    /* 显示模板 */
    if ($is_add)
    {
        $smarty->assign('ur_here', $_LANG['add_favourable']);
    }
    else
    {
        $smarty->assign('ur_here', $_LANG['edit_favourable']);
    }
    $href = 'favourable.php?act=list';
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }
    $smarty->assign('action_link', array('href' => $href, 'text' => $_LANG['favourable_list']));
    assign_query_info();
    $smarty->display('favourable_info.htm');
}

/*------------------------------------------------------ */
//-- 添加、编辑后提交
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('favourable');

    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'insert';

    /* 检查名称是否重复 */
    $act_name = sub_str($_POST['act_name'], 255, false);
    if (!$exc->is_only('act_name', $act_name, intval($_POST['id'])))
    {
        sys_msg($_LANG['act_name_exists']);
    }

    /* 检查享受优惠的会员等级 */
    if (!isset($_POST['user_rank']))
    {
        sys_msg($_LANG['pls_set_user_rank']);
    }

    /* 检查优惠范围扩展信息 */
    if (intval($_POST['act_range']) > 0 && !isset($_POST['act_range_ext']))
    {
        sys_msg($_LANG['pls_set_act_range']);
    }

    /* 检查金额上下限 */
    $min_amount = floatval($_POST['min_amount']) >= 0 ? floatval($_POST['min_amount']) : 0;
    $max_amount = floatval($_POST['max_amount']) >= 0 ? floatval($_POST['max_amount']) : 0;
    if ($max_amount > 0 && $min_amount > $max_amount)
    {
        sys_msg($_LANG['amount_error']);
    }

    /* 取得赠品 */
    $gift = array();
    if (intval($_POST['act_type']) == FAT_GOODS && isset($_POST['gift_id']))
    {
        foreach ($_POST['gift_id'] as $key => $id)
        {
            $gift[] = array('id' => $id, 'name' => $_POST['gift_name'][$key], 'price' => $_POST['gift_price'][$key]);
        }
    }

    /* 提交值 */
    $favourable = array(
        'act_id'        => intval($_POST['id']),
        'act_name'      => $act_name,
        'start_time'    => local_strtotime($_POST['start_time']),
        'end_time'      => local_strtotime($_POST['end_time']),
        'user_rank'     => isset($_POST['user_rank']) ? join(',', $_POST['user_rank']) : '0',
        'act_range'     => intval($_POST['act_range']),
        'act_range_ext' => intval($_POST['act_range']) == 0 ? '' : join(',', $_POST['act_range_ext']),
        'min_amount'    => floatval($_POST['min_amount']),
        'max_amount'    => floatval($_POST['max_amount']),
        'act_type'      => intval($_POST['act_type']),
        'act_type_ext'  => floatval($_POST['act_type_ext']),
        'gift'          => serialize($gift)
    );
    if ($favourable['act_type'] == FAT_GOODS)
    {
        $favourable['act_type_ext'] = round($favourable['act_type_ext']);
    }

    /* 保存数据 */
    if ($is_add)
    {
        $db->autoExecute($ecs->table('favourable_activity'), $favourable, 'INSERT');
        $favourable['act_id'] = $db->insert_id();
    }
    else
    {
        $db->autoExecute($ecs->table('favourable_activity'), $favourable, 'UPDATE', "act_id = '$favourable[act_id]'");
    }
    
    
    /*买几送几，满减，折扣 start*/
    /* $buy_number_activitys = isset($_POST['buy_number_activity'])?$_POST['buy_number_activity']:null;
     $give_number_activitys = isset($_POST['give_number_activity'])?$_POST['give_number_activity']:null;
     $max_give_numbers = isset($_POST['max_give_number'])?$_POST['max_give_number']:null;
     $is_double_gives = isset($_POST['is_double_give'])?$_POST['is_double_give']:null;
     $other_goods_ids = isset($_POST['other_goods_id'])?$_POST['other_goods_id']:null; */
    $buy_give_activity_list = isset($_POST['buy_give_activity_list'])?$_POST['buy_give_activity_list']:null;
    
    //     var_dump($buy_give_activity_list);
    //     exit;
    $sql = "DELETE FROM ". $GLOBALS['ecs']->table('fav_act_detail_package') ." WHERE act_detail_id IN ( SELECT act_detail_id FROM ". $GLOBALS['ecs']->table('fav_act_detail')." WHERE act_id = '".$favourable['act_id']."')";
    $GLOBALS['db']->query($sql);
    
    $sql = "DELETE FROM ". $GLOBALS['ecs']->table('fav_act_detail') ." WHERE act_id = '".$favourable['act_id']."'";
    $GLOBALS['db']->query($sql);
    
    if($buy_give_activity_list){
    	foreach ($buy_give_activity_list as $key => $value) {
    		$buy_number_activity  = $value['buy_amt_number'];
    		if(empty($buy_number_activity)){
    			continue;
    		}
    		$give_number_activity  = empty($value['act_amt_number'])?0:intval($value['act_amt_number']);
    		$is_double_give  = empty($value['is_double_give'])?0:intval($value['is_double_give']);
    		$other_goods_id  = empty($value['other_goods_id'])?null:intval($value['other_goods_id']);
    		$max_give_number  = empty($value['max_act_number'])?-1:intval($value['max_act_number']);
    
    
    		$sql = "INSERT INTO " . $GLOBALS['ecs']->table('fav_act_detail') . " (act_id, buy_amt_number, act_amt_number, max_act_number,is_double_give,other_goods_id) " .
    				"VALUES ('$favourable[act_id]', '$buy_number_activity', '$give_number_activity', '$max_give_number', '$is_double_give', '$other_goods_id')";
    		$db->query($sql);
    
    		//写入组合赠送
    		if(isset($buy_give_activity_list[$key]['package_gift'])){
    			$give_number_activitys = $buy_give_activity_list[$key]['package_gift']['act_amt_number'];
    	   
    			$other_goods_ids = $buy_give_activity_list[$key]['package_gift']['other_goods_id'];
    			$inserted_buy_give_id = $GLOBALS['db']->insert_id();
    			foreach ($give_number_activitys as $i => $v) {
    				$give_number_activity_pkg  = empty($v)?0:intval($v);
    				if(empty($give_number_activity_pkg)){
    					continue;
    				}
    				$other_goods_id_pkg  = empty($other_goods_ids[$i])?null:intval($other_goods_ids[$i]);
    
    				$sql = "INSERT INTO " . $GLOBALS['ecs']->table('fav_act_detail_package') . " (act_detail_id, act_amt_number, other_goods_id) " .
    						"VALUES ('$inserted_buy_give_id', '$give_number_activity_pkg', '$other_goods_id_pkg')";
    				$db->query($sql);
    			}
    		}
    	}
    }
    
    /* if($buy_number_activitys){
     foreach ($buy_number_activitys as $key => $value) {
     $buy_number_activity  = empty($value)?0:intval($value);
     if(empty($buy_number_activity)){
     continue;
     }
     $give_number_activity  = empty($give_number_activitys[$key])?0:intval($give_number_activitys[$key]);
     $is_double_give  = empty($is_double_gives[$key])?0:intval($is_double_gives[$key]);
     $other_goods_id  = empty($other_goods_ids[$key])?null:intval($other_goods_ids[$key]);
     $max_give_number  = empty($max_give_numbers[$key])?-1:intval($max_give_numbers[$key]);
    
    
     $sql = "INSERT INTO " . $GLOBALS['ecs']->table('buy_give_activity') . " (goods_id, buy_number_activity, give_number_activity, max_give_number,is_double_give,other_goods_id) " .
     "VALUES ('$goods_id', '$buy_number_activity', '$give_number_activity', '$max_give_number', '$is_double_give', '$other_goods_id')";
     $db->query($sql);
     }
     } */
    
    /*买几送几 end*/
    

    /* 记日志 */
    if ($is_add)
    {
        admin_log($favourable['act_name'], 'add', 'favourable');
    }
    else
    {
        admin_log($favourable['act_name'], 'edit', 'favourable');
    }

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    if ($is_add)
    {
        $links = array(
            array('href' => 'favourable.php?act=add', 'text' => $_LANG['continue_add_favourable']),
            array('href' => 'favourable.php?act=list', 'text' => $_LANG['back_favourable_list'])
        );
        sys_msg($_LANG['add_favourable_ok'], 0, $links);
    }
    else
    {
        $links = array(
            array('href' => 'favourable.php?act=list&' . list_link_postfix(), 'text' => $_LANG['back_favourable_list'])
        );
        sys_msg($_LANG['edit_favourable_ok'], 0, $links);
    }
}

/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search')
{
    /* 检查权限 */
    check_authz_json('favourable');

    include_once(ROOT_PATH . 'includes/cls_json.php');

    $json   = new JSON;
    $filter = $json->decode($_GET['JSON']);
    $filter->keyword = json_str_iconv($filter->keyword);
    if ($filter->act_range == FAR_ALL)
    {
        $arr[0] = array(
            'id'   => 0,
            'name' => $_LANG['js_languages']['all_need_not_search']
        );
    }
    elseif ($filter->act_range == FAR_CATEGORY)
    {
        $sql = "SELECT cat_id AS id, cat_name AS name FROM " . $ecs->table('category') .
            " WHERE cat_name LIKE '%" . mysql_like_quote($filter->keyword) . "%' LIMIT 50";
        $arr = $db->getAll($sql);
    }
    elseif ($filter->act_range == FAR_BRAND)
    {
        $sql = "SELECT brand_id AS id, brand_name AS name FROM " . $ecs->table('brand') .
            " WHERE brand_name LIKE '%" . mysql_like_quote($filter->keyword) . "%' LIMIT 50";
        $arr = $db->getAll($sql);
    }
    else
    {
        $sql = "SELECT goods_id AS id, goods_name AS name FROM " . $ecs->table('goods') .
            " WHERE goods_name LIKE '%" . mysql_like_quote($filter->keyword) . "%'" .
            " OR goods_sn LIKE '%" . mysql_like_quote($filter->keyword) . "%' LIMIT 50";
        $arr = $db->getAll($sql);
    }
    if (empty($arr))
    {
        $arr = array(0 => array(
            'id'   => 0,
            'name' => $_LANG['search_result_empty']
        ));
    }

    make_json_result($arr);
}

/*
 * 取得优惠活动列表
 * @return   array
 */
function favourable_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['is_going']   = empty($_REQUEST['is_going']) ? 0 : 1;
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'act_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = "";
        if (!empty($filter['keyword']))
        {
            $where .= " AND act_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        if ($filter['is_going'])
        {
            $now = gmtime();
            $where .= " AND start_time <= '$now' AND end_time >= '$now' ";
        }

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('favourable_activity') .
                " WHERE 1 $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 查询 */
        $sql = "SELECT * ".
                "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
                " WHERE 1 $where ".
                " ORDER BY $filter[sort_by] $filter[sort_order] ".
                " LIMIT ". $filter['start'] .", $filter[page_size]";

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['start_time']  = local_date('Y-m-d H:i', $row['start_time']);
        $row['end_time']    = local_date('Y-m-d H:i', $row['end_time']);

        $list[] = $row;
    }

    return array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>