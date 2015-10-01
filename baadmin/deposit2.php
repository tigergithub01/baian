<?php

/**
 * ECSHOP 订单管理
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
    $smarty->assign('ur_here', $_LANG['11_deposit_list']);
    $smarty->assign('action_link', array('href' => 'order.php?act=list', 'text' => $_LANG['02_order_list']));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('order_query.htm');
}

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('order_view');

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['11_deposit_list']);
    $smarty->assign('ordersn1', $_LANG['deposit_sn']);
    //$smarty->assign('action_link', array('href' => 'order.php?act=order_query', 'text' => $_LANG['11_deposit_list']));

    //$smarty->assign('status_list', $_LANG['cs']);   // 订单状态

    //$smarty->assign('os_unconfirmed',   OS_UNCONFIRMED);
    //$smarty->assign('cs_await_pay',     CS_AWAIT_PAY);
    //$smarty->assign('cs_await_ship',    CS_AWAIT_SHIP);
    $smarty->assign('full_page',        1);

    $order_list = order_list();
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

    $order_list = order_list();

    $smarty->assign('order_list',   $order_list['orders']);
    $smarty->assign('filter',       $order_list['filter']);
    $smarty->assign('record_count', $order_list['record_count']);
    $smarty->assign('page_count',   $order_list['page_count']);
    $sort_flag  = sort_flag($order_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    make_json_result($smarty->fetch('deposit_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}




/**
 *  获取订单列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function order_list()
{
    $result = get_filter();
    if ($result === false)
    {
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
            $filter['page_size'] = 1;
        }

        /* 记录总数 */
        if ($filter['user_name'])
        {
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('deposit') . " AS o ,".
                   $GLOBALS['ecs']->table('users') . " AS u " . $where;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('deposit') . " AS o ". $where;
        }

        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT o.dep_id, o.order_id, u.order_sn, o.add_time, o.start_time, o.start_time, o.end_time ".
                " FROM " . $GLOBALS['ecs']->table('deposit') . " AS o " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('order_info'). " AS u ON o.order_id=u.order_id ".
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
        $row[$key]['_id'] = $value['dep_id'];
        $row[$key]['order_sn'] = $value['order_sn'];
        $row[$key]['add_time'] = local_date('Y-m-d H:i', $value['add_time']);
        $row[$key]['start_time'] = local_date('Y-m-d', $value['start_time']);
        $row[$key]['end_time'] = local_date('Y-m-d', $value['end_time']);
        
    }
    $arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
?>