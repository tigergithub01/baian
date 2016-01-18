<?php

/**
 * ECSHOP 管理中心文章处理程序文件
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: within_links 17095 2010-04-12 10:26:10Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");
require_once(ROOT_PATH . 'includes/cls_image.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("relative_module"), $db, 'module_id', 'module_name');
$image = new cls_image();
/* 允许上传的文件类型 */
$allow_file_types = '|GIF|JPG|PNG|BMP|';

/*------------------------------------------------------ */
//-- 文章列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
	admin_priv('relative_module');
    $filter = array();
    $smarty->assign('ur_here',      $_LANG['54_relative_module']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_module'], 'href' => 'relative_module.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $relative_module = get_relative_module();
	
    $smarty->assign('relative_module',    $relative_module['arr']);
    $smarty->assign('filter',          $relative_module['filter']);
    $smarty->assign('record_count',    $relative_module['record_count']);
    $smarty->assign('page_count',      $relative_module['page_count']);

    $sort_flag  = sort_flag($relative_module['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('relative_module.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('relative_module');

    $get_relative_module = get_relative_module();

    $smarty->assign('relative_module',    $get_relative_module['arr']);
    $smarty->assign('filter',          $get_relative_module['filter']);
    $smarty->assign('record_count',    $get_relative_module['record_count']);
    $smarty->assign('page_count',      $get_relative_module['page_count']);

    $sort_flag  = sort_flag($get_relative_module['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('relative_module.htm'), '',
        array('filter' => $get_relative_module['filter'], 'page_count' => $get_relative_module['page_count']));
}

/*------------------------------------------------------ */
//-- 添加
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
	admin_priv('relative_module');
  
	include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
	
	$module_info['is_show']=1;
	
	/* 创建 html editor */
	create_html_editor('header_content', '');
	create_html_editor('footer_content', '');
	
	$smarty->assign('module_info', $module_info);
    $smarty->assign('ur_here',     $_LANG['module_name']);
    $smarty->assign('action_link', array('text' => $_LANG['54_relative_module'], 'href' => 'relative_module.php?act=list'));
    $smarty->assign('form_action', 'insert');
    assign_query_info();
    $smarty->display('relative_module_info.htm');
}

/*------------------------------------------------------ */
//-- 添加文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
	admin_priv('relative_module');
    
	$module_info = $_POST['module_info'];
	$module_info['header_content'] = $_POST['header_content'];
	$module_info['footer_content'] = $_POST['footer_content'];

    /*检查是否重复*/
    $is_only = $exc->is_only('module_name', $module_info['module_name'],0, "");
    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['module_name_exist'], stripslashes($module_info['module_name'])), 1);
    }
	
    /*插入数据*/
    $db->autoExecute($ecs->table('relative_module'), $module_info);

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'relative_module.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'relative_module.php?act=list';

    admin_log($module_info['module_name'],'add','relative_module');

    clear_cache_files(); // 清除相关的缓存文件

    sys_msg($_LANG['module_succed'],0, $link);
}

/*------------------------------------------------------ */
//-- 编辑
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{	
	
    /* 权限判断 */
    admin_priv('relative_module');
    
    include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
    
    /* 取文章数据 */
    $sql = "SELECT * FROM " .$ecs->table('relative_module'). " WHERE module_id='$_REQUEST[id]'";
    $module_info = $db->GetRow($sql);
    
    /* 创建 html editor */
    create_html_editor('header_content', $module_info['header_content']);
    create_html_editor('footer_content', $module_info['footer_content'],'FCKeditor_footer');
    
    
    $smarty->assign('module_info',     $module_info);
    $smarty->assign('ur_here',     $_LANG['edit_store']);
    $smarty->assign('action_link', array('text' => $_LANG['54_relative_module'], 'href' => 'relative_module.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('relative_module_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('relative_module');
    $module_id = $_POST['id'];
    $module_info = $_POST['module_info'];
	$module_info['header_content'] = $_POST['header_content'];
	$module_info['footer_content'] = $_POST['footer_content'];
	/*检查是否相同*/
    $is_only = $exc->is_only('module_name', $module_info['module_name'], $_POST['id'], "");

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['module_name_exist'], stripslashes($module_info['module_name'])), 1);
    }
	
    if ($db->autoExecute($ecs->table('relative_module'), $module_info, 'UPDATE', "module_id='$module_id'"))
    {
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'relative_module.php?act=list&' . list_link_postfix();

        $note = sprintf($_LANG['module_edit_succed'], stripslashes($module_info['module_name']));
        admin_log($module_info['module_name'], 'edit', 'relative_module');

        clear_cache_files();

        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑文章主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_module_name')
{
    check_authz_json('relative_module');

    $id    = intval($_POST['id']);
    $module_name = json_str_iconv(trim($_POST['val']));

    /* 检查文章标题是否重复 */
    if ($exc->num("module_name", $module_name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['module_name_exist'], $module_name));
    }
    else
    {
        if ($exc->edit("module_name = '$module_name'", $id))
        {
            clear_cache_files();
            admin_log($module_name, 'edit', 'module_info');
            make_json_result(stripslashes($module_name));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 编辑排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('relative_module');

    $id    = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    /* 检查输入的值是否合法 */
    if (!preg_match("/^[0-9]+$/", $order))
    {
        make_json_error(sprintf($_LANG['enter_int'], $order));
    }
    else
    {
        if ($exc->edit("sort_order = '$order'", $id))
        {
            clear_cache_files();
            make_json_result(stripslashes($order));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_is_show')
{
    check_authz_json('relative_module');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if ($exc->edit("is_show = '$val'", $id))
    {
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}


/*------------------------------------------------------ */
//-- 删除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    admin_priv('relative_module');
	$id = intval($_GET['id']);
    $module_name = $exc->get_name($id);
    if ($exc->drop($id))
    {
        admin_log(addslashes($module_name),'remove','relative_module');
        clear_cache_files();
    }

    $url = 'relative_module.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}


/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch')
{
    admin_priv('relative_module');
	/* 批量删除 */
    if (isset($_POST['type']))
    {
        if ($_POST['type'] == 'button_remove')
        {
            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_record'], 1);
            }


            foreach ($_POST['checkboxes'] AS $key => $id)
            {
				$name = $exc->get_name($id);
			    if ($exc->drop($id))
                {
                    admin_log(addslashes($name),'remove','relative_module');
                }
            }

        }

      
    }

    /* 清除缓存 */
    clear_cache_files();
    $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'relative_module.php?act=list');
    sys_msg($_LANG['batch_handle_ok'], 0, $lnk);
}



/* 获得文章列表 */
function get_relative_module()
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
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.module_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND a.module_name LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
      

        /* 内链总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('relative_module'). ' AS a '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取内链数据 */
        $sql = 'SELECT a.* '.
               'FROM ' .$GLOBALS['ecs']->table('relative_module'). ' AS a '.
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
        //$rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);
        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


?>