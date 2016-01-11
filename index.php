<?php

/**
 * ECSHOP 首页文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: index.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);

$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";

/* ecs_header("Location: www/index.html\n");
exit; */

/* if(($ua == '' || preg_match($uachar, $ua))&& !strpos(strtolower($_SERVER['REQUEST_URI']),'wap'))
{
    $Loaction = 'mobile/';

    if (!empty($Loaction))
    {
        ecs_header("Location: $Loaction\n");

        exit;
    }

} */
/*------------------------------------------------------ */
//-- Shopex系统地址转换
/*------------------------------------------------------ */
if (!empty($_GET['gOo']))
{
    if (!empty($_GET['gcat']))
    {
        /* 商品分类。*/
        $Loaction = 'category.php?id=' . $_GET['gcat'];
    }
    elseif (!empty($_GET['acat']))
    {
        /* 文章分类。*/
        $Loaction = 'article_cat.php?id=' . $_GET['acat'];
    }
    elseif (!empty($_GET['goodsid']))
    {
        /* 商品详情。*/
        $Loaction = 'goods.php?id=' . $_GET['goodsid'];
    }
    elseif (!empty($_GET['articleid']))
    {
        /* 文章详情。*/
        $Loaction = 'article.php?id=' . $_GET['articleid'];
    }

    if (!empty($Loaction))
    {
        ecs_header("Location: $Loaction\n");

        exit;
    }
}

