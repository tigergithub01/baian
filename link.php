<?php

/**
 * ECSHOP 支付配送DEMO
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: laiguangming $
 * $Id: link.php 17217 2013-07-27 11:17:08Z laiguangming $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */


if ($_SESSION['user_id'] > 0)
{
    $consignee_list = get_consignee_list($_SESSION['user_id']);

    $choose['country'] = isset($_POST['country']) ? intval($_POST['country']) : $consignee_list[0]['country'];
    $choose['province'] = isset($_POST['province']) ? intval($_POST['province']) : $consignee_list[0]['province'];
    $choose['city'] = isset($_POST['city']) ? intval($_POST['city']) : $consignee_list[0]['city'];
    $choose['district'] = isset($_POST['district']) ? intval($_POST['district']) : (isset($consignee_list[0]['district']) ? $consignee_list[0]['district'] : 0 );
}
else
{
    $choose['country'] = isset($_POST['country']) ? intval($_POST['country']) : $_CFG['shop_country'];
    $choose['province'] = isset($_POST['province']) ? intval($_POST['province']) : 2;
    $choose['city'] = isset($_POST['city']) ? intval($_POST['city']) : 35;
    $choose['district'] = isset($_POST['district']) ? intval($_POST['district']) : 417;
}



/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

assign_template();
assign_dynamic('link');
$position = assign_ur_here(0, $_LANG['shopping_links']);
$smarty->assign('page_title', $position['title']);    // 页面标题
$smarty->assign('ur_here',    $position['ur_here']);  // 当前位置

$smarty->assign('helps',      get_shop_help());       // 网店帮助
$smarty->assign('lang',       $_LANG);


$smarty->assign('choose',     $choose);




/* links */
$links = index_get_links();
$smarty->assign('img_links',       $links['img']);
$smarty->assign('txt_links',       $links['txt']);


$smarty->display('link.dwt');













/**
 * 获得所有的友情链接
 *
 * @access  private
 * @return  array
 */
function index_get_links()
{
    $sql = 'SELECT link_logo, link_name, link_url FROM ' . $GLOBALS['ecs']->table('friend_link') . ' ORDER BY show_order';
    $res = $GLOBALS['db']->getAll($sql);
    $links['img'] = $links['txt'] = array();
    foreach ($res AS $row)
    {
        if (!empty($row['link_logo']))
        {
            $links['img'][] = array('name' => $row['link_name'],
                                    'url'  => $row['link_url'],
                                    'logo' => $row['link_logo']);
        }
        else
        {
            $links['txt'][] = array('name' => $row['link_name'],
                                    'url'  => $row['link_url']);
        }
    }
    return $links;
}

?>