<?php

/**
 * ECSHOP 超值礼包管理程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: gift_giving.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("gift_giving"), $db, 'giving_id', 'user_id');

/*------------------------------------------------------ */
//-- 添加活动
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('gift_giving');
    

    /* 组合商品 */

    /* 初始化信息 */

    $smarty->assign('gift_giving',      $gift_giving);
    $smarty->assign('ur_here',      $_LANG['gift_giving_add']);
    $smarty->assign('action_link',  array('text' => $_LANG['15_gift_giving_list'], 'href'=>'gift_giving.php?act=list'));
    $smarty->assign('form_action',  'insert');

    assign_query_info();
    $smarty->display('gift_giving_info.htm');
}

elseif ($_REQUEST['act'] =='insert')
{
    /* 权限判断 */
    admin_priv('gift_giving');
    


    /* 将时间转换成整数 */


    /* 插入数据 */
    $record = array('send_flag' => isset($_POST['send_flag'])?intval($_POST['send_flag']):0, 
    		'sent_time' => empty($_POST['sent_time'])?null:local_strtotime($_POST['sent_time']), 
    		'order_sn' => $_POST['order_sn'],
            'sent_memo' => $_POST['sent_memo']);

    $db->AutoExecute($ecs->table('gift_giving'),$record,'INSERT');

    /* 礼包编号 */
    $gift_giving_id = $db->insert_id();


    admin_log($gift_giving_id,'add','gift_giving');
    $link[] = array('text' => $_LANG['back_list'], 'href'=>'gift_giving.php?act=list');
    $link[] = array('text' => $_LANG['continue_add'], 'href'=>'gift_giving.php?act=add');
    sys_msg($_LANG['add_succeed'],0,$link);
}

/*------------------------------------------------------ */
//-- 编辑活动
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('gift_giving');

    $sql = "SELECT gg.giving_id, gg.user_id, gg.apply_time, gg.send_flag, gg.sent_time, gg.order_sn, gg.sent_memo,u.user_name AS user_name ".
    		" FROM " . $GLOBALS['ecs']->table('gift_giving') . " AS gg" .
    		" LEFT JOIN ". $GLOBALS['ecs']->table('users') ." AS u ON (gg.user_id = u.user_id)".
    		" WHERE gg.giving_id = "  . $_REQUEST['id'] ;
    $gift_giving = $GLOBALS['db']->getRow($sql);
    $gift_giving['apply_time'] = local_date($GLOBALS['_CFG']['time_format'], $gift_giving['apply_time']);
    $gift_giving['sent_time']   = empty($gift_giving['sent_time'])?"":local_date($GLOBALS['_CFG']['time_format'], $gift_giving['sent_time']);

    $smarty->assign('gift_giving',           $gift_giving);
    $smarty->assign('ur_here',           "编辑礼品索要信息");
    $smarty->assign('action_link',       array('text' => $_LANG['15_gift_giving_list'], 'href'=>'gift_giving.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action',       'update');

    assign_query_info();
    $smarty->display('gift_giving_info.htm');

}
elseif ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('gift_giving');
    
    /* 将时间转换成整数 */
//     $_POST['sent_time']   = local_strtotime($_POST['sent_time']);

    /* 更新数据 */
    $record = array('send_flag' => isset($_POST['send_flag'])?intval($_POST['send_flag']):0, 
    		'sent_time' => empty($_POST['sent_time'])?null:local_strtotime($_POST['sent_time']), 
    		'order_sn' => $_POST['order_sn'],
            'sent_memo' => $_POST['sent_memo']);
    $db->autoExecute($ecs->table('gift_giving'), $record, 'UPDATE', "giving_id = '" . $_POST['id'] . "'" );

    admin_log($_POST['id'],'edit','gift_giving');
    $link[] = array('href' => 'gift_giving.php?act=edit&id='.$_POST['id'], 'text' => '编辑礼品索要');
    $link[] = array('href' => 'gift_giving.php?act=list', 'text' => $_LANG['15_gift_giving_list']);
    
    sys_msg($_LANG['edit_succeed'],0,$link);
}

/*------------------------------------------------------ */
//-- 删除指定的活动
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('gift_giving_manage');

    $id = intval($_GET['id']);

    $exc->drop($id);

    $sql = "DELETE FROM " .$ecs->table('gift_giving') .
            " WHERE giving_id='$id'";
    $db->query($sql);

    $url = 'gift_giving.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 活动列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['16_gift_giving']);
//     $smarty->assign('action_link',  array('text' => $_LANG['gift_giving_add'], 'href'=>'gift_giving.php?act=add'));
//     $smarty->assign('action_link2',  array('text' => '添加推荐组合', 'href'=>'gift_giving.php?act=add&act_type='.GAT_RECOMMAND));

    $gift_givings = get_gift_giving_list();

    $smarty->assign('gift_giving_list', $gift_givings['gift_givings']);
    $smarty->assign('filter',       $gift_givings['filter']);
    $smarty->assign('record_count', $gift_givings['record_count']);
    $smarty->assign('page_count',   $gift_givings['page_count']);

    $sort_flag  = sort_flag($gift_givings['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $smarty->assign('full_page',    1);
    assign_query_info();
    $smarty->display('gift_giving_list.htm');
}

/*------------------------------------------------------ */
//-- 查询、翻页、排序
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $gift_givings = get_gift_giving_list();

    $smarty->assign('gift_giving_list', $gift_givings['gift_givings']);
    $smarty->assign('filter',       $gift_givings['filter']);
    $smarty->assign('record_count', $gift_givings['record_count']);
    $smarty->assign('page_count',   $gift_givings['page_count']);

    $sort_flag  = sort_flag($gift_givings['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('gift_giving_list.htm'), '',
        array('filter' => $gift_givings['filter'], 'page_count' => $gift_givings['page_count']));
}



/**
 * 获取礼品索要
 *
 * @access  public
 *
 * @return void
 */
function get_gift_giving_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 查询条件 */
        $filter['keywords']   = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'apply_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = (!empty($filter['keywords'])) ? " AND u.user_name like '%". mysql_like_quote($filter['keywords']) ."%'" : '';

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('gift_giving') . " AS gg" .
        		" LEFT JOIN ". $GLOBALS['ecs']->table('users') ." AS u ON (gg.user_id = u.user_id)".
        		" WHERE 1 "  . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);        

        /* 获活动数据 */
        /* $sql = "SELECT act_id, act_name AS gift_giving_name, start_time, end_time, is_finished, ext_info ".
               " FROM " . $GLOBALS['ecs']->table('goods_activity') .
               " WHERE act_type = " . GAT_gift_giving . $where .
               " ORDER by $filter[sort_by] $filter[sort_order] LIMIT ". $filter['start'] .", " . $filter['page_size']; */
        
        $sql = "SELECT gg.giving_id, gg.user_id, gg.apply_time, gg.send_flag, gg.sent_time, gg.order_sn, gg.sent_memo,u.user_name AS user_name ".
        		" FROM " . $GLOBALS['ecs']->table('gift_giving') . " AS gg" .
        		" LEFT JOIN ". $GLOBALS['ecs']->table('users') ." AS u ON (gg.user_id = u.user_id)".
        		" WHERE 1 "  . $where .
        		" ORDER by $filter[sort_by] $filter[sort_order] LIMIT ". $filter['start'] .", " . $filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
    	$row[$key]['apply_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['apply_time']);
        $row[$key]['sent_time']   = empty($val['sent_time'])?"":local_date($GLOBALS['_CFG']['time_format'], $val['sent_time']);
    }

    $arr = array('gift_givings' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>