//判断是否有ajax请求
$act = !empty($_GET['act']) ? $_GET['act'] : '';
if ($act == 'cat_rec')
{
    $rec_array = array(1 => 'best', 2 => 'new', 3 => 'hot');
    $rec_type = !empty($_REQUEST['rec_type']) ? intval($_REQUEST['rec_type']) : '1';
    $cat_id = !empty($_REQUEST['cid']) ? intval($_REQUEST['cid']) : '0';
    include_once('includes/cls_json.php');
    $json = new JSON;
    $result   = array('error' => 0, 'content' => '', 'type' => $rec_type, 'cat_id' => $cat_id);

    $children = get_children($cat_id);
    $smarty->assign($rec_array[$rec_type] . '_goods',      get_category_recommend_goods($rec_array[$rec_type], $children));    // 推荐商品
    $smarty->assign('cat_rec_sign', 1);
    $result['content'] = $smarty->fetch('library/recommend_' . $rec_array[$rec_type] . '.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));

if (!$smarty->is_cached('index.dwt', $cache_id))
{
    assign_template();

    $position = assign_ur_here();
    $smarty->assign('page_title',      $position['title']);    // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置
	$smarty->assign('pvnewcomments', index_get_comments());

    /* meta information */
    $smarty->assign('keywords',        htmlspecialchars($_CFG['shop_keywords']));
    $smarty->assign('description',     htmlspecialchars($_CFG['shop_desc']));
    $smarty->assign('flash_theme',     $_CFG['flash_theme']);  // Flash轮播图片模板
	/* $F1= tui_product(332,10); //一楼精品
	$F2=tui_product(333,10);//二楼精品
 	$F3=tui_product(336,10);//三楼精品
	$F4=tui_product(335,10);//四楼精品
	$F5=tui_product(334,10);//五楼楼精品
	$F6=tui_product(337,10);//六楼精品
	$F7=tui_product(339,10);//7楼精品 */
    $smarty->assign('F1',$F1);
	$smarty->assign('F2',$F2); 
    $smarty->assign('F3',$F3); 
    $smarty->assign('F4',$F4); 
    $smarty->assign('F5',$F5); 
    $smarty->assign('F6',$F6);
	 $smarty->assign('F7',$F7); 
	 $cur_period = $GLOBALS['db']->getRow("select * from web_period order by period_id desc"); 
        	$left_articles = $GLOBALS['db']->getAll("select * from web_comment where isshow = 1 order by addtime desc limit 6 "); 
        	$act_baobao = $GLOBALS['db']->getRow("select * from web_join_info where period_id = '".$cur_period['period_id']."' and status = 1 order by vote_num desc ");
        	//echo "<pre>";var_dump($act_baobao);echo "</pre>";
	//调去栏目
	
	//热门评论start
   /* $plan= get_childname(0); //读取一级栏目下的子栏目
	$i=0;
	foreach($plan as $value){
		//var_dump($value['id']);
	$listz =	get_ecs_category_ids($value['id']);
     //$ax=get_ecs_category_ids($listz);
	$ax= get_my_comment($listz, 6);
	$redate[$i]=$ax;
	$i++;
		}
	$smarty->assign('rmm',$redate); */
	
	$hot_comments = get_hot_comment(6);
	$smarty->assign('hot_comments',$hot_comments);
	//热门评论end
	
	//	var_dump($listz);
	//$ax=get_ecs_category_ids(332);*/
  $F['1_img']=get_advlist('首页-分类ID332-左边图片', 2);
  $F['1_left']=get_advlist('首页-分类ID332-底部图片-左', 1); 
  $F['1_right']=get_advlist('首页-分类ID332-底部图片-右', 1); 
  $F['2_img']=get_advlist('首页-分类ID333-左边图片', 2); 
  $F['2_left']=get_advlist('首页-分类ID333-底部图片-左', 1);
  $F['2_right']=get_advlist('首页-分类ID333-底部图片-右', 1);
  $F['3_img']=get_advlist('首页-分类ID336-左边图片', 2);
  $F['3_left']=get_advlist('首页-分类ID336-底部图片-左', 1);
  $F['3_right']=get_advlist('首页-分类ID336-底部图片-右', 1);
  $F['4_img']=get_advlist('首页-分类ID335-左边图片', 2);
  $F['4_left']=get_advlist('首页-分类ID335-底部图片-左', 1);
  $F['4_right']=get_advlist('首页-分类ID335-底部图片-右', 1);
  $F['5_img']=get_advlist('首页-分类ID334-左边图片', 2);
  $F['5_left']=get_advlist('首页-分类ID334-底部图片-左', 1);
  $F['5_right']=get_advlist('首页-分类ID334-底部图片-右', 1);
  $F['6_img']=get_advlist('首页-分类ID337-左边图片', 2);
  $F['6_left']=get_advlist('首页-分类ID337-底部图片-左', 1);
  $F['6_right']=get_advlist('首页-分类ID337-底部图片-右', 1);
  $F['7_img']=get_advlist('首页-分类ID339-左边图片', 2);
  $F['7_left']=get_advlist('首页-分类ID339-底部图片-左',1);
  $F['7_right']=get_advlist('首页-分类ID339-底部图片-右',1);
  $F['8_left']= get_advlist('首页_精品聚惠_左边',1); 
  $F['8_right']= get_advlist('首页_精品聚惠_右边',1);
  
  //热点文章
  //$listt= get_article_new（2,'art_id'）
	  $smarty->assign('Fimg',$F); 
  $smarty->assign('hotarticle',get_article_new(array(19),'art_id'));
$smarty->assign( 'bestGoods344',get_category_recommend_goods('best', 
$children));
	$ax=get_category_recommend_goods('hot',$children) ;
	
	$smarty->assign('pnam0',$plan);
	// var_dump($F1['content']); 
 

    $smarty->assign('feed_url',        ($_CFG['rewrite'] == 1) ? 'feed.xml' : 'feed.php'); // RSS URL

    $smarty->assign('categories',      get_categories_tree()); // 分类树
	$smarty->assign('categories1', get_child_tree('332')); // 指定分类下的小类
	$smarty->assign('categories2', get_child_tree('333')); // 指定分类下的小类
	$smarty->assign('categories3', get_child_tree('336')); // 指定分类下的小类
	$smarty->assign('categories4', get_child_tree('335')); // 指定分类下的小类
	$smarty->assign('categories5', get_child_tree('334')); // 指定分类下的小类
	$smarty->assign('categories6', get_child_tree('337')); // 指定分类下的小类
	$smarty->assign('categories7', get_child_tree('339')); // 指定分类下的小类
    $smarty->assign('helps',           get_shop_help());       // 网店帮助
    $smarty->assign('top_goods',       get_top10());           // 销售排行

    $smarty->assign('best_goods',      get_recommend_goods('best'));    // 推荐商品
    $smarty->assign('new_goods',       get_recommend_goods('new'));     // 最新商品
    $smarty->assign('hot_goods',       get_recommend_goods('hot'));     // 热点文章
    $smarty->assign('promotion_goods', get_promote_goods()); // 特价商品
	$smarty->assign("huiju",getads(166,2));
	$smarty->assign("ym",getads(159,2));
	$smarty->assign("shipin",getads(160,2));
	$smarty->assign("yongpin",getads(161,2));
	$smarty->assign("fushi",getads(162,2));
	$smarty->assign("tongchuang",getads(163,2));
	$smarty->assign("wanju",getads(164,2));
	$smarty->assign("baihuo",getads(165,2));
	$smarty->assign("f1tong",getads(167,1));
	$smarty->assign("f2tong",getads(168,1));
	$smarty->assign("f3tong",getads(169,1));
	$smarty->assign("f4tong",getads(170,1));
	$smarty->assign("f5tong",getads(171,1));
	$smarty->assign("f6tong",getads(172,1));
	$smarty->assign("f7tong",getads(173,1));
	$smarty->assign("dibu",getads(183,1));//首页底部广告图
    
	$smarty->assign('brand_list',      get_brands());
    $smarty->assign('promotion_info',  get_promotion_info()); // 增加一个动态显示所有促销信息的标签栏

    $smarty->assign('invoice_list',    index_get_invoice_query());  // 发货查询
    $smarty->assign('new_articles',    index_get_new_articles());   // 最新文章
	$smarty->assign('class_articles_33', index_get_class_articles(33,3)); // 分类调用文章 
    $smarty->assign('group_buy_goods', index_get_group_buy());      // 团购商品
    $smarty->assign('auction_list',    index_get_auction());        // 拍卖活动
	$smarty->assign('cat_id344_best_goods', index_get_cat_id_goods_best_list(344,10));
	$smarty->assign('cat_id386_best_goods', index_get_cat_id_goods_best_list(386,10));
	$smarty->assign('cat_id341_best_goods', index_get_cat_id_goods_best_list(341,10));
	$smarty->assign('cat_id342_best_goods', index_get_cat_id_goods_best_list(342,10));
	$smarty->assign('cat_id421_best_goods', index_get_cat_id_goods_best_list(421,10));
	$smarty->assign('cat_id548_best_goods', index_get_cat_id_goods_best_list(548,10));
	$smarty->assign('cat_id355_best_goods', index_get_cat_id_goods_best_list(355,10));
	$smarty->assign('cat_id351_best_goods', index_get_cat_id_goods_best_list(351,10));
	$smarty->assign('cat_id349_best_goods', index_get_cat_id_goods_best_list(349,10));
	$smarty->assign('cat_id347_best_goods', index_get_cat_id_goods_best_list(347,10));
	$smarty->assign('cat_id570_best_goods', index_get_cat_id_goods_best_list(570,10));
	$smarty->assign('cat_id357_best_goods', index_get_cat_id_goods_best_list(357,10));
	$smarty->assign('cat_id590_best_goods', index_get_cat_id_goods_best_list(590,10));
    $smarty->assign('shop_notice',     $_CFG['shop_notice']);       // 商店公告
	
 $smarty->assign('wap_index_ad',get_wap_advlist('wap首页幻灯广告', 5));  //wap首页幻灯广告位

	 

	 $smarty->assign('wap_index_ad1',get_wap_advlist('wap首页分类广告1', 3));  //wap首页分类广告位

	 $smarty->assign('wap_index_ad2',get_wap_advlist('wap首页分类广告2', 3));

	 $smarty->assign('wap_index_ad3',get_wap_advlist('wap首页分类广告3', 3));	 

	 $smarty->assign('wap_index_ad4',get_wap_advlist('wap首页分类广告4', 3));  //wap首页分类广告位

	 $smarty->assign('wap_index_ad5',get_wap_advlist('wap首页分类广告5', 3));

	 $smarty->assign('wap_index_ad6',get_wap_advlist('wap首页分类广告6', 3));	 

	 $smarty->assign('wap_index_ad7',get_wap_advlist('wap首页分类广告7', 3));	

	 $smarty->assign('wap_index_ad_btm',get_wap_advlist('wap首页底部告位', 2));//wap首页底部告位

	/* $smarty->assign('wap_tree1', get_wap_parent_id_tree(332));     //wap首页分类广告下子分类

     $smarty->assign('wap_tree2', get_wap_parent_id_tree(333));

     $smarty->assign('wap_tree3', get_wap_parent_id_tree(336));
	 
	 $smarty->assign('wap_tree4', get_wap_parent_id_tree(335));

     $smarty->assign('wap_tree5', get_wap_parent_id_tree(334));
	 
	 $smarty->assign('wap_tree6', get_wap_parent_id_tree(337));

     $smarty->assign('wap_tree7', get_wap_parent_id_tree(339));*/

	
    /* 首页主广告设置 */
    $smarty->assign('index_ad',     $_CFG['index_ad']);
    if ($_CFG['index_ad'] == 'cus')
    {
        $sql = 'SELECT ad_type, content, url FROM ' . $ecs->table("ad_custom") . ' WHERE ad_status = 1';
        $ad = $db->getRow($sql, true);
        $smarty->assign('ad', $ad);
    }

    /* links */
    $links = index_get_links();
    $smarty->assign('img_links',       $links['img']);
    $smarty->assign('txt_links',       $links['txt']);
    $smarty->assign('data_dir',        DATA_DIR);       // 数据目录
	
 
$smarty->assign("flash",get_flash_xml());
$smarty->assign('flash_count',count(get_flash_xml()));




    /* 首页推荐分类 */
    $cat_recommend_res = $db->getAll("SELECT c.cat_id, c.cat_name, cr.recommend_type FROM " . $ecs->table("cat_recommend") . " AS cr INNER JOIN " . $ecs->table("category") . " AS c ON cr.cat_id=c.cat_id");
    if (!empty($cat_recommend_res))
    {
        $cat_rec_array = array();
        foreach($cat_recommend_res as $cat_recommend_data)
        {
            $cat_rec[$cat_recommend_data['recommend_type']][] = array('cat_id' => $cat_recommend_data['cat_id'], 'cat_name' => $cat_recommend_data['cat_name']);
        }
        $smarty->assign('cat_rec', $cat_rec);
    }

    /* 页面中的动态内容 */
    assign_dynamic('index');
    
    //猜你喜欢    
    $may_like_goods = com_sale_get_may_like_goods();
    $smarty->assign('may_like_goods',$may_like_goods);
    
    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
    		"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
    		'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ,g.original_img ' .
    		'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
    		'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
    		"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
    		"WHERE $where $ext ORDER BY $sort $order";
    
    $sql = 'select goods_id, goods_name, shop_price, market_price, wxbuy_cut, goods_thumb from '.$GLOBALS['ecs']->table('goods').
    ' where wxbuy_cut>0 order by goods_id desc limit 3';
    
    $wxbuy_three_goods = $db->getAll($sql);
    //    echo "<pre>";
    //    var_dump($wxbuy_three_goods);die();
    
    include "phpqrcode/qrlib.php";
    
    foreach($wxbuy_three_goods as $key=>$goods){
    	$data = 'http://'.$_SERVER['HTTP_HOST'].'/weixin_add_to_cart.php?goods_id='.$goods['goods_id'];
    	$errorCorrectionLevel = 'L';
    	$matrixPointSize = 4;
    	$path = "qrcode/";
    	if(!file_exists($path)){
    		mkdir($path);
    	}
    	$filename = $path.$errorCorrectionLevel.'-'.$matrixPointSize.'-'.md5('wxbuy'.$goods['goods_id']).'.png';
    	if(!file_exists($filename) || 1){
    		QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 3);
    	}
    	$wxbuy_three_goods[$key]['index_wxbuy_qrcode'] = $filename;
    	$wxbuy_three_goods[$key]['url'] = build_uri('goods', array('gid'=>$goods['goods_id']));
    	$wxbuy_three_goods[$key]['wx_price'] = number_format(floatval($goods['shop_price'])-floatval($goods['wxbuy_cut']),2);
    }
    $smarty->assign('wxbuy_three_goods',$wxbuy_three_goods);
    //		echo "<pre>";
    //		var_dump($wxbuy_three_goods);die();
    
    
    $latest_blog_rows = unserialize(file_get_contents('http://127.0.0.1/muying/latest_blogs.php'));
    $latest_blogs = array();
    foreach($latest_blog_rows as $row)
    {
    	$row['log_PostTime'] = date('m-d', $row['log_PostTime']);
    	$latest_blogs[] = $row;
    }
    
    $smarty->assign('latest_blogs',$latest_blogs);
    
    //底部导航 2015-10-03
    include_once ('includes/extend/cls_article.php');
    $cls_article = new cls_article();
    $nav_bottom_article = $cls_article->get_article(154);
    $smarty->assign('nav_bottom',$nav_bottom_article);
    
}




$smarty->display('index.dwt', $cache_id);

/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */

/**
 * 调用发货单查询
 *
 * @access  private
 * @return  array
 */
function index_get_invoice_query()
{
    $sql = 'SELECT o.order_sn, o.invoice_no, s.shipping_code FROM ' . $GLOBALS['ecs']->table('order_info') . ' AS o' .
            ' LEFT JOIN ' . $GLOBALS['ecs']->table('shipping') . ' AS s ON s.shipping_id = o.shipping_id' .
            " WHERE invoice_no > '' AND shipping_status = " . SS_SHIPPED .
            ' ORDER BY shipping_time DESC LIMIT 10';
    $all = $GLOBALS['db']->getAll($sql);

    foreach ($all AS $key => $row)
    {
        $plugin = ROOT_PATH . 'includes/modules/shipping/' . $row['shipping_code'] . '.php';

        if (file_exists($plugin))
        {
            include_once($plugin);

            $shipping = new $row['shipping_code'];
            $all[$key]['invoice_no'] = $shipping->query((string)$row['invoice_no']);
        }
    }

    clearstatcache();

    return $all;
}

/**
 * 获得最新的文章列表。
 *
 * @access  private
 * @return  array
 */
function index_get_new_articles()
{
    $sql = 'SELECT a.article_id, a.title, ac.cat_name, a.add_time, a.file_url, a.open_type, ac.cat_id, ac.cat_name ' .
            ' FROM ' . $GLOBALS['ecs']->table('article') . ' AS a, ' .
                $GLOBALS['ecs']->table('article_cat') . ' AS ac' .
            ' WHERE a.is_open = 1 AND a.cat_id = ac.cat_id AND ac.cat_type = 1' .
            ' ORDER BY a.article_type DESC, a.add_time DESC LIMIT ' . $GLOBALS['_CFG']['article_number'];
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $arr[$idx]['id']          = $row['article_id'];
        $arr[$idx]['title']       = $row['title'];
        $arr[$idx]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ?
                                        sub_str($row['title'], $GLOBALS['_CFG']['article_title_length']) : $row['title'];
        $arr[$idx]['cat_name']    = $row['cat_name'];
        $arr[$idx]['add_time']    = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);
        $arr[$idx]['url']         = $row['open_type'] != 1 ?
                                        build_uri('article', array('aid' => $row['article_id']), $row['title']) : trim($row['file_url']);
        $arr[$idx]['cat_url']     = build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']);
    }

    return $arr;
}

