<?php

/**
 * ECSHOP SQL语句执行类。在调用该类方法之前，请参看相应方法的说明。
 *
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: cls_sql_executor.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

class cls_article
{
    /**
 * get article by acticle id
 * @param unknown $id
 * @return unknown
 */
function get_article($article_id)
{
	/* 获得文章的信息 */
	$sql = 'SELECT article_id, title,content' .
			' FROM ' .$GLOBALS['ecs']->table('article') .
			' WHERE article_id='.$article_id;
	$row = $GLOBALS['db']->getRow($sql);
	return $row;
}
}

?>