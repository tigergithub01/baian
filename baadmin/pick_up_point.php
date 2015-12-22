<?php

/**
 * ECSHOP 管理中心文章处理程序文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: pick_up_point.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("pick_up_point"), $db, 'point_id', 'point_name');


/*------------------------------------------------------ */
//-- 文章列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['54_pick_up_point']);
    $smarty->assign('action_link',  array('text' => "新增自提点", 'href' => 'pick_up_point.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $pick_up_point_list = get_pick_up_points_list();

    $smarty->assign('pick_up_point_list',    $pick_up_point_list['arr']);
    $smarty->assign('filter',          $pick_up_point_list['filter']);
    $smarty->assign('record_count',    $pick_up_point_list['record_count']);
    $smarty->assign('page_count',      $pick_up_point_list['page_count']);

    $sort_flag  = sort_flag($pick_up_point_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('pick_up_point_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('pick_up_point');

    $pick_up_point_list = get_pick_up_points_list();

    $smarty->assign('pick_up_point_list',    $pick_up_point_list['arr']);
    $smarty->assign('filter',          $pick_up_point_list['filter']);
    $smarty->assign('record_count',    $pick_up_point_list['record_count']);
    $smarty->assign('page_count',      $pick_up_point_list['page_count']);

    $sort_flag  = sort_flag($pick_up_point_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('pick_up_point_list.htm'), '',
        array('filter' => $pick_up_point_list['filter'], 'page_count' => $pick_up_point_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('pick_up_point');


    /*初始化*/
    $pick_up_point = array();

    /* 取得分类、品牌 */
    $smarty->assign('goods_cat_list', cat_list());

    $smarty->assign('province_list', get_pick_point_region_list());
    $smarty->assign('countries',        get_regions());
    $smarty->assign('pick_up_point',     $pick_up_point);
    $smarty->assign('ur_here',     "新增自提点");
    $smarty->assign('action_link', array('text' => $_LANG['54_pick_up_point'], 'href' => 'pick_up_point.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->display('pick_up_point_info.htm');
}

/*------------------------------------------------------ */
//-- 添加文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('pick_up_point');

    $pick_up_point['point_name']   = $_POST['point_name'];
    $pick_up_point['point_addr'] = $_POST['point_addr'];
    $pick_up_point['regions']    =  $_POST['regions'] ? "" . implode(",",$_POST['regions'])  : "";
    
    if(empty($pick_up_point['point_name'])){
    	sys_msg("自提点不能为空!", 1);
    }
    
    if(empty($pick_up_point['point_addr'])){
    	sys_msg("自提点地址不能为空!", 1);
    }
    
    if(empty($pick_up_point['regions'])){
    	sys_msg("自提点覆盖区域不能为空!", 1);
    }
    
    
    if ($db->autoExecute($ecs->table('pick_up_point'), $pick_up_point) !== false)
    {
    	admin_log($_POST['point_name'],'add','pick_up_point');
    	
    	$links = array(array('href' => 'pick_up_point.php?act=list', 'text' => $_LANG['back_list']));
    	sys_msg("自提点插入成功", 0, $links);
    }
    else
    {
    	sys_msg("自提点插入失败", 1);
    }
}

/*------------------------------------------------------ */
//-- 编辑
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('pick_up_point');
    $pick_up_point = get_pick_up_point(intval($_REQUEST['id']));
    
    if (empty($pick_up_point))
    {
    	sys_msg("自提点不存在", 1);
    }
    
    $smarty->assign('regions', get_pick_point_region_list($_REQUEST['id']));
    $smarty->assign('countries',        get_regions());
    $smarty->assign('pick_up_point',     $pick_up_point);
    $smarty->assign('ur_here',     "编辑自提点");
    $smarty->assign('action_link', array('text' => $_LANG['54_pick_up_point'], 'href' => 'pick_up_point.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('pick_up_point_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('pick_up_point');
    
    $pick_up_point['point_name']   = $_POST['point_name'];
    $pick_up_point['point_addr'] = $_POST['point_addr'];
    $pick_up_point['regions']    =  $_POST['regions'] ? "" . implode(",",$_POST['regions'])  : "";
    
    if(empty($pick_up_point['point_name'])){
    	sys_msg("自提点不能为空!", 1);
    }
    
    if(empty($pick_up_point['point_addr'])){
    	sys_msg("自提点地址不能为空!", 1);
    }
    
    if(empty($pick_up_point['regions'])){
    	sys_msg("自提点覆盖区域不能为空!", 1);
    }
    
    
    if ($db->autoExecute($ecs->table('pick_up_point'), $pick_up_point, 'UPDATE', "point_id='".$_POST['id']."'") !== false)
    {
    	$links = array(array('href' => 'pick_up_point.php?act=list', 'text' => $_LANG['back_list']));
    	sys_msg("自提点更新成功", 0, $links);
    }
    else
    {
    	sys_msg("自提点更新失败", 1);
    }
}

/*------------------------------------------------------ */
//-- 删除文章主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('pick_up_point');

    $id = intval($_GET['id']);
	$pick_up_point = get_pick_up_point($id);
    $name = $pick_up_point['point_name'];
    if ($exc->drop($id))
    {
        admin_log(addslashes($name),'remove','pick_up_point');
        clear_cache_files();
    }

    $url = 'pick_up_point.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}


/* 获得文章列表 */
function get_pick_up_points_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.point_name' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        /* $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        } */

        /* 文章总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('pick_up_point'). ' AS a '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取文章数据 */
        $sql = 'SELECT a.* '.
               'FROM ' .$GLOBALS['ecs']->table('pick_up_point'). ' AS a '.
               'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);

        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/*
* 自提点对应区域列表
*/
function get_pick_point_region_list($point_id='')
{
	if ($point_id)
	{
		$sql = "select regions from ". $GLOBALS['ecs']->table('pick_up_point') ." where point_id='$point_id' ";
		$regions = $GLOBALS['db']->getOne($sql);
	}
	$region_list=array();	
	$region_id_list = explode(',', $regions);
	
	foreach ($region_id_list as $key => $value) {
		if(empty($value)){
			continue;
		}
		$sql="select * from ". $GLOBALS['ecs']->table("region") ." where region_id = $value";
		$row=$GLOBALS['db']->getRow($sql);
		$region_list[$row['region_id']]['name'] = $row['region_name'];
	}
	return $region_list;
}

function get_pick_up_point($point_id)
{
	$sql = "SELECT * FROM " .$GLOBALS['ecs']->table('pick_up_point'). " WHERE point_id='$point_id'";

	return $GLOBALS['db']->getRow($sql);
}

?>