/**
 * 获得最新的团购活动
 *
 * @access  private
 * @return  array
 */
function index_get_group_buy()
{
    $time = gmtime();
    $limit = get_library_number('group_buy', 'index');

    $group_buy_list = array();
    if ($limit > 0)
    {
        $sql = 'SELECT gb.act_id AS group_buy_id, gb.goods_id, gb.ext_info, gb.goods_name, g.goods_thumb, g.goods_img ' .
                'FROM ' . $GLOBALS['ecs']->table('goods_activity') . ' AS gb, ' .
                    $GLOBALS['ecs']->table('goods') . ' AS g ' .
                "WHERE gb.act_type = '" . GAT_GROUP_BUY . "' " .
                "AND g.goods_id = gb.goods_id " .
                "AND gb.start_time <= '" . $time . "' " .
                "AND gb.end_time >= '" . $time . "' " .
                "AND g.is_delete = 0 " .
                "ORDER BY gb.act_id DESC " .
                "LIMIT $limit" ;
        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            /* 如果缩略图为空，使用默认图片 */
            $row['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
            $row['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);

            /* 根据价格阶梯，计算最低价 */
            $ext_info = unserialize($row['ext_info']);
            $price_ladder = $ext_info['price_ladder'];
            if (!is_array($price_ladder) || empty($price_ladder))
            {
                $row['last_price'] = price_format(0);
            }
            else
            {
                foreach ($price_ladder AS $amount_price)
                {
                    $price_ladder[$amount_price['amount']] = $amount_price['price'];
                }
            }
            ksort($price_ladder);
            $row['last_price'] = price_format(end($price_ladder));
            $row['url'] = build_uri('group_buy', array('gbid' => $row['group_buy_id']));
            $row['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                           sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $row['short_style_name']   = add_style($row['short_name'],'');
            $group_buy_list[] = $row;
        }
    }

    return $group_buy_list;
}

/**
 * 取得拍卖活动列表
 * @return  array
 */
function index_get_auction()
{
    $now = gmtime();
    $limit = get_library_number('auction', 'index');
    $sql = "SELECT a.act_id, a.goods_id, a.goods_name, a.ext_info, g.goods_thumb ".
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS a," .
                      $GLOBALS['ecs']->table('goods') . " AS g" .
            " WHERE a.goods_id = g.goods_id" .
            " AND a.act_type = '" . GAT_AUCTION . "'" .
            " AND a.is_finished = 0" .
            " AND a.start_time <= '$now'" .
            " AND a.end_time >= '$now'" .
            " AND g.is_delete = 0" .
            " ORDER BY a.start_time DESC" .
            " LIMIT $limit";
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $ext_info = unserialize($row['ext_info']);
        $arr = array_merge($row, $ext_info);
        $arr['formated_start_price'] = price_format($arr['start_price']);
        $arr['formated_end_price'] = price_format($arr['end_price']);
        $arr['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr['url'] = build_uri('auction', array('auid' => $arr['act_id']));
        $arr['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                           sub_str($arr['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $arr['goods_name'];
        $arr['short_style_name']   = add_style($arr['short_name'],'');
        $list[] = $arr;
    }

    return $list;
}

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

function get_flash_xml()
{
    $flashdb = array();
    if (file_exists(ROOT_PATH . DATA_DIR . '/flash_data.xml'))
    {

        // 兼容v2.7.0及以前版本
        if (!preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"\ssort="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER))
        {
            preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER);
        }

        if (!empty($t))
        {
            foreach ($t as $key => $val)
            {
                $val[4] = isset($val[4]) ? $val[4] : 0;
                $flashdb[] = array('src'=>$val[1],'url'=>$val[2],'text'=>$val[3],'sort'=>$val[4]);
				
				//print_r($flashdb);
            }
        }
    }
    return $flashdb;
}
function get_hot_cat_goods($type = '',$cat_id, $num = 7)
{
	$children = get_children($cat_id);
	$sql = 'SELECT g.goods_id,g.cat_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .

	"IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, ".

	'g.promote_price, promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img ' .

	"FROM " . $GLOBALS['ecs']->table('goods') . ' AS g '.

	"LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".

	"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".

	'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND  g.is_delete = 0' ;



	switch ($type)

	{

		case 'best':

			$sql .= ' AND is_best = 1';

			break;

		case 'new':

			$sql .= ' AND is_new = 1';

			break;

		case 'hot':

			$sql .= ' AND is_hot = 1';

			break;

		case 'promote':

			$time = gmtime();

			$sql .= " AND is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time'";

			break;

	}



	$sql.=' AND (' . $children . 'OR ' . get_extension_goods($children) . ') ' .'ORDER BY g.sort_order, g.goods_id DESC';







	if ($num > 0)

	{

		$sql .= ' LIMIT ' . $num;

	}


/* 记录浏览历史 */
if (!empty($_COOKIE['ECS']['history']))
{
    $history = explode(',', $_COOKIE['ECS']['history']);

    array_unshift($history, $goods_id);
    $history = array_unique($history);

    while (count($history) > $_CFG['history_number'])
    {
        array_pop($history);
    }

    setcookie('ECS[history]', implode(',', $history), gmtime() + 3600 * 24 * 30);
}
else
{
    setcookie('ECS[history]', $goods_id, gmtime() + 3600 * 24 * 30);
}


/* 更新点击次数 */
$db->query('UPDATE ' . $ecs->table('goods') . " SET click_count = click_count + 1 WHERE goods_id = '$_REQUEST[id]'");

$smarty->assign('now_time',  gmtime());           // 当前系统时间
$smarty->display('index.dwt',      $cache_id);
	//echo $sql;



	$res = $GLOBALS['db']->getAll($sql);



	$goods = array();

	foreach ($res AS $idx => $row)

	{

		if ($row['promote_price'] > 0)

		{

			$promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);

			$goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';

		}

		else

		{

			$goods[$idx]['promote_price'] = '';

		}



		$temp=$row['cat_id'];

		$cat_info=get_hot_cat_info($temp);



		$goods[$idx]['id']           = $row['goods_id'];

		$goods[$idx]['cat_id']       = $row['cat_id'];

		$goods[$idx]['cat_name']     = $cat_info['name'];

		$goods[$idx]['cat_url']     = $cat_info['url'];





		$goods[$idx]['name']         = $row['goods_name'];

		$goods[$idx]['brief']        = $row['goods_brief'];

		$goods[$idx]['market_price'] = price_format($row['market_price']);

		$goods[$idx]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?

		sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];

		$goods[$idx]['shop_price']   = price_format($row['shop_price']);

		$goods[$idx]['thumb']        = empty($row['goods_thumb']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_thumb'];

		$goods[$idx]['goods_img']    = empty($row['goods_img']) ? $GLOBALS['_CFG']['no_picture'] : $row['goods_img'];

		$goods[$idx]['url']          = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

	}



	return $goods;

}



