<?php
/**
 * ECSHOP 寄存管理
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: order.php 17219 2011-01-27 10:49:19Z yehuaixiao $
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

/*------------------------------------------------------ */
//-- 寄存查询
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'order_query')
{
    /* 检查权限 */
    admin_priv('order_view');

    /* 载入配送方式 */
    $smarty->assign('shipping_list', shipping_list());

    /* 载入支付方式 */
    $smarty->assign('pay_list', payment_list());

    /* 载入国家 */
    $smarty->assign('country_list', get_regions());

    /* 载入订单状态、付款状态、发货状态 */
    $smarty->assign('os_list', get_status_list('order'));
    $smarty->assign('ps_list', get_status_list('payment'));
    $smarty->assign('ss_list', get_status_list('shipping'));

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['03_order_query']);
    $smarty->assign('action_link', array('href' => 'order.php?act=list', 'text' => $_LANG['02_order_list']));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('deposit_list.htm');
}

/*------------------------------------------------------ */
//-- 寄存列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('order_view');

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['11_deposit_list']);
    $smarty->assign('ordersn1', $_LANG['deposit_sn']);
    $smarty->assign('all_status2', $_LANG['oper_sta']);
    $smarty->assign('deposit_dq', $_LANG['deposit_dq']);
    //$smarty->assign('action_link', array('href' => 'order.php?act=order_query', 'text' => $_LANG['11_deposit_list']));

    //$smarty->assign('status_list', $_LANG['cs']);   // 订单状态

    //$smarty->assign('os_unconfirmed',   OS_UNCONFIRMED);
    //$smarty->assign('cs_await_pay',     CS_AWAIT_PAY);
    //$smarty->assign('cs_await_ship',    CS_AWAIT_SHIP);
    $smarty->assign('full_page',        1);

    $order_list = deposit_list();
    $smarty->assign('order_list',   $order_list['orders']);
    $smarty->assign('filter',       $order_list['filter']);
    $smarty->assign('record_count', $order_list['record_count']);
    $smarty->assign('page_count',   $order_list['page_count']);
    $smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('deposit_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 检查权限 */
    admin_priv('order_view');

    $order_list = deposit_list();
    $smarty->assign('order_list',   $order_list['orders']);
    $smarty->assign('filter',       $order_list['filter']);
    $smarty->assign('record_count', $order_list['record_count']);
    $smarty->assign('page_count',   $order_list['page_count']);
    $sort_flag  = sort_flag($order_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    make_json_result($smarty->fetch('deposit_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}


/*------------------------------------------------------ */
//-- 获取订单商品信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_goods_info')
{
    /* 取得订单商品 */
    $order_id = isset($_REQUEST['order_id'])?intval($_REQUEST['order_id']):0;
    if (empty($order_id))
    {
        make_json_response('', 1, $_LANG['error_get_goods_info']);
    }
    $goods_list = array();
    $goods_attr = array();
    $sql = "SELECT o.*, g.goods_thumb, g.goods_number AS storage, '' goods_attr, IFNULL(b.brand_name, '') AS brand_name " .
            "FROM " . $ecs->table('deposit_goods') . " AS o ".
            "LEFT JOIN " . $ecs->table('goods') . " AS g ON o.goods_id = g.goods_id " .
            "LEFT JOIN " . $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
            "WHERE o.dep_id = '{$order_id}' ";
	/*$sql = "SELECT o.*, g.goods_thumb, g.goods_number AS storage, o.goods_attr, IFNULL(b.brand_name, '') AS brand_name " .
            "FROM " . $ecs->table('order_goods') . " AS o ".
            "LEFT JOIN " . $ecs->table('goods') . " AS g ON o.goods_id = g.goods_id " .
            "LEFT JOIN " . $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
            "WHERE o.order_id = '{$order_id}' ";*/


    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        /* 虚拟商品支持 */
        if ($row['is_real'] == 0)
        {
            /* 取得语言项 */
            $filename = ROOT_PATH . 'plugins/' . $row['extension_code'] . '/languages/common_' . $_CFG['lang'] . '.php';
            if (file_exists($filename))
            {
                include_once($filename);
                if (!empty($_LANG[$row['extension_code'].'_link']))
                {
                    $row['goods_name'] = $row['goods_name'] . sprintf($_LANG[$row['extension_code'].'_link'], $row['goods_id'], $order['order_sn']);
                }
            }
        }

        $row['formated_subtotal']       = price_format($row['goods_price'] * $row['goods_number']);
        $row['formated_goods_price']    = price_format($row['goods_price']);
        $_goods_thumb = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $_goods_thumb = (strpos($_goods_thumb, 'http://') === 0) ? $_goods_thumb : $ecs->url() . $_goods_thumb;
        $row['goods_thumb'] = $_goods_thumb;
        $goods_attr[] = explode(' ', trim($row['goods_attr'])); //将商品属性拆分为一个数组
		
        $goods_list[] = $row;
    }
    $attr = array();
    $arr  = array();
    foreach ($goods_attr AS $index => $array_val)
    {
        foreach ($array_val AS $value)
        {
            $arr = explode(':', $value);//以 : 号将属性拆开
            $attr[$index][] =  @array('name' => $arr[0], 'value' => $arr[1]);
        }
    }

	$lang = array();
	$lang['goods_name_brand'] = "商品名称 [ 品牌 ]";
	$lang['goods_sn'] = "货号";
	$lang['goods_price'] = "价格";
	$lang['goods_number'] = "寄存数量";
	$lang['storage'] = "库存";


	$smarty->assign('lang', $lang);
	$smarty->assign('goods_attr', $attr);
    $smarty->assign('goods_list', $goods_list);
    $str = $smarty->fetch('deposit_goods_info.htm');
    $goods[] = array('order_id' => $order_id, 'str' => $str);
    make_json_result($goods);
}

/*------------------------------------------------------ */
//-- 操作订单状态（载入页面）
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'operate')
{
    $order_id = '';
    /* 检查权限 */
    admin_priv('order_os_edit');

    /* 取得订单id（可能是多个，多个sn）和操作备注（可能没有） */
    if(isset($_REQUEST['order_id']))
    {
        $order_id= $_REQUEST['order_id'];
    }
    $batch          = isset($_REQUEST['batch']); // 是否批处理

	if ($db->GetOne('SELECT COUNT(*) FROM ' .$ecs->table('deposit') .' WHERE dep_id in('.$order_id.') AND datediff(FROM_UNIXTIME(end_time),now())>=30')<=0)
	{
		$admin_id = $_SESSION['admin_id'];
		$sql = 'UPDATE ' .$ecs->table('deposit') .' SET oper_status=1,oper_user='.$admin_id.',oper_time='.gmtime().' WHERE dep_id in('.$order_id.') AND datediff(FROM_UNIXTIME(end_time),now())<30 AND oper_status=0';
		$db->query($sql);
		sys_msg('操作成功！', 0, array(), true, 5);
		/* 跳回订单商品 */
		//ecs_header("Location: order.php?act=" . $step_act . "&order_id=" . $order_id . "&step=goods\n");
		//exit;
	}
	else
	{
		sys_msg('选中的记录包含未到期的寄存，操作失败', 1, array(), false);
	}



	//$smarty->assign('dep_id', $_SESSION['admin_id']);

    /* 显示模板 */
    //assign_query_info();
    //$smarty->display('deposit_test.htm');

}








/**
 *  获取寄存列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function deposit_list()
{
    $result = get_filter();
    if ($result === false)
    {
		/* 过滤信息 */
		$filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
		$filter['operstatus'] = isset($_REQUEST['operstatus']) ? intval($_REQUEST['operstatus']) : -1;
		$filter['status'] = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : -1;
        
		$where = 'WHERE 1 ';
        if ($filter['order_sn'])
        {
            $where .= " AND o.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
        }
		if ($filter['operstatus'] != -1)
        {
            $where .= " AND e.oper_status = " . mysql_like_quote($filter['operstatus']);
        }
        if ($filter['status'] != -1)
        {
            if ($filter['status'] == 1)
              $where .= " AND datediff(FROM_UNIXTIME(e.end_time),now())>=30";
            elseif ($filter['status'] == 2)
              $where .= " AND datediff(FROM_UNIXTIME(e.end_time),now())<30 AND datediff(FROM_UNIXTIME(e.end_time),now())>=0";
            elseif ($filter['status'] == 3)
              $where .= " AND datediff(FROM_UNIXTIME(e.end_time),now())<0";
        }

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        if ($filter['user_name'])
        {
            $sql = "SELECT COUNT(e.*) FROM " . $GLOBALS['ecs']->table('deposit') . " AS e ,".
                   $GLOBALS['ecs']->table('users') . " AS u " .
                   " LEFT JOIN " .$GLOBALS['ecs']->table('order_info'). " AS o ON e.order_id=o.order_id "
                   . $where;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('deposit') . " AS e " .
            " LEFT JOIN " .$GLOBALS['ecs']->table('order_info'). " AS o ON e.order_id=o.order_id "
            . $where;
        }

        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;


        /* 查询 */
        $sql = "SELECT e.dep_id, e.order_id, o.order_sn, e.add_time, e.start_time, e.start_time, e.end_time,".
				        "us.mobile_phone,us.ID_cards,us.user_name,us.user_id,e.oper_status ".
                " FROM " . $GLOBALS['ecs']->table('deposit') . " AS e " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('order_info'). " AS o ON e.order_id=o.order_id ".
                " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS us ON us.user_id=e.user_id ".
				        $where .
                " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";

        /*foreach (array('order_sn', 'consignee', 'email', 'address', 'zipcode', 'tel', 'user_name') AS $val)
        {
            $filter[$val] = stripslashes($filter[$val]);
        }
        set_filter($filter, $sql);*/
    }
    /*else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }*/

    $row = $GLOBALS['db']->getAll($sql);

    /* 格式话数据 */
    foreach ($row AS $key => $value)
    {
        $row[$key]['order_id'] = $value['order_id'];
        $row[$key]['dep_id'] = $value['dep_id'];
        $row[$key]['order_sn'] = $value['order_sn'];
        $row[$key]['add_time'] = local_date('Y-m-d H:i', $value['add_time']);
        $row[$key]['start_time'] = local_date('Y-m-d', $value['start_time']);
        $row[$key]['end_time'] = local_date('Y-m-d', $value['end_time']);
		    $cha = DateDiff(local_date('Y-m-d', $value['end_time']),local_date('Y-m-d',time()));
        $row[$key]['daoqi'] = $cha;
        $row[$key]['phone'] = $value['mobile_phone'];
        $row[$key]['cards'] = $value['ID_cards'];
        $row[$key]['u_name'] = $value['user_name'];
        $row[$key]['u_id'] = $value['user_id'];
        $row[$key]['op_status'] = $value['oper_status'];
        
    }
    $arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>