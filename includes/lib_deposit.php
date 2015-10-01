<?php

/**
 * ECSHOP 购物流程函数库
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_order.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}



/**
 * 取得寄存信息
 * @param   int     $order_id   寄存id
 * @return  array   寄存信息（金额都有相应格式化的字段，前缀是formated_）
 */
function deposit_info($order_id)
{
    /* 计算订单各种费用之和的语句 */
    $order_id = intval($order_id);
    if ($order_id > 0)
    {
        $sql = "SELECT d.*,o.order_sn, o.consignee, o.email, o.address, o.zipcode, ".
                "o.tel, o.mobile, o.sign_building, o.best_time ".
                " FROM " . $GLOBALS['ecs']->table('deposit') .
                " AS d LEFT JOIN ". $GLOBALS['ecs']->table('order_info') .
                " AS o ON d.order_id=o.order_id WHERE d.dep_id = '$order_id'";
    }
    
    $order = $GLOBALS['db']->getRow($sql);

    /* 格式化金额字段 */
    if ($order)
    {
        $order['dep_id']                  = $order['dep_id'];
        $order['order_sn']                = $order['order_sn'];
        $order['user_id']                 = $order['user_id'];
        $order['order_id']                = $order['order_id'];
        $order['start_time']              = local_date('Y-m-d', $order['start_time']);
        $order['end_time']                = local_date('Y-m-d', $order['end_time']);
        $order['add_time']                = local_date('Y-m-d', $order['add_time']);
        $cha = DateDiff($order['end_time'],local_date('Y-m-d',time()));
        $order['daoqi']                   = $cha;
    }

    return $order;
}


/**
 * 取得寄存商品
 * @param   int     $order_id   寄存id
 * @return  array   寄存商品数组
 */
function deposit_goods($order_id)
{
    $sql = "SELECT dep_id, goods_id, goods_name, goods_sn, goods_number, goods_numberB " .
            "FROM " . $GLOBALS['ecs']->table('deposit_goods') .
            " WHERE dep_id = '$order_id'";

    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $goods_list[] = $row;
    }

    //return $GLOBALS['db']->getAll($sql);
    return $goods_list;
}



?>