function get_hot_cat_info($cat_id)

{

	/* 分类信息 */

	$sql = 'SELECT cat_name FROM ' . $GLOBALS['ecs']->table('category') . " WHERE cat_id = '$cat_id'";

	$cat['name'] = $GLOBALS['db']->getOne($sql);

	$cat['url']  = build_uri('category', array('cid' => $cat_id), $cat['name']);

	$cat['id']   = $cat_id;



	return $cat;

}









//LSJ

/**

 * ============================================================================

 * 文章自定义数据调用函数

 * ============================================================================

*/

//取得文章里面的图片

function GetImageSrc($body) {

   if( !isset($body) ) {

   		return '';

   }

   else {

    	preg_match_all ("/<(img|IMG)(.*)(src|SRC)=[\"|'|]{0,}([h|\/].*(jpg|JPG|gif|GIF|png|PNG))[\"|'|\s]{0,}/isU",$body,$out);

		return $out[4];

   }

}



//提取文里面的URL

function GetArticleUrl($body) {

	if( !isset($body) ) {

		return '';

	}

	else {

		preg_match_all("/<(a|A)(.*)(href|HREF)=[\"|'|](.*)[\"|'|\s]{0,}>(.*)<\/(a|A)>/isU",$body,$out);

		return $out;

	}

}



