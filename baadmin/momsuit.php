<?php

/**
 * ECSHOP 准妈妈套餐管理
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: yangtuo-laiguangming $
 * $Id: topic.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

$allow_suffix = array('gif', 'jpg', 'png', 'jpeg', 'bmp', 'swf');

/*------------------------------------------------------ */
//-- 专题列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    //admin_priv('topic_manage');

    $smarty->assign('ur_here',     '准妈妈套装');

    $smarty->assign('full_page',   1);
    $list = get_momsuit_list();

    $smarty->assign('topic_list',   $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->assign('action_link', array('text' => '添加套装', 'href' => 'momsuit.php?act=add'));
    $smarty->display('momsuit_list.htm');
}
/* 添加,编辑 */
if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    //admin_priv('topic_manage');

    $isadd     = $_REQUEST['act'] == 'add';
    $smarty->assign('isadd', $isadd);
    $topic_id  = empty($_REQUEST['suit_id']) ? 0 : intval($_REQUEST['suit_id']);

    $te='修改套装';
    if ($isadd)
    {
      $te='添加套装';
    }
    $smarty->assign('ur_here',     $te);
    $smarty->assign('action_link', list_link($isadd));
    
    $smarty->assign('cat_list',   cat_list(0, 1));
    $smarty->assign('brand_list', get_brand_list());


    if (!$isadd)
    {
        $sql = "SELECT * FROM " . $ecs->table('momsuit') . " WHERE Suit_ID = " . $topic_id;
        $topic = $db->getRow($sql);
        $topic['start_time'] = local_date('Y-m-d', $topic['start_time']);
        $topic['end_time']   = local_date('Y-m-d', $topic['end_time']);
        
        
        $sql2 = "SELECT g.goods_id,g.goods_name FROM " . $ecs->table('momsuit_goods') . " sg LEFT JOIN " . $ecs->table('goods') . " g ON sg.goods_id=g.goods_id WHERE sg.Suit_ID=". $topic_id;
        $res = $GLOBALS['db']->getAll( $sql2 );
        $arr_goods = array( );
        foreach ( $res as $idx => $row )
		    {
				    $arr_goods[$row['goods_id']]['goods_id']      = $row['goods_id'];
				    $arr_goods[$row['goods_id']]['goods_name']    = $row['goods_name'];
		    }

        $smarty->assign('momsuit', $topic);
        $smarty->assign('momsuit_goods_list', $arr_goods);
        $smarty->assign('act',   "update");
    }
    else
    {
        $topic = array('title' => '', 'topic_type' => 0, 'url' => 'http://', 'Suit_price' => '0');
        $smarty->assign('momsuit', $topic);

        //create_html_editor('topic_intro');
        $smarty->assign('act', "insert");
    }
    $smarty->display('momsuit_edit.htm');
}
elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    //$momsuit_goods = explode('|',$_POST['topic_data']);
    //foreach ($momsuit_goods AS $key => $val)
    //{
    //    $sql = "insert into " . $ecs->table('momsuit_goods') ." (goods_id) values('$val')";
    //    $db->query($sql);
    //}
    
    
    
    
    //admin_priv('topic_manage');

    $is_insert = $_REQUEST['act'] == 'insert';
    $topic_id  = empty($_POST['topic_id']) ? 0 : intval($_POST['topic_id']);

    // 主图上传
    if ($_FILES['topic_img']['name'] && $_FILES['topic_img']['size'] > 0)
    {
        /* 检查文件合法性 */
        if(!get_file_suffix($_FILES['topic_img']['name'], $allow_suffix))
        {
            sys_msg($_LANG['invalid_type']);
        }

        /* 处理 */
        $name = date('Ymd');
        for ($i = 0; $i < 6; $i++)
        {
            $name .= chr(mt_rand(97, 122));
        }
        $name .= '.' . end(explode('.', $_FILES['topic_img']['name']));
        $target = ROOT_PATH . DATA_DIR . '/afficheimg/' . $name;

        if (move_upload_file($_FILES['topic_img']['tmp_name'], $target))
        {
            $topic_img = DATA_DIR . '/afficheimg/' . $name;
        }
    }
    else if (!empty($_REQUEST['url']))
    {
        /* 来自互联网图片 不可以是服务器地址 */
        if(strstr($_REQUEST['url'], 'http') && !strstr($_REQUEST['url'], $_SERVER['SERVER_NAME']))
        {
            /* 取互联网图片至本地 */
            $topic_img = get_url_image($_REQUEST['url']);
        }
        else{
            sys_msg($_LANG['web_url_no']);
        }
    }
    unset($name, $target);

    $topic_img = empty($topic_img) ? $_POST['img_url'] : $topic_img;
    $htmls = '';



    $title_pic = empty($title_pic) ? $_POST['title_img_url'] : $title_pic;

    require(ROOT_PATH . 'includes/cls_json.php');

    $start_time = local_strtotime($_POST['start_time']);
    $end_time   = local_strtotime($_POST['end_time']);
    $nows       = local_strtotime(date("Y-m-d H:i:s"));
    $Suit_price = !empty($_POST['Suit_price']) ? floatval($_POST['Suit_price'] ) : 0;
    $momsuit_goods = explode('|',$_POST['topic_data']);


    if ($is_insert)
    {
        $sql = "INSERT INTO " . $ecs->table('momsuit') . " (Suit_Name,Suit_img,Suit_price,start_time,end_time,add_timt)" .
                "VALUES ('$_POST[topic_name]', '$topic_img', '$Suit_price', '$start_time', '$end_time', '$nows')"; 
        $db->query($sql);
        $new_suit_id = $db->insert_id();
        
        foreach ($momsuit_goods AS $key => $val)
        {
           $sqlgoods = "insert into " . $ecs->table('momsuit_goods') ." (Suit_ID,goods_id,add_timt) values('$new_suit_id', '$val', '$nows')";
           $db->query($sqlgoods);
        }
    }
    else
    {
        $sql = "UPDATE " . $ecs->table('momsuit') .
                "SET Suit_Name='$_POST[topic_name]', start_time='$start_time', Suit_price='$Suit_price', end_time='$end_time', Suit_img='$topic_img'" .
               " WHERE Suit_ID='$topic_id' LIMIT 1";
        $db->query($sql);
        
        $sqlgoods_de = "DELETE FROM " . $ecs->table('momsuit_goods') . " WHERE Suit_ID=" . $topic_id;
        $db->query($sqlgoods_de);
        
        foreach ($momsuit_goods AS $key => $val)
        {
           $sqlgoods = "insert into " . $ecs->table('momsuit_goods') ." (Suit_ID,goods_id,add_timt) values('$topic_id', '$val', '$nows')";
           $db->query($sqlgoods);
        }
    }


    clear_cache_files();

    $links[] = array('href' => 'momsuit.php', 'text' =>  $_LANG['back_list']);
    $te='修改套装成功';
    if ($is_insert)
    {
      $te='添加套装成功';
    }
    sys_msg($te, 0, $links);
}
elseif ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr = get_goods_list($filters);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['goods_id'],
                       'text'  => $val['goods_name']);
    }

    make_json_result($opt);
}
elseif ($_REQUEST["act"] == "delete")
{
    //admin_priv('topic_manage');

    $sql = "DELETE FROM " . $ecs->table('momsuit') . " WHERE ";
    $sqlgoods = "DELETE FROM " . $ecs->table('momsuit_goods') . " WHERE ";

    if (!empty($_POST['checkboxs']))
    {
        $sql .= db_create_in($_POST['checkboxs'], 'Suit_ID');
        $sqlgoods .= db_create_in($_POST['checkboxs'], 'Suit_ID');
    }
    elseif (!empty($_GET['id']))
    {
        $_GET['id'] = intval($_GET['id']);
        $sql .= "Suit_ID = '$_GET[id]'";
        $sqlgoods .= "Suit_ID = '$_GET[id]'";
    }
    else
    {
        exit;
    }

    $db->query($sql);
    $db->query($sqlgoods);

    clear_cache_files();

    if (!empty($_REQUEST['is_ajax']))
    {
        $url = 'momsuit.php?act=query&' . str_replace('act=delete', '', $_SERVER['QUERY_STRING']);
        ecs_header("Location: $url\n");
        exit;
    }

    $links[] = array('href' => 'momsuit.php', 'text' =>  $_LANG['back_list']);
    sys_msg('删除成功', 0, $links);
}
elseif ($_REQUEST["act"] == "query")
{
    $topic_list = get_momsuit_list();
    $smarty->assign('topic_list',   $topic_list['item']);
    $smarty->assign('filter',       $topic_list['filter']);
    $smarty->assign('record_count', $topic_list['record_count']);
    $smarty->assign('page_count',   $topic_list['page_count']);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    /* 排序标记 */
    $sort_flag  = sort_flag($topic_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $tpl = 'momsuit_list.htm';
    make_json_result($smarty->fetch($tpl), '',array('filter' => $topic_list['filter'], 'page_count' => $topic_list['page_count']));
}

/**
 * 获取专题列表
 * @access  public
 * @return void
 */
function get_momsuit_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 查询条件 */
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'Suit_ID' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('momsuit');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('momsuit'). " ORDER BY $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $query = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $res = array();

    while($topic = $GLOBALS['db']->fetch_array($query)){
        $topic['start_time'] = local_date('Y-m-d',$topic['start_time']);
        $topic['end_time']   = local_date('Y-m-d',$topic['end_time']);
        $topic['url']        = $GLOBALS['ecs']->url() . 'topic.php?topic_id=' . $topic['topic_id'];
        $res[] = $topic;
    }

    $arr = array('item' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 列表链接
 * @param   bool    $is_add     是否添加（插入）
 * @param   string  $text       文字
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true, $text = '')
{
    $href = 'momsuit.php?act=list';
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }
    if ($text == '')
    {
        $text = $GLOBALS['_LANG']['momsuit_list'];
    }

    return array('href' => $href, 'text' => $text);
}

