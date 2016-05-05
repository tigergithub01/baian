<?php

/**
 * ECSHOP 首页文件
 * ============================================================================
 * 版权所有 2005-2008 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: testyang $
 * $Id: index.php 15095 2008-10-27 08:34:33Z testyang $
 */
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2) {
	$smarty->caching = true;
}

// 判断是否有ajax请求
$keywords = ! empty ( $_REQUEST ['keywords'] ) ? trim ( $_REQUEST ['keywords'] ) : '';
include_once ('includes/cls_json.php');
$json = new JSON ();
$result = array (
		'error' => 0,
		'content' => '' 
);

/* if($keywords!="")
{
	$sql = "SELECT distinct keyword  FROM " . $ecs->table('keywords') ." where searchengine='ecshop' and keyword LIKE'%".mysql_like_quote($keywords)."%' order by count DESC";
	$res = $db->selectLimit($sql,10);
	$arr = array();
	while ($row = $db->FetchRow($res))
	{
		$count=$db->getOne("select count(*) from " . $ecs->table('goods') ." where goods_name LIKE '%".mysql_like_quote($row['keyword'])."%' AND is_on_sale = 1 AND is_delete = 0");
		$result['content']=$result['content']."<li onmouseout='javascript:suggestOut(this);' onmouseover='javascript:suggestOver(this);' onclick='javascript:form_submit(this);'><span class='suggest-key'>".$row['keyword']."</span><span class='suggest-result'>".$count."个商品</span></li>";
	}

	if($result['content']!="")$result['content']="<ol>".$result['content']."</ol>";
} */

if ($keywords != "") {
	
	// 先简单分解为单个文字,以后可以改为分词工具scws:eg,https://github.com/hightman/scws
	$keywords = str_replace ( ' ', '', $keywords );
	$arr_chr = array ();
	for($i = 0; $i < mb_strlen ( $keywords ); $i ++) {
		$chr = mb_substr ( $keywords, $i, 1, 'utf-8' );
		if (! empty ( $chr )) {
			$arr_chr [] = $chr;
		}
	}
	
	// 然后根据分词结果拼装sql,查询历史搜索关键词
	$keywords_sql = " AND (";
	$operator = "AND";
	foreach ( $arr_chr as $key => $val ) {
		$val = mysql_like_quote ( trim ( $val ) );
		if ($key > 0 && $key < count ( $arr_chr ) && count ( $arr_chr ) > 1) {
			$keywords_sql .= $operator;
		}
		$keywords_sql .= "(keyword LIKE '%$val%')";
	}
	$keywords_sql .= ')';
	$sql = "SELECT distinct keyword  FROM " . $ecs->table ( 'keywords' ) . " where searchengine='ecshop' " . $keywords_sql . " order by count DESC";
	$res = $db->selectLimit ( $sql, 10 );
	
	// 获取商品件数
	$arr = array ();
	while ( $row = $db->FetchRow ( $res ) ) {
		$keyword = $row ['keyword'];
		$arr_chr = array ();
		// 先简单分解为单个文字,以后可以改为分词工具scws:eg,https://github.com/hightman/scws
		/* for($i = 0; $i < mb_strlen ( $keyword ); $i ++) {
			$chr = mb_substr ( $keyword, $i, 1, 'utf-8' );
			if (! empty ( $chr )) {
				$arr_chr [] = $chr;
			}
		} */
		
		//scws分词处理 tiger.guo 20160506
		$arr_chr = get_scwp_words($keyword);
		
		$keywords_sql = " AND (";
		foreach ( $arr_chr as $key => $val ) {
			$val = mysql_like_quote ( trim ( $val ) );
			if ($key > 0 && $key < count ( $arr_chr ) && count ( $arr_chr ) > 1) {
				$keywords_sql .= $operator;
			}
// 			$keywords_sql .= "(goods_name LIKE '%$val%' OR goods_sn LIKE '%$val%' OR keywords LIKE '%$val%')";
			$keywords_sql .= "(goods_name LIKE '%$val%' OR keywords LIKE '%$val%')";
		}
		$keywords_sql .= ')';
		
		
		$sql = "select count(*) from " . $ecs->table ( 'goods' ) . " where is_on_sale = 1 AND is_delete = 0 " . $keywords_sql;
		$count = $db->getOne ( $sql );
		
		if ($count > 0) {
// 			var_dump($count);
// 			var_dump($sql);
// 			$result ['content'] = $result ['content'] . "<li onmouseout='javascript:suggestOut(this);' onmouseover='javascript:suggestOver(this);' onclick='javascript:form_submit(this);'><span class='suggest-key'>" . $row ['keyword'] . "</span><span class='suggest-result'>" . $count . "个商品</span></li>";
// 			$result ['content'] = $result ['content'] . "<li><a href=\"search.php?keywords=$row[keyword]\">".$row ['keyword']."</a>"."<span>".$count."个商品</span>"."</li>";
			$result ['content'] = $result ['content'] . "<li><a href=\"search.php?keywords=$row[keyword]\">".$row ['keyword']."<span>".$count."个商品</span>"."</a>"."</li>";
// 			$result ['content'] = $result ['content'] . "<li onmouseout='javascript:suggestOut(this);' onmouseover='javascript:suggestOver(this);' onclick='javascript:form_submit(this);'><span class='suggest-key'>" . $row ['keyword'] . "</span><span class='suggest-result'>" . $count . "个商品</span></li>";
		}
	}
	
	if ($result ['content'] != "")
		$result ['content'] = "<ul class='ul-so2'>" . $result ['content'] . "</ul>";
}
die ( $json->encode ( $result ) );
?>