function get_article_children_new ($cat = 0)

{

    return db_create_in(array_unique(array_merge(array($cat), array_keys(article_cat_list($cat, 0, false)))), 'a.cat_id');

}




/**

* 按文章ID号/分类ID/商品ID号/商品分类ID号取得文章

* @param  array    $id       文章ID或文章分类ID

* @param  string   $getwhat  以何种方式取文章其中可选参数有:

								[1]art_cat(以文章分类ID获取)    [2]art_id(以文章ID获取)

								[3]goods_cat(以商品分类ID获取)  [4]goods_id(以商品ID获取)

								其中的[3]和[4]必须有商品关联文章或文章关联商品

* @param  integer  $num      控制显示多少条文章.当参数为0时则全部显示

* @param  integer  $start    从第几条数据开始取

* @param  boolean  $isrand   是否随机显示文章(默认为不显示)

* @param  boolean  $showall   是否显示隐藏的文章(黑认为不显示隐藏文章)

* @return array

*/

function get_article_new( $id = array(0), $getwhat = 'art_id', $num = 0, $isrand = false, $showall = true, $start = 0 ) {

	$sql = '';

	$findkey = '';

	$search = '';

	$wherestr = '';

	

	for( $i=0; $i<count($id); $i++ ) {

		if( $i<count($id)-1 ) {

			$findkey .= $id[$i] .',';

		}

		else {

			$findkey .= $id[$i];

		}

	}

	

	if( $getwhat == 'art_cat' ){

		for( $i=0; $i<count($id); $i++ ) {

			if( $i<count($id)-1 ) {

				$search .= get_article_children_new($id[$i]) . ' OR ';

			}

			else {

				$search .= get_article_children_new($id[$i]);

			}

		}

	}

	elseif($getwhat == 'goods_cat') {

		for( $i=0; $i<count($id); $i++) {

			if( $i<count($id)-1 ) {

				$search .= get_children($id[$i]) . ' OR ';

			}

			else {

				$search .= get_children($id[$i]);

			}

		}

	}

	elseif( $getwhat == 'art_id' ) {

		$search = 'ac.cat_id IN' . '(' . $findkey . ')';

	}

	elseif( $getwhat == 'goods_id' ) {

		$search = 'g.goods_id IN' . '(' . $findkey . ')';

	}

	$wherestr = '(' . $search . ')';

	

	if( $getwhat == 'art_cat' || $getwhat == 'art_id' ) {

		$sql = 'SELECT a.*,ac.cat_id,ac.cat_name,ac.keywords as cat_keywords,ac.cat_desc 

		FROM ' . $GLOBALS['ecs']->table('article') . ' AS a left join ' .

		 $GLOBALS['ecs']->table('article_cat') . ' AS ac on a.cat_id = ac.cat_id  ' .

		' WHERE ' . $wherestr;

	}

	elseif( $getwhat == 'goods_cat' || $getwhat == 'goods_id' ) {

		$sql = 'SELECT DISTINCT a.*,ac.cat_id,ac.cat_name,ac.keywords as cat_keywords,ac.cat_desc FROM ' . 

		$GLOBALS['ecs']->table('goods') .' AS g ' .

		'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_article') . ' AS ga ON g.goods_id=ga.goods_id INNER JOIN ' . 

		$GLOBALS['ecs']->table('article') . ' AS a ON ga.article_id = a.article_id, ' .
		$GLOBALS['ecs']->table('article_cat') . 'AS ac ' .

		'WHERE (a.cat_id = ac.cat_id) AND ' . $wherestr;	

	}

	

	

	if( ($showall == false) && ( $getwhat == 'art_cat' || $getwhat == 'art_id' ) ) {

		$sql .= ' AND a.is_open=1';

	}

	elseif( ($showall == false) && ( $getwhat == 'goods_cat' || $getwhat == 'goods_id' ) ) {

		$sql .= ' AND a.is_open=1';

	}

	

	if( $isrand == true ) {

		$sql .= ' ORDER BY rand()';

	}

	elseif( ($isrand == false) && ( $getwhat == 'art_cat' || $getwhat == 'art_id' ) ) {

		$sql .= ' ORDER BY a.add_time DESC, a.article_id DESC';

	}

	elseif( ($isrand == false) && ( $getwhat == 'goods_cat' || $getwhat == 'goods_id' ) ) {

		$sql .= ' ORDER BY a.add_time DESC, a.article_id DESC';

	}

	

	if( $start == 0 && $num>0 ) {

		$sql .= ' LIMIT ' . $num;

	}

	elseif( $start>0 && $num>0 ) {

		$sql .= ' LIMIT ' . $start . ',' . $num;

	}

	

	//开始查询
 
	$arr = $GLOBALS['db']->getAll($sql);

	$articles = array();

	foreach ($arr AS $id => $row) {

		$articles[$id]['cat_id']       = $row['cat_id'];

		$articles[$id]['cat_name']     = $row['cat_name'];

		$articles[$id]['cat_url']      = build_uri('article_cat', array('acid' => $row['cat_id']));

		$articles[$id]['cat_keywords'] = $row['cat_keywords'];

		$articles[$id]['cat_desc']     = $row['cat_desc'];

		$articles[$id]['title']        = $row['title'];

		$articles[$id]['url']          = build_uri('article', array('aid'=>$row['article_id']), $row['title']);

		$articles[$id]['author']       = $row['author'];

		$articles[$id]['content']      = $row['content'];

		$articles[$id]['keywords']     = $row['keywords'];

		$articles[$id]['description']     = $row['description'];

		$articles[$id]['file_url']     = $row['file_url'];

		$articles[$id]['link']         = $row['link'];

		$articles[$id]['addtime']      = date($GLOBALS['_CFG']['date_format'], $row['add_time']);

		$articles[$id]['content']      = $row['content'];

		$imgsrc                        = GetImageSrc($row['content']);

		$articles[$id]['img']          = $imgsrc;                         //提取文章中所有的图片	

		$link                          = GetArticleUrl($row['content']);

		$articles[$id]['link_url']     = $link[4];                        //提取文章中所有的链接地址

		$articles[$id]['link_title']   = $link[5];                        //提取文章中所有的链接名称

	}