function get_toppic_width_height()
{
    $width_height = array();

    $file_path = ROOT_PATH . 'themes/' . $GLOBALS['_CFG']['template'] . '/topic.dwt';
    if (!file_exists($file_path) || !is_readable($file_path))
    {
        return $width_height;
    }

    $string = file_get_contents($file_path);

    $pattern_width = '/var\s*topic_width\s*=\s*"(\d+)";/';
    $pattern_height = '/var\s*topic_height\s*=\s*"(\d+)";/';
    preg_match($pattern_width, $string, $width);
    preg_match($pattern_height, $string, $height);
    if(isset($width[1]))
    {
        $width_height['pic']['width'] = $width[1];
    }
    if(isset($height[1]))
    {
        $width_height['pic']['height'] = $height[1];
    }
    unset($width, $height);

    $pattern_width = '/TitlePicWidth:\s{1}(\d+)/';
    $pattern_height = '/TitlePicHeight:\s{1}(\d+)/';
    preg_match($pattern_width, $string, $width);
    preg_match($pattern_height, $string, $height);
    if(isset($width[1]))
    {
        $width_height['title_pic']['width'] = $width[1];
    }
    if(isset($height[1]))
    {
        $width_height['title_pic']['height'] = $height[1];
    }

    return $width_height;
}

function get_url_image($url)
{
    $ext = strtolower(end(explode('.', $url)));
    if($ext != "gif" && $ext != "jpg" && $ext != "png" && $ext != "bmp" && $ext != "jpeg")
    {
        return $url;
    }

    $name = date('Ymd');
    for ($i = 0; $i < 6; $i++)
    {
        $name .= chr(mt_rand(97, 122));
    }
    $name .= '.' . $ext;
    $target = ROOT_PATH . DATA_DIR . '/afficheimg/' . $name;

    $tmp_file = DATA_DIR . '/afficheimg/' . $name;
    $filename = ROOT_PATH . $tmp_file;

    $img = file_get_contents($url);

    $fp = @fopen($filename, "a");
    fwrite($fp, $img);
    fclose($fp);

    return $tmp_file;
}
?>