//var_dump($articles);
	return $articles;

}
function get_is_computer(){

$is_computer=$_REQUEST['is_computer'];

return $is_computer;

}
function get_wap_advlist( $position, $num )

{

		$arr = array( );

		$sql = "select ap.ad_width,ap.ad_height,ad.ad_id,ad.ad_name,ad.ad_code,ad.ad_link,ad.ad_id from ".$GLOBALS['ecs']->table( "ad_position" )." as ap left join ".$GLOBALS['ecs']->table( "ad" )." as ad on ad.position_id = ap.position_id where ap.position_name='".$position.( "' and UNIX_TIMESTAMP()>ad.start_time and UNIX_TIMESTAMP()<ad.end_time and ad.enabled=1 limit ".$num );

		$res = $GLOBALS['db']->getAll( $sql );

		foreach ( $res as $idx => $row )

		{

				$arr[$row['ad_id']]['name'] = $row['ad_name'];

				$arr[$row['ad_id']]['url'] = "affiche.php?ad_id=".$row['ad_id']."&uri=".$row['ad_link'];

				$arr[$row['ad_id']]['image'] = "data/afficheimg/".$row['ad_code'];

				$arr[$row['ad_id']]['content'] = "<a href='".$arr[$row['ad_id']]['url']."' target='_blank'><img src='data/afficheimg/".$row['ad_code']."' width='".$row['ad_width']."' height='".$row['ad_height']."' /></a>";

				$arr[$row['ad_id']]['ad_code'] = $row['ad_code'];

		}

		return $arr;

}
/*
function  tui_product($id,$num){
	$dataArray=array();
	$children = get_child($id);
	$i=0;
	foreach($children as $value){
	$dataArray[$i]=index_get_cat_id_goods_best_list($value['cat_id'],$num); 
		$i++;
		}
	//$arr=index_get_cat_id_goods_best_list(344,$num);
	$data=array('content'=>$dataArray, 'pkname'=>$children);
	return  $data;
	
	}
	*/
	function get_ecs_category_ids( $id )
{
    $ids = "";
    $sql = "SELECT cat_id, cat_name FROM ".$GLOBALS['ecs']->table('category')." WHERE FIND_IN_SET( cat_id, getChildDeptLst( ".$id." ) ) ";
	
	//var_dump($sql);
    $res = $GLOBALS['db']->getAll( $sql );
    
    $i = 0;
    foreach ( $res as $idx => $row )
		{
        if ($i == 0)
          $ids = $ids . $row['cat_id'];
        else
          $ids = $ids . "," . $row['cat_id'];
        $i = $i+1;
    }
    
    return $ids;
}
function get_news(){
	
	
	
	}

function index_get_comments()
{
$sql = 'SELECT id_value, user_name, content, add_time FROM ' . $GLOBALS['ecs']->table('comment') . ' WHERE comment_rank = 6 AND status = 1 ORDER BY comment_id DESC LIMIT 6';
$res = $GLOBALS['db']->getAll($sql);
$pvnewcomments = array();
foreach ($res AS $row)
{
$pvnewcomments[] = array('id_value' => $row['id_value'],
'user_name'  => $row['user_name'],
'content'  => $row['content'],
'add_time' => date("Y-m-d H:i:s", $row['add_time']));
}
return $pvnewcomments;
}

function get_hot_comment($count=1 )
{
		$arr = array( );
		$sql = "SELECT c.*, g.goods_id, g.goods_thumb, g.goods_name FROM ".
		$GLOBALS['ecs']->table( "comment" )." AS c  LEFT JOIN ".
		$GLOBALS['ecs']->table( "goods" )." AS g ON c.id_value = g.goods_id WHERE c.status=1 and g.is_on_sale=1 and g.is_delete=0 order by c.add_time desc limit ".$count;
		$res = $GLOBALS['db']->getAll( $sql );
		foreach ( $res as $idx => $row )
		{
				$arr[$idx]['id_value'] = $row['id_value'];
				$arr[$idx]['user_name'] = empty($row['user_name'])?'':substr_cut($row['user_name']);
				$arr[$idx]['content'] = $row['content'];
				$arr[$idx]['comment_rank'] = $row['comment_rank'];
				$arr[$idx]['time'] = local_date( "m-d", $row['add_time'] );
				$arr[$idx]['goods_id'] = $row['goods_id'];
				$arr[$idx]['goods_name'] = $row['goods_name'];
				$arr[$idx]['goods_thumb'] = get_image_path( $row['goods_id'], $row['goods_thumb'], TRUE );
				$arr[$idx]['url'] = build_uri( "goods", array(
						"gid" => $row['goods_id']
				), $row['goods_name'] );
		}
		return $arr;
}



?>