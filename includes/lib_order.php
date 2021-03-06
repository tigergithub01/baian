<?php

/**
 * ECSHOP 购物流程函数库
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
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
 * 处理序列化的支付、配送的配置参数
 * 返回一个以name为索引的数组
 *
 * @access  public
 * @param   string       $cfg
 * @return  void
 */
function unserialize_config($cfg)
{
    if (is_string($cfg) && ($arr = unserialize($cfg)) !== false)
    {
        $config = array();

        foreach ($arr AS $key => $val)
        {
            $config[$val['name']] = $val['value'];
        }

        return $config;
    }
    else
    {
        return false;
    }
}
/**
 * 取得已安装的配送方式
 * @return  array   已安装的配送方式
 */
function shipping_list()
{
    $sql = 'SELECT shipping_id, shipping_name ' .
            'FROM ' . $GLOBALS['ecs']->table('shipping') .
            ' WHERE enabled = 1';

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得配送方式信息
 * @param   int     $shipping_id    配送方式id
 * @return  array   配送方式信息
 */
function shipping_info($shipping_id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('shipping') .
            " WHERE shipping_id = '$shipping_id' " .
            'AND enabled = 1';

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 取得可用的配送方式列表
 * @param   array   $region_id_list     收货人地区id数组（包括国家、省、市、区）
 * @return  array   配送方式数组
 */
function available_shipping_list($region_id_list)
{
    $sql = 'SELECT s.shipping_id, s.shipping_code, s.shipping_name, ' .
                's.shipping_desc, s.insure, s.support_cod, a.configure ' .
            'FROM ' . $GLOBALS['ecs']->table('shipping') . ' AS s, ' .
                $GLOBALS['ecs']->table('shipping_area') . ' AS a, ' .
                $GLOBALS['ecs']->table('area_region') . ' AS r ' .
            'WHERE r.region_id ' . db_create_in($region_id_list) .
            ' AND r.shipping_area_id = a.shipping_area_id AND a.shipping_id = s.shipping_id AND s.enabled = 1 ORDER BY s.shipping_order';

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得某配送方式对应于某收货地址的区域信息
 * @param   int     $shipping_id        配送方式id
 * @param   array   $region_id_list     收货人地区id数组
 * @return  array   配送区域信息（config 对应着反序列化的 configure）
 */
function shipping_area_info($shipping_id, $region_id_list)
{
    $sql = 'SELECT s.shipping_code, s.shipping_name, ' .
                's.shipping_desc, s.insure, s.support_cod, a.configure ' .
            'FROM ' . $GLOBALS['ecs']->table('shipping') . ' AS s, ' .
                $GLOBALS['ecs']->table('shipping_area') . ' AS a, ' .
                $GLOBALS['ecs']->table('area_region') . ' AS r ' .
            "WHERE s.shipping_id = '$shipping_id' " .
            'AND r.region_id ' . db_create_in($region_id_list) .
            ' AND r.shipping_area_id = a.shipping_area_id AND a.shipping_id = s.shipping_id AND s.enabled = 1';
    $row = $GLOBALS['db']->getRow($sql);

    if (!empty($row))
    {
        $shipping_config = unserialize_config($row['configure']);
        if (isset($shipping_config['pay_fee']))
        {
            if (strpos($shipping_config['pay_fee'], '%') !== false)
            {
                $row['pay_fee'] = floatval($shipping_config['pay_fee']) . '%';
            }
            else
            {
                 $row['pay_fee'] = floatval($shipping_config['pay_fee']);
            }
        }
        else
        {
            $row['pay_fee'] = 0.00;
        }
    }

    return $row;
}

/**
 * 计算运费
 * @param   string  $shipping_code      配送方式代码
 * @param   mix     $shipping_config    配送方式配置信息
 * @param   float   $goods_weight       商品重量
 * @param   float   $goods_amount       商品金额
 * @param   float   $goods_number       商品数量
 * @return  float   运费
 */
function shipping_fee($shipping_code, $shipping_config, $goods_weight, $goods_amount, $goods_number='')
{
    if (!is_array($shipping_config))
    {
        $shipping_config = unserialize($shipping_config);
    }

    $filename = ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php';
    if (file_exists($filename))
    {
        include_once($filename);

        $obj = new $shipping_code($shipping_config);

        return $obj->calculate($goods_weight, $goods_amount, $goods_number);
    }
    else
    {
        return 0;
    }
}

/**
 * 获取指定配送的保价费用
 *
 * @access  public
 * @param   string      $shipping_code  配送方式的code
 * @param   float       $goods_amount   保价金额
 * @param   mix         $insure         保价比例
 * @return  float
 */
function shipping_insure_fee($shipping_code, $goods_amount, $insure)
{
    if (strpos($insure, '%') === false)
    {
        /* 如果保价费用不是百分比则直接返回该数值 */
        return floatval($insure);
    }
    else
    {
        $path = ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php';

        if (file_exists($path))
        {
            include_once($path);

            $shipping = new $shipping_code;
            $insure   = floatval($insure) / 100;

            if (method_exists($shipping, 'calculate_insure'))
            {
                return $shipping->calculate_insure($goods_amount, $insure);
            }
            else
            {
                return ceil($goods_amount * $insure);
            }
        }
        else
        {
            return false;
        }
    }
}

/**
 * 取得已安装的支付方式列表
 * @return  array   已安装的配送方式列表
 */
function payment_list()
{
    $sql = 'SELECT pay_id, pay_name ' .
            'FROM ' . $GLOBALS['ecs']->table('payment') .
            ' WHERE enabled = 1';

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得支付方式信息
 * @param   int     $pay_id     支付方式id
 * @return  array   支付方式信息
 */
function payment_info($pay_id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('payment') .
            " WHERE pay_id = '$pay_id' AND enabled = 1";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 获得订单需要支付的支付费用
 *
 * @access  public
 * @param   integer $payment_id
 * @param   float   $order_amount
 * @param   mix     $cod_fee
 * @return  float
 */
function pay_fee($payment_id, $order_amount, $cod_fee=null)
{
    $pay_fee = 0;
    $payment = payment_info($payment_id);
    $rate    = ($payment['is_cod'] && !is_null($cod_fee)) ? $cod_fee : $payment['pay_fee'];

    if (strpos($rate, '%') !== false)
    {
        /* 支付费用是一个比例 */
        $val     = floatval($rate) / 100;
        $pay_fee = $val > 0 ? $order_amount * $val /(1- $val) : 0;
    }
    else
    {
        $pay_fee = floatval($rate);
    }

    return round($pay_fee, 2);
}

/**
 * 取得可用的支付方式列表
 * @param   bool    $support_cod        配送方式是否支持货到付款
 * @param   int     $cod_fee            货到付款手续费（当配送方式支持货到付款时才传此参数）
 * @param   int     $is_online          是否支持在线支付
 * @param   array   $region_id_list     收货人地区id数组
 * @return  array   配送方式数组
 */
function available_payment_list($support_cod, $cod_fee = 0, $is_online = false,$region_id_list=null)
{
    $sql = 'SELECT pay_id, pay_code, pay_name, pay_fee, pay_desc, pay_config, is_cod, cod_area' .
            ' FROM ' . $GLOBALS['ecs']->table('payment') .
            ' WHERE enabled = 1 ';
    if (!$support_cod)
    {
        $sql .= 'AND is_cod = 0 '; // 如果不支持货到付款
    }
    if ($is_online)
    {
        $sql .= "AND is_online = '1' ";
    }
    $sql .= 'ORDER BY pay_order'; // 排序
    $res = $GLOBALS['db']->query($sql);

    $pay_list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['is_cod'] == '1')
        {
            //判断货到付款支付的区域：
			$support_cod_area = false;
        	if(!empty($row['cod_area']) && $region_id_list){
        		$cod_area_ids = explode(',', $row['cod_area']);
        		foreach ($region_id_list as $key => $value) {
        			if(in_array($value, $cod_area_ids)){
        				$support_cod_area = true;
        				break;
        			}
        		}
        	} 
        	if(!$support_cod_area){
        		continue;
        	}   
        	     	
        	$row['pay_fee'] = $cod_fee;
        }

        $row['format_pay_fee'] = strpos($row['pay_fee'], '%') !== false ? $row['pay_fee'] :
        price_format($row['pay_fee'], false);
        
       //微信支付只有在微信浏览器中加载
        $ag1  = strstr($_SERVER['HTTP_USER_AGENT'],"MicroMessenger");
        if($row['pay_code']=='wxpay' && empty($ag1)){
        	//do nothing
        }else if($row['pay_code']=='alipay' && !empty($ag1)){
        	//支付宝在微信浏览器中不显示
        	//do nothing        	
        }else{
        	 $modules[] = $row;
        }
    }

    include_once(ROOT_PATH.'includes/lib_compositor.php');

    if(isset($modules))
    {
        return $modules;
    }
}

/**
 * 取得包装列表
 * @return  array   包装列表
 */
function pack_list()
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('pack');
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['format_pack_fee'] = price_format($row['pack_fee'], false);
        $row['format_free_money'] = price_format($row['free_money'], false);
        $list[] = $row;
    }

    return $list;
}

/**
 * 取得包装信息
 * @param   int     $pack_id    包装id
 * @return  array   包装信息
 */
function pack_info($pack_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pack') .
            " WHERE pack_id = '$pack_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 根据订单中的商品总额来获得包装的费用
 *
 * @access  public
 * @param   integer $pack_id
 * @param   float   $goods_amount
 * @return  float
 */
function pack_fee($pack_id, $goods_amount)
{
    $pack = pack_info($pack_id);

    $val = (floatval($pack['free_money']) <= $goods_amount && $pack['free_money'] > 0) ? 0 : floatval($pack['pack_fee']);

    return $val;
}

/**
 * 取得贺卡列表
 * @return  array   贺卡列表
 */
function card_list()
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('card');
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['format_card_fee'] = price_format($row['card_fee'], false);
        $row['format_free_money'] = price_format($row['free_money'], false);
        $list[] = $row;
    }

    return $list;
}

/**
 * 取得贺卡信息
 * @param   int     $card_id    贺卡id
 * @return  array   贺卡信息
 */
function card_info($card_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('card') .
            " WHERE card_id = '$card_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 根据订单中商品总额获得需要支付的贺卡费用
 *
 * @access  public
 * @param   integer $card_id
 * @param   float   $goods_amount
 * @return  float
 */
function card_fee($card_id, $goods_amount)
{
    $card = card_info($card_id);

    return ($card['free_money'] <= $goods_amount && $card['free_money'] > 0) ? 0 : $card['card_fee'];
}

/**
 * 取得订单信息
 * @param   int     $order_id   订单id（如果order_id > 0 就按id查，否则按sn查）
 * @param   string  $order_sn   订单号
 * @return  array   订单信息（金额都有相应格式化的字段，前缀是formated_）
 */
function order_info($order_id, $order_sn = '')
{
    /* 计算订单各种费用之和的语句 */
    $total_fee = " (goods_amount - discount + tax + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee) AS total_fee ";
    $order_id = intval($order_id);
    if ($order_id > 0)
    {
        $sql = "SELECT *, " . $total_fee . " FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE order_id = '$order_id'";
    }
    else
    {
        $sql = "SELECT *, " . $total_fee . "  FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE order_sn = '$order_sn'";
    }
    $order = $GLOBALS['db']->getRow($sql);

    /* 格式化金额字段 */
    if ($order)
    {
        $order['formated_goods_amount']   = price_format($order['goods_amount'], false);
        $order['formated_discount']       = price_format($order['discount'], false);
        $order['formated_tax']            = price_format($order['tax'], false);
        $order['formated_shipping_fee']   = price_format($order['shipping_fee'], false);
        $order['formated_insure_fee']     = price_format($order['insure_fee'], false);
        $order['formated_pay_fee']        = price_format($order['pay_fee'], false);
        $order['formated_pack_fee']       = price_format($order['pack_fee'], false);
        $order['formated_card_fee']       = price_format($order['card_fee'], false);
        $order['formated_total_fee']      = price_format($order['total_fee'], false);
        $order['formated_money_paid']     = price_format($order['money_paid'], false);
        $order['formated_bonus']          = price_format($order['bonus'], false);
        $order['formated_integral_money'] = price_format($order['integral_money'], false);
        $order['formated_surplus']        = price_format($order['surplus'], false);
        $order['formated_order_amount']   = price_format(abs($order['order_amount']), false);
        $order['formated_add_time']       = local_date($GLOBALS['_CFG']['time_format'], $order['add_time']);
    }

    return $order;
}

/**
 * 判断订单是否已完成
 * @param   array   $order  订单信息
 * @return  bool
 */
function order_finished($order)
{
    return $order['order_status']  == OS_CONFIRMED &&
        ($order['shipping_status'] == SS_SHIPPED || $order['shipping_status'] == SS_RECEIVED) &&
        ($order['pay_status']      == PS_PAYED   || $order['pay_status'] == PS_PAYING);
}

/**
 * 取得订单商品
 * @param   int     $order_id   订单id
 * @return  array   订单商品数组
 */
function order_goods($order_id)
{
    $sql = "SELECT og.rec_id, og.goods_id, og.goods_name, og.goods_sn, og.market_price, og.goods_number, " .
            "og.goods_price, og.goods_attr, og.is_real, og.parent_id, og.is_gift, " .
            "og.goods_price * og.goods_number AS subtotal, og.extension_code, " .
            "g.goods_thumb , g.goods_img, og.product_id, IF(og.is_gift, og.is_gift, og.goods_id) AS gid " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') . ' AS og ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            "ON og.goods_id = g.goods_id " .
            " WHERE og.order_id = '$order_id'";
    $sql = $sql . " ORDER BY gid, og.is_gift";

    $res = $GLOBALS['db']->query($sql);
    $goods_list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['extension_code'] == 'package_buy')
        {
            $row['package_goods_list'] = get_package_goods($row['goods_id']);
            if($row['package_goods_list'] && count($row['package_goods_list']>0)){
            	$row['goods_thumb'] = $row['package_goods_list'][0]['goods_thumb'];
            }
            
        }
        
        $row['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $row['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
        $row['goods_url']              = build_uri('goods', array('gid'=>$row['goods_id'],'pid'=>$row['product_id']), $row['goods_name']);
//         $goods_list[$row['rec_id']] = $row;
		
        /*根据产品编号查询规格*/
        if ($row['product_id'] && intval($row['product_id'])>0 )
        {
        	$sql = "SELECT c.attr_name,b.attr_value from ".$GLOBALS['ecs']->table('products_attr')." AS a
					LEFT JOIN ".$GLOBALS['ecs']->table('goods_attr')." AS b ON (a.goods_attr_id = b.goods_attr_id)
					LEFT JOIN ".$GLOBALS['ecs']->table('attribute')." c ON (b.attr_id = c.attr_id)
					WHERE a.product_id = '".$row['product_id']."'";
        	$attr_list = $GLOBALS['db']->getAll($sql);
        	foreach ($attr_list AS $attr)
        	{
        		$row['goods_name'] .= (' [' .$attr['attr_name'].':'. $attr['attr_value'] . '] ');
        	}
        }
        
        $goods_list[] = $row;
    }
// 	var_dump($goods_list);
    //return $GLOBALS['db']->getAll($sql);
    return $goods_list;
}

/**
 * 取得订单总金额
 * @param   int     $order_id   订单id
 * @param   bool    $include_gift   是否包括赠品
 * @return  float   订单总金额
 */
function order_amount($order_id, $include_gift = true)
{
    $sql = "SELECT SUM(goods_price * goods_number) " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') .
            " WHERE order_id = '$order_id'";
    if (!$include_gift)
    {
        $sql .= " AND is_gift = 0";
    }

    return floatval($GLOBALS['db']->getOne($sql));
}

/**
 * 取得某订单商品总重量和总金额（对应 cart_weight_price）
 * @param   int     $order_id   订单id
 * @return  array   ('weight' => **, 'amount' => **, 'formated_weight' => **)
 */
function order_weight_price($order_id)
{
    $sql = "SELECT SUM(g.goods_weight * o.goods_number) AS weight, " .
                "SUM(o.goods_price * o.goods_number) AS amount ," .
                "SUM(o.goods_number) AS number " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') . " AS o, " .
                $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE o.order_id = '$order_id' " .
            "AND o.goods_id = g.goods_id";

    $row = $GLOBALS['db']->getRow($sql);
    $row['weight'] = floatval($row['weight']);
    $row['amount'] = floatval($row['amount']);
    $row['number'] = intval($row['number']);

    /* 格式化重量 */
    $row['formated_weight'] = formated_weight($row['weight']);

    return $row;
}

/**
 * 获得订单中的费用信息
 *
 * @access  public
 * @param   array   $order
 * @param   array   $goods
 * @param   array   $consignee
 * @param   bool    $is_gb_deposit  是否团购保证金（如果是，应付款金额只计算商品总额和支付费用，可以获得的积分取 $gift_integral）
 * @return  array
 */
function order_fee($order, $goods, $consignee)
{
    /* 初始化订单的扩展code */
    if (!isset($order['extension_code']))
    {
        $order['extension_code'] = '';
    }

    if ($order['extension_code'] == 'group_buy')
    {
        $group_buy = group_buy_info($order['extension_id']);
    }

    $total  = array('real_goods_count' => 0,
                    'gift_amount'      => 0,
                    'goods_price'      => 0,
                    'market_price'     => 0,
                    'discount'         => 0,
                    'pack_fee'         => 0,
                    'card_fee'         => 0,
                    'shipping_fee'     => 0,
                    'shipping_insure'  => 0,
                    'integral_money'   => 0,
                    'bonus'            => 0,
                    'surplus'          => 0,
                    'cod_fee'          => 0,
                    'pay_fee'          => 0,
                    'tax'              => 0);
    $weight = 0;

    /* 商品总价 */
    foreach ($goods AS $val)
    {
        /* 统计实体商品的个数 */
        if ($val['is_real'])
        {
            $total['real_goods_count']++;
        }

        $total['goods_price']  += $val['goods_price'] * $val['goods_number'];
        $total['market_price'] += $val['market_price'] * $val['goods_number'];
    }

    $total['saving']    = $total['market_price'] - $total['goods_price'];
    $total['save_rate'] = $total['market_price'] ? round($total['saving'] * 100 / $total['market_price']) . '%' : 0;

    $total['goods_price_formated']  = price_format($total['goods_price'], false);
    $total['market_price_formated'] = price_format($total['market_price'], false);
    $total['saving_formated']       = price_format($total['saving'], false);

    /* 折扣 */
    if ($order['extension_code'] != 'group_buy')
    {
        $discount = compute_discount();
        $total['discount'] = $discount['discount'];
        if ($total['discount'] > $total['goods_price'])
        {
            $total['discount'] = $total['goods_price'];
        }
    }
    $total['discount_formated'] = price_format($total['discount'], false);

    /* 税额 */
    if (!empty($order['need_inv']) && $order['inv_type'] != '')
    {
        /* 查税率 */
        $rate = 0;
        foreach ($GLOBALS['_CFG']['invoice_type']['type'] as $key => $type)
        {
            if ($type == $order['inv_type'])
            {
                $rate = floatval($GLOBALS['_CFG']['invoice_type']['rate'][$key]) / 100;
                break;
            }
        }
        if ($rate > 0)
        {
            $total['tax'] = $rate * $total['goods_price'];
        }
    }
    $total['tax_formated'] = price_format($total['tax'], false);

    /* 包装费用 */
    if (!empty($order['pack_id']))
    {
        $total['pack_fee']      = pack_fee($order['pack_id'], $total['goods_price']);
    }
    $total['pack_fee_formated'] = price_format($total['pack_fee'], false);

    /* 贺卡费用 */
    if (!empty($order['card_id']))
    {
        $total['card_fee']      = card_fee($order['card_id'], $total['goods_price']);
    }
    $total['card_fee_formated'] = price_format($total['card_fee'], false);

    /* 红包 */

    if (!empty($order['bonus_id']))
    {
        $bonus          = bonus_info($order['bonus_id']);
        $total['bonus'] = $bonus['type_money'];
    }
    
    /* 多红包使用  added by tiger.guo 20160106*/
    if (!empty($order['bonus_ids']))
    {
    	$bonus_id_list = explode(",", $order['bonus_ids']);
    	foreach ($bonus_id_list as $bonus_id) {
    		$bonus          = bonus_info($bonus_id);
    		$total['bonus'] += $bonus['type_money'];
    	}
    }
    
    //判断红包使用最大金额
    $order_max_bonus =  flow_available_bonus(1);
    $total['bonus'] = ($total['bonus'] > $order_max_bonus)? $order_max_bonus : $total['bonus'];
    
    $total['bonus_formated'] = price_format($total['bonus'], false);

    /* 线下红包 */
     if (!empty($order['bonus_kill']))
    {
        $bonus          = bonus_info(0,$order['bonus_kill']);
        $total['bonus_kill'] = $order['bonus_kill'];
        $total['bonus_kill_formated'] = price_format($total['bonus_kill'], false);
    }



    /* 配送费用 */
    $shipping_cod_fee = NULL;

    if ($order['shipping_id'] > 0 && $total['real_goods_count'] > 0)
    {
        $region['country']  = $consignee['country'];
        $region['province'] = $consignee['province'];
        $region['city']     = $consignee['city'];
        $region['district'] = $consignee['district'];
        $shipping_info = shipping_area_info($order['shipping_id'], $region);

        if (!empty($shipping_info))
        {
			 
			$region    = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);
            if ($order['extension_code'] == 'group_buy')
            {
                $weight_price = cart_weight_price(CART_GROUP_BUY_GOODS,$region);
            }
            else
            {
                $weight_price = cart_weight_price(CART_GENERAL_GOODS,$region);
            }
			

            // 查看购物车中是否全为免运费商品，若是则把运费赋为零
            /* $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('cart') . " WHERE  `session_id` = '" . SESS_ID. "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0";
            $shipping_count = $GLOBALS['db']->getOne($sql); */
            
            // 设置某个商品在在某些地区可以包邮
            $shipping_count = get_not_free_shipping_count($region);

            $total['shipping_fee'] = ($shipping_count == 0 AND $weight_price['free_shipping'] == 1) ?0 :  shipping_fee($shipping_info['shipping_code'],$shipping_info['configure'], $weight_price['weight'], $total['goods_price'], $weight_price['number']);
			
            //运费可减 added by tiger.guo 20151226
            $total['shipping_fee'] = ($total['shipping_fee'] - $weight_price['reduce_ship_amt']<=0)?0:($total['shipping_fee'] - $weight_price['reduce_ship_amt']);
            
            if (!empty($order['need_insure']) && $shipping_info['insure'] > 0)
            {
                $total['shipping_insure'] = shipping_insure_fee($shipping_info['shipping_code'],
                    $total['goods_price'], $shipping_info['insure']);
            }
            else
            {
                $total['shipping_insure'] = 0;
            }

            if ($shipping_info['support_cod'])
            {
                $shipping_cod_fee = $shipping_info['pay_fee'];
            }
        }
    }

    $total['shipping_fee_formated']    = price_format($total['shipping_fee'], false);
    $total['shipping_insure_formated'] = price_format($total['shipping_insure'], false);

    // 购物车中的商品能享受红包支付的总额
    $bonus_amount = compute_discount_amount();
    // 红包和积分最多能支付的金额为商品总额
    $max_amount = $total['goods_price'] == 0 ? $total['goods_price'] : $total['goods_price'] - $bonus_amount;

    /* 计算订单总额 */
    if ($order['extension_code'] == 'group_buy' && $group_buy['deposit'] > 0)
    {
        $total['amount'] = $total['goods_price'];
    }
    else
    {
        $total['amount'] = $total['goods_price'] - $total['discount'] + $total['tax'] + $total['pack_fee'] + $total['card_fee'] +
            $total['shipping_fee'] + $total['shipping_insure'] + $total['cod_fee'];

        // 减去红包金额
        $use_bonus        = min($total['bonus'], $max_amount); // 实际减去的红包金额
        if(isset($total['bonus_kill']))
        {
            $use_bonus_kill   = min($total['bonus_kill'], $max_amount);
            $total['amount'] -=  $price = number_format($total['bonus_kill'], 2, '.', ''); // 还需要支付的订单金额
        }

        $total['bonus']   = $use_bonus;
        $total['bonus_formated'] = price_format($total['bonus'], false);

        $total['amount'] -= $use_bonus; // 还需要支付的订单金额
        $max_amount      -= $use_bonus; // 积分最多还能支付的金额

    }

    /* 余额 */
    $order['surplus'] = $order['surplus'] > 0 ? $order['surplus'] : 0;
    if ($total['amount'] > 0)
    {
        if (isset($order['surplus']) && $order['surplus'] > $total['amount'])
        {
            $order['surplus'] = $total['amount'];
            $total['amount']  = 0;
        }
        else
        {
            $total['amount'] -= floatval($order['surplus']);
        }
    }
    else
    {
        $order['surplus'] = 0;
        $total['amount']  = 0;
    }
    $total['surplus'] = $order['surplus'];
    $total['surplus_formated'] = price_format($order['surplus'], false);

    /* 积分 */
    $order['integral'] = $order['integral'] > 0 ? $order['integral'] : 0;
    if ($total['amount'] > 0 && $max_amount > 0 && $order['integral'] > 0)
    {
        $integral_money = value_of_integral($order['integral']);

        // 使用积分支付
        $use_integral            = min($total['amount'], $max_amount, $integral_money); // 实际使用积分支付的金额
        $total['amount']        -= $use_integral;
        $total['integral_money'] = $use_integral;
        $order['integral']       = integral_of_value($use_integral);
    }
    else
    {
        $total['integral_money'] = 0;
        $order['integral']       = 0;
    }
    $total['integral'] = $order['integral'];
    $total['integral_formated'] = price_format($total['integral_money'], false);

    /* 保存订单信息 */
    $_SESSION['flow_order'] = $order;

    $se_flow_type = isset($_SESSION['flow_type']) ? $_SESSION['flow_type'] : '';
    
    /* 支付费用 */
    if (!empty($order['pay_id']) && ($total['real_goods_count'] > 0 || $se_flow_type != CART_EXCHANGE_GOODS))
    {
        $total['pay_fee']      = pay_fee($order['pay_id'], $total['amount'], $shipping_cod_fee);
    }

    $total['pay_fee_formated'] = price_format($total['pay_fee'], false);

    $total['amount']           += $total['pay_fee']; // 订单总额累加上支付费用
    $total['amount_formated']  = price_format($total['amount'], false);

    /* 取得可以得到的积分和红包 */
    if ($order['extension_code'] == 'group_buy')
    {
        $total['will_get_integral'] = $group_buy['gift_integral'];
    }
    elseif ($order['extension_code'] == 'exchange_goods')
    {
        $total['will_get_integral'] = 0;
    }
    else
    {
        $total['will_get_integral'] = get_give_integral(1);
    }
    $total['will_get_bonus']        = $order['extension_code'] == 'exchange_goods' ? 0 : price_format(get_total_bonus(), false);
    $total['formated_goods_price']  = price_format($total['goods_price'], false);
    $total['formated_market_price'] = price_format($total['market_price'], false);
    $total['formated_saving']       = price_format($total['saving'], false);

    if ($order['extension_code'] == 'exchange_goods')
    {
        $sql = 'SELECT SUM(eg.exchange_integral) '.
               'FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c,' . $GLOBALS['ecs']->table('exchange_goods') . 'AS eg '.
               "WHERE c.goods_id = eg.goods_id AND c.session_id= '" . SESS_ID . "' " .
               "  AND c.rec_type = '" . CART_EXCHANGE_GOODS . "' " .
               '  AND c.is_gift = 0 AND c.goods_id > 0 ' .
               'GROUP BY eg.goods_id';
        $exchange_integral = $GLOBALS['db']->getOne($sql);
        $total['exchange_integral'] = $exchange_integral;
    }

    return $total;
}

/**
 * 修改订单
 * @param   int     $order_id   订单id
 * @param   array   $order      key => value
 * @return  bool
 */
function update_order($order_id, $order)
{
    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'),
        $order, 'UPDATE', "order_id = '$order_id'");
}

/**
 * 得到新订单号
 * @return  string
 */
function get_order_sn()
{
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);

    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * 取得购物车商品
 * @param   int     $type   类型：默认普通商品
 * @return  array   购物车商品数组
 */
/*wzys设置某个商品在在某些地区可以包邮，某些地区不能*/  
function cart_goods($type = CART_GENERAL_GOODS,$region_id_list=array(),$is_checked)
{
	/*wzys设置某个商品在在某些地区可以包邮，某些地区不能end*/  
    $sql = "SELECT rec_id, user_id, goods_id, goods_name, goods_sn, goods_number, " .
            "market_price, goods_price, goods_attr_id, goods_attr, is_real, extension_code, parent_id, is_gift, is_shipping, product_id, " .
            "goods_price * goods_number AS subtotal, IF(is_gift, is_gift, goods_id) AS gid " .
            "FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' " .
            "AND rec_type = '$type'";
    if(isset($is_checked)){
    	$sql = $sql." AND is_checked = '$is_checked'";
    } 
    $sql = $sql . " ORDER BY gid, is_gift";

    $arr = $GLOBALS['db']->getAll($sql);

    /* 格式化价格及礼包商品 */
    foreach ($arr as $key => $value)
    {
        $arr[$key]['formated_market_price'] = price_format($value['market_price'], false);
        $arr[$key]['formated_goods_price']  = price_format($value['goods_price'], false);
        $arr[$key]['formated_subtotal']     = price_format($value['subtotal'], false);
        
        //商品图片
        $goods_thumb = $GLOBALS['db']->getOne("SELECT `goods_thumb` FROM " . $GLOBALS['ecs']->table('goods') . " WHERE `goods_id`='{$arr[$key]['goods_id']}'");
        $arr[$key]['goods_thumb'] = get_image_path($arr[$key]['goods_id'], $goods_thumb, true);
        
        /**商品链接**/
        $arr[$key]['goods_url'] = build_uri('goods', array('gid'=>$arr[$key]['goods_id'],'pid'=>$arr[$key]['product_id']), $arr[$key]['goods_name']);
        
        /*根据产品编号查询规格*/
        if ($arr[$key]['product_id'] && intval($arr[$key]['product_id'])>0 )
        {
        	$sql = "SELECT c.attr_name,b.attr_value from ".$GLOBALS['ecs']->table('products_attr')." AS a
					LEFT JOIN ".$GLOBALS['ecs']->table('goods_attr')." AS b ON (a.goods_attr_id = b.goods_attr_id)
					LEFT JOIN ".$GLOBALS['ecs']->table('attribute')." c ON (b.attr_id = c.attr_id)
					WHERE a.product_id = '".$arr[$key]['product_id']."'";
        	$attr_list = $GLOBALS['db']->getAll($sql);
        	foreach ($attr_list AS $attr)
        	{
        		$arr[$key]['goods_name'] .= (' [' .$attr['attr_name'].':'. $attr['attr_value'] . '] ');
        	}
        }
        
        /* 根据商品查询可用红包金额  START*/
        if ($value['extension_code'] == 'package_buy'){
        	$sql = "SELECT sum(IFNULL(g.bonus * pg.goods_number , 0)) AS bonus ".
        			" FROM " . $GLOBALS['ecs']->table('package_goods') . " AS pg
                LEFT JOIN " .$GLOBALS['ecs']->table('goods') . " AS g ON pg.goods_id = g.goods_id WHERE pg.package_id = '".$arr[$key]['goods_id']."'";
        }else{
        	$sql = "SELECT IFNULL(g.bonus, 0) AS bonus ".
        	" FROM ".$GLOBALS['ecs']->table('goods')." AS g WHERE g.goods_id = '".$arr[$key]['goods_id']."'";
        }
        $bonus = $GLOBALS['db']->getOne($sql);
        $arr[$key]['bonus'] = $bonus;
        $arr[$key]['bonus_formatted']   = (empty($arr[$key]['bonus']) || $arr[$key]['bonus'] == 0) ? 0 : price_format($arr[$key]['bonus'], false);
        /* 根据商品查询可用红包金额  END*/
       
        
        
		/*wzys设置某个商品在在某些地区可以包邮，某些地区不能*/  
		if($value['is_shipping']==1)
		{
			
			$sql = 'SELECT c.goods_id FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c, ' .
					$GLOBALS['ecs']->table('goods_free_shipping_area') . ' AS r ' .
				'WHERE r.region_id ' . db_create_in($region_id_list) .
				" AND c.is_shipping = 1 AND r.goods_id = $value[goods_id]";
			$all =  $GLOBALS['db']->getAll($sql);
			if(empty($all))
			{
				$arr[$key]['is_shipping']=0;
			}
			else
			{
				$arr[$key]['is_shipping']=1;
			}
		}
		else
		{
			$arr[$key]['is_shipping']=0;
		}
		/*wzys设置某个商品在在某些地区可以包邮，某些地区不能end*/ 
        if ($value['extension_code'] == 'package_buy')
        {
            $arr[$key]['package_goods_list'] = get_package_goods($value['goods_id']);
            if($arr[$key]['package_goods_list'] && count($arr[$key]['package_goods_list']>0)){
            	$arr[$key]['goods_thumb'] = $arr[$key]['package_goods_list'][0]['goods_thumb'];
            }
        }
        
        /* 订单提交时显示买几送几 */        
        $goods_sql = "SELECT g.is_buy_gift, g.gift_start_date, g.gift_end_date FROM " . $GLOBALS['ecs']->table('goods') . ' AS g ' . 'WHERE g.goods_id = ' . $arr[$key]['goods_id'];        	
        $goods = $GLOBALS['db']->getRow($goods_sql);
        $goods['is_buy_gift'] = ($goods['is_buy_gift']==1 && gmtime() >= $goods['gift_start_date'] && gmtime() <= $goods['gift_end_date'])?1:0;
        //非赠送并且当前参与赠送活动
        if($goods['is_buy_gift'] == 1 && $arr[$key]['is_gift']==0 && $arr[$key]['parent_id']==0){
        	$buy_give_activity_list = get_buy_give_activity_list($arr[$key]['goods_id']);
        	$arr[$key]['buy_give_activity_list'] = $buy_give_activity_list;
        }   
        
        
    }

    return $arr;
}

/**
 * 取得购物车总金额
 * @params  boolean $include_gift   是否包括赠品
 * @param   int     $type           类型：默认普通商品
 * @return  float   购物车总金额
 */
function cart_amount($include_gift = true, $type = CART_GENERAL_GOODS)
{
    $sql = "SELECT SUM(goods_price * goods_number) " .
            " FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' " .
            "AND rec_type = '$type' ";

    if (!$include_gift)
    {
        $sql .= ' AND is_gift = 0 AND goods_id > 0';
    }

    return floatval($GLOBALS['db']->getOne($sql));
}

/**
 * 检查某商品是否已经存在于购物车
 *
 * @access  public
 * @param   integer     $id
 * @param   array       $spec
 * @param   int         $type   类型：默认普通商品
 * @return  boolean
 */
function cart_goods_exists($id, $spec, $type = CART_GENERAL_GOODS)
{
    /* 检查该商品是否已经存在在购物车中 */
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('cart').
            "WHERE session_id = '" .SESS_ID. "' AND goods_id = '$id' ".
            "AND parent_id = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
            "AND rec_type = '$type'";

    return ($GLOBALS['db']->getOne($sql) > 0);
}

/**
 * 获得购物车中商品的总重量、总价格、总数量
 *
 * @access  public
 * @param   int     $type   类型：默认普通商品
 * @return  array
 */
 /*设置某个商品在在某些地区可以包邮，某些地区不能*/  
function cart_weight_price($type = CART_GENERAL_GOODS,$region_id_list=array())
{
    $package_row['weight'] = 0;
    $package_row['amount'] = 0;
    $package_row['number'] = 0;

    $packages_row['free_shipping'] = 1;
    $time = gmtime();

    /* 计算超值礼包内商品的相关配送参数 */
    $sql = 'SELECT goods_id, goods_number, goods_price FROM ' . $GLOBALS['ecs']->table('cart') . " WHERE extension_code = 'package_buy' AND session_id = '" . SESS_ID . "' AND is_checked = 1";
    $row = $GLOBALS['db']->getAll($sql);

    if ($row)
    {
        $packages_row['free_shipping'] = 0;
        $free_shipping_count = 0;

        foreach ($row as $val)
        {
           /*设置某个商品在在某些地区可以包邮，某些地区不能*/  
			// 如果商品全为免运费商品，设置一个标识变量
			 $sql = 'SELECT pg.goods_id FROM ' .
                    $GLOBALS['ecs']->table('package_goods') . ' AS pg, ' .
					$GLOBALS['ecs']->table('goods_free_shipping_area') . ' AS r, ' .
                    $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    "WHERE r.region_id " . db_create_in($region_id_list) ." and r.goods_id=g.goods_id and g.goods_id = pg.goods_id AND g.is_shipping = 1 AND pg.package_id = '"  . $val['goods_id'] . "'";
			$goods_ids = $GLOBALS['db']->getCol($sql);
			
			$where =" and pg.goods_id " . db_create_in($goods_ids) ."";
	        $where = str_replace('IN','NOT IN',$where);
			
            $sql = 'SELECT count(*) FROM ' .
                    $GLOBALS['ecs']->table('package_goods') . ' AS pg, ' .
                    $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    "WHERE g.goods_id = pg.goods_id $where AND pg.package_id = '"  . $val['goods_id'] . "'";
            $shipping_count = $GLOBALS['db']->getOne($sql);

            if ($shipping_count > 0)
            {
                // 循环计算每个超值礼包商品的重量和数量，注意一个礼包中可能包换若干个同一商品
            	//可减运费金额 added by tiger.guo 20151226
            	//可减运费应该排除免费所辖区域 modified by tiger.guo 20160219
            	
                $sql = 'SELECT SUM(g.goods_weight * pg.goods_number) AS weight, ' .
                  	   "SUM((case when (g.is_promote = 1 AND g.promote_start_date <= '$time' AND g.promote_end_date >= '$time') then promote_reduce_ship_amt else reduce_ship_amt end ) * pg.goods_number) AS reduce_ship_amt, ".	
                    'SUM(pg.goods_number) AS number FROM ' .
                    $GLOBALS['ecs']->table('package_goods') . ' AS pg, ' .
                    $GLOBALS['ecs']->table('goods') . ' AS g ' .
                    "WHERE g.goods_id = pg.goods_id $where AND pg.package_id = '"  . $val['goods_id'] . "'";

                $goods_row = $GLOBALS['db']->getRow($sql);
                $package_row['weight'] += floatval($goods_row['weight']) * $val['goods_number'];
                $package_row['amount'] += floatval($val['goods_price']) * $val['goods_number'];
                $package_row['number'] += intval($goods_row['number']) * $val['goods_number'];
                $package_row['reduce_ship_amt'] += floatval($goods_row['reduce_ship_amt']) * $val['goods_number'];
            }
            else
            {
                $free_shipping_count++;
            }
			/*wzys设置某个商品在在某些地区可以包邮，某些地区不能end*/  
        }

        $packages_row['free_shipping'] = $free_shipping_count == count($row) ? 1 : 0;
        
        //可减运费金额 added by tiger.guo 20151226
        //可减运费应该排除免费所辖区域 modified by tiger.guo 20160219
        /* $sql = 'SELECT SUM(g.reduce_ship_amt * pg.goods_number) AS reduce_ship_amt ' .
        		$GLOBALS['ecs']->table('package_goods') . ' AS pg, ' .
        		$GLOBALS['ecs']->table('goods') . ' AS g ' .
        		"WHERE g.goods_id = pg.goods_id AND pg.package_id = '"  . $val['goods_id'] . "'";
        $goods_row = $GLOBALS['db']->getRow($sql);
        $package_row['reduce_ship_amt'] += floatval($goods_row['reduce_ship_amt']) * $val['goods_number']; */
    }

    /* 获得购物车中非超值礼包商品的总重量 */
   /*设置某个商品在在某些地区可以包邮，某些地区不能*/ 
    /* modified by tiger.guo 20160424 赠品不计算运费 */
	$sql = 'SELECT c.goods_id FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c, ' .
                $GLOBALS['ecs']->table('goods_free_shipping_area') . ' AS r, ' .
                $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'WHERE r.region_id ' . db_create_in($region_id_list) .
            " AND c.session_id = '" . SESS_ID . "' AND c.rec_type = '$type' " .
            " AND g.is_shipping = 1 AND c.is_checked = 1 AND c.goods_id = r.goods_id ".
            " AND c.goods_id = g.goods_id AND c.extension_code != 'package_buy' ";
    $goods_ids =  $GLOBALS['db']->getCol($sql);	

	$where =" and c.goods_id " . db_create_in($goods_ids) ."";
	$where = str_replace('IN','NOT IN',$where);
	

	//可减运费金额 added by tiger.guo 20151226
	//可减运费应该排除免费所辖区域 modified by tiger.guo 20160219
	/* modified by tiger.guo 20160424 赠品不计算运费 */
	/* 加入促销商品免运费金额 promote_reduce_ship_amt tiger.guo 20160702  */
    $sql    = 'SELECT SUM(g.goods_weight * c.goods_number) AS weight, ' .
                    'SUM(c.goods_price * c.goods_number) AS amount, ' .
                    'SUM(c.goods_number) AS number, '.
                    "SUM((case when (g.is_promote = 1 AND g.promote_start_date <= '$time' AND g.promote_end_date >= '$time') then promote_reduce_ship_amt else reduce_ship_amt end )  * c.goods_number) AS reduce_ship_amt ".
                'FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c '.
                'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = c.goods_id '.
                "WHERE c.session_id = '" . SESS_ID . "' " .
                "AND c.is_checked = 1 AND c.is_gift = 0  AND c.rec_type = '$type' $where AND c.extension_code != 'package_buy'";
    $row = $GLOBALS['db']->getRow($sql);
	/*wzys设置某个商品在在某些地区可以包邮，某些地区不能end*/  

    $packages_row['weight'] = floatval($row['weight']) + $package_row['weight'];
    $packages_row['amount'] = floatval($row['amount']) + $package_row['amount'];
    $packages_row['number'] = intval($row['number']) + $package_row['number'];
    $packages_row['reduce_ship_amt'] = floatval($row['reduce_ship_amt']) + $package_row['reduce_ship_amt'];
    /* 格式化重量 */
    $packages_row['formated_weight'] = formated_weight($packages_row['weight']);
    
    
    
    /* $sql    = 'SELECT SUM(g.reduce_ship_amt * c.goods_number) AS reduce_ship_amt ' .
    		'FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c '.
    		'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = c.goods_id '.
    		"WHERE c.session_id = '" . SESS_ID . "' " .
    		"AND c.is_checked = 1 AND c.rec_type = '$type' $where AND c.extension_code != 'package_buy'";
    $row = $GLOBALS['db']->getRow($sql);
    $packages_row['reduce_ship_amt'] = floatval($row['reduce_ship_amt']) + $package_row['reduce_ship_amt']; */

    return $packages_row;
}

/**
 * 判断商品库存以及限购情况，详情页增加与减少商品数量时使用
 * @param unknown $goods_id
 * @param number $num
 * @param string $address
 */
function jude_goods_number($goods_id, $num = 1, $address=null){
	
	/* 取得商品信息 */
	$sql = "SELECT g.goods_number ".
			" FROM " .$GLOBALS['ecs']->table('goods'). " AS g ".
			" WHERE g.goods_id = '$goods_id'" ." AND g.is_delete = 0";
	$goods = $GLOBALS['db']->getRow($sql);
	if (empty($goods))
	{
		return ['success'=>false,'msg'=>$GLOBALS['_LANG']['goods_not_exists']];
	}
	
	if ($GLOBALS['_CFG']['use_storage'] == 1)
	{
		//检查：商品购买数量是否大于总库存
		if ($num > $goods['goods_number'])
		{
			return ['success'=>false,'msg'=>sprintf($GLOBALS['_LANG']['shortage'], $goods['goods_number'])];
		}
	
	
		//如果传入了配送地址，根据配送地址匹配、仓库检查库存情况
		if($address){
			$store_number = get_goods_store($goods_id, null, $address);
			if ($num > $store_number){
				return ['success'=>false,'msg'=>sprintf($GLOBALS['_LANG']['shortage'], $store_number)];
			}
		}
		
		//TODO:检查礼包的库存
// 		judge_package_stock($package_id,$num);
	
	}
	
	/*限时抢购商品检查限购数量 */
	$promote_rtn = jude_promote_limit_goods($_SESSION['user_id'], $goods_id,$num);
	if(!$promote_rtn['status']){
		return ['success'=>false,'msg'=>$promote_rtn['msg']];
	}
	
	return ['success'=>true,'msg'=>''];
}

/**
 * 添加商品到购物车
 *
 * @access  public
 * @param   integer $goods_id   商品编号
 * @param   integer $num        商品数量
 * @param   array   $spec       规格值对应的id数组
 * @param   integer $parent     基本件
 * @param   integer $product_id     产品编号
 * @param   array   $regions     选择的配送区域（注意，在商品列表中直接添加到购物车时，无法检查配送区域）
 * @return  boolean
 */
function addto_cart($goods_id, $num = 1, $spec = array(), $parent = 0,$product_id = null, $address=null)
{
    $GLOBALS['err']->clean();
    $_parent_id = $parent;

    /* 取得商品信息 */
    $sql = "SELECT g.goods_name, g.goods_sn, g.is_on_sale, g.is_real, ".
                "g.market_price, g.shop_price AS org_price, g.promote_price, g.promote_start_date, ".
                "g.promote_end_date, g.goods_weight, g.integral, g.extension_code, ".
                "g.goods_number, g.is_alone_sale, g.is_shipping,".
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price ".
            " FROM " .$GLOBALS['ecs']->table('goods'). " AS g ".
            " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            " WHERE g.goods_id = '$goods_id'" .
            " AND g.is_delete = 0";
    $goods = $GLOBALS['db']->getRow($sql);

    if (empty($goods))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['goods_not_exists'], ERR_NOT_EXISTS);

        return false;
    }
    
	/**获取产品信息****/
    $product = null;
    if($product_id){
    	$product = get_product($product_id);
    	if(!$product){
    		//查找不到对应的产品
    		$product_id = null;
    	}
    }
    
    /***TODO:百安母婴中没有配件单独销售的情况，此情况可以忽略 start***/
    /* 如果是作为配件添加到购物车的，需要先检查购物车里面是否已经有基本件 */
    if ($parent > 0)
    {
        
    	$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('cart') .
                " WHERE goods_id='$parent' AND session_id='" . SESS_ID . "' AND extension_code <> 'package_buy'";
        
        
        
        if ($GLOBALS['db']->getOne($sql) == 0)
        {
            $GLOBALS['err']->add($GLOBALS['_LANG']['no_basic_goods'], ERR_NO_BASIC_GOODS);

            return false;
        }
    }
    /***TODO:百安母婴中没有配件单独销售的情况，此情况可以忽略 start***/
    
    /* 是否正在销售 */
    if ($goods['is_on_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['not_on_sale'], ERR_NOT_ON_SALE);

        return false;
    }

    /* 不是配件时检查是否允许单独销售 */
    if (empty($parent) && $goods['is_alone_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['cannt_alone_sale'], ERR_CANNT_ALONE_SALE);

        return false;
    }

    /***TODO:百安母婴中不从页面中直接传入规格，此情况可以忽略 start***/
    /* 如果商品有规格则取规格商品信息 配件除外 */
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '$goods_id' LIMIT 0, 1";
    $prod = $GLOBALS['db']->getRow($sql);

    if (is_spec($spec) && !empty($prod))
    {
        $product_info = get_products_info($goods_id, $spec);
    }
    if (empty($product_info))
    {
        $product_info = array('product_number' => '', 'product_id' => 0);
    }
    /***TODO:百安母婴中不从页面中直接传入规格，此情况可以忽略 end***/
    

    /* 检查：库存 */
    if ($GLOBALS['_CFG']['use_storage'] == 1)
    {
        //检查：商品购买数量是否大于总库存
        if ($num > $goods['goods_number'])
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $goods['goods_number']), ERR_OUT_OF_STOCK);

            return false;
        }
        
        
        //检查产品的库存
    	if ($product && ($num > $product['product_number'])){
        	$GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $goods['product_number']), ERR_OUT_OF_STOCK);
        	return false;
        }
        
        //TODO:如果传入了配送地址，根据配送地址匹配、仓库检查库存情况
        if($address){
        	$store_number = get_goods_store($goods_id, $product['product_id'], $address);
        	if ($num > $store_number){
        		$GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $store_number), ERR_OUT_OF_STOCK);
        		return false;
        	}
        }

        //商品存在规格 是货品 检查该货品库存
        if (is_spec($spec) && !empty($prod))
        {
            if (!empty($spec))
            {
                /* 取规格的货品库存 */
                if ($num > $product_info['product_number'])
                {
                    $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $product_info['product_number']), ERR_OUT_OF_STOCK);
    
                    return false;
                }
            }
        }       
    }
    
    /*限时抢购商品检查限购数量 */
    $promote_rtn = jude_promote_limit_goods($_SESSION['user_id'], $goods_id,$num);
    if(!$promote_rtn['status']){
    	$GLOBALS['err']->add($promote_rtn['msg']);
    	return false;
    }    
    

    /* 计算商品的促销价格 */
    $spec_price             = spec_price($spec);
    $goods_price            = get_final_price($goods_id, $num, true, $spec,$product_id);
    $goods['market_price'] += $spec_price;
    $goods_attr             = get_goods_attr_info($spec);
    $goods_attr_id          = join(',', $spec);

    /* 初始化要插入购物车的基本件数据 */
    $parent = array(
        'user_id'       => $_SESSION['user_id'],
        'session_id'    => SESS_ID,
        'goods_id'      => $goods_id,
        'goods_sn'      => addslashes($goods['goods_sn']),
        'product_id'    => $product_id,
        'goods_name'    => addslashes($goods['goods_name']),
        'market_price'  => $goods['market_price'],
        'goods_attr'    => addslashes($goods_attr),
        'goods_attr_id' => $goods_attr_id,
        'is_real'       => $goods['is_real'],
        'extension_code'=> $goods['extension_code'],
        'is_gift'       => 0,
        'is_shipping'   => $goods['is_shipping'],
        'rec_type'      => CART_GENERAL_GOODS,
    	'is_checked'	=> 1	
    );

    /**本项目单独购买配件的情况暂不处理 start****/
    /* 如果该配件在添加为基本件的配件时，所设置的“配件价格”比原价低，即此配件在价格上提供了优惠， */
    /* 则按照该配件的优惠价格卖，但是每一个基本件只能购买一个优惠价格的“该配件”，多买的“该配件”不享 */
    /* 受此优惠 */
    $basic_list = array();
    $sql = "SELECT parent_id, goods_price " .
            "FROM " . $GLOBALS['ecs']->table('group_goods') .
            " WHERE goods_id = '$goods_id'" .
            " AND goods_price < '$goods_price'" .
            " AND parent_id = '$_parent_id'" .
            " ORDER BY goods_price";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $basic_list[$row['parent_id']] = $row['goods_price'];
    }

    /* 取得购物车中该商品每个基本件的数量 */
    $basic_count_list = array();
    if ($basic_list)
    {
        $sql = "SELECT goods_id, SUM(goods_number) AS count " .
                "FROM " . $GLOBALS['ecs']->table('cart') .
                " WHERE session_id = '" . SESS_ID . "'" .
                " AND parent_id = 0" .
                " AND extension_code <> 'package_buy' " .
                " AND goods_id " . db_create_in(array_keys($basic_list)) .
                " GROUP BY goods_id";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $basic_count_list[$row['goods_id']] = $row['count'];
        }
    }

    /* 取得购物车中该商品每个基本件已有该商品配件数量，计算出每个基本件还能有几个该商品配件 */
    /* 一个基本件对应一个该商品配件 */
    if ($basic_count_list)
    {
        $sql = "SELECT parent_id, SUM(goods_number) AS count " .
                "FROM " . $GLOBALS['ecs']->table('cart') .
                " WHERE session_id = '" . SESS_ID . "'" .
                " AND goods_id = '$goods_id'" .
                " AND extension_code <> 'package_buy' " .
                " AND parent_id " . db_create_in(array_keys($basic_count_list)) .
                " GROUP BY parent_id";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $basic_count_list[$row['parent_id']] -= $row['count'];
        }
    }

    /* 循环插入配件 如果是配件则用其添加数量依次为购物车中所有属于其的基本件添加足够数量的该配件 */
    foreach ($basic_list as $parent_id => $fitting_price)
    {
        /* 如果已全部插入，退出 */
        if ($num <= 0)
        {
            break;
        }

        /* 如果该基本件不再购物车中，执行下一个 */
        if (!isset($basic_count_list[$parent_id]))
        {
            continue;
        }

        /* 如果该基本件的配件数量已满，执行下一个基本件 */
        if ($basic_count_list[$parent_id] <= 0)
        {
            continue;
        }

        /* 作为该基本件的配件插入 */
        $parent['goods_price']  = max($fitting_price, 0) + $spec_price; //允许该配件优惠价格为0
        $parent['goods_number'] = min($num, $basic_count_list[$parent_id]);
        $parent['parent_id']    = $parent_id;

        /* 添加 */
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('cart'), $parent, 'INSERT');

        /* 改变数量 */
        $num -= $parent['goods_number'];
    }
    /**本项目单独购买配件的情况暂不处理 end****/

    /* 如果数量不为0，作为基本件插入 */
    if ($num > 0)
    {
        /* 检查该商品是否已经存在在购物车中 */
        $sql = "SELECT goods_number FROM " .$GLOBALS['ecs']->table('cart').
                " WHERE session_id = '" .SESS_ID. "' AND goods_id = '$goods_id' ".
                " AND parent_id = 0 AND is_gift = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
                " AND extension_code <> 'package_buy' " .
                " AND rec_type = 'CART_GENERAL_GOODS'";
        if($product_id){
        	$sql = $sql ." AND product_id = '$product_id' ";
        }

        $row = $GLOBALS['db']->getRow($sql);

        if($row) //如果购物车已经有此物品，则更新
        {
            $num += $row['goods_number'];
            
            //库存确定
            if($product){
            	$goods_storage=$product['product_number'];
            }else{
            	$goods_storage=$goods['goods_number'];
            }
            
            /* if(is_spec($spec) && !empty($prod) )
            {
             	$goods_storage=$product_info['product_number'];
            }
            else
            {
                $goods_storage=$goods['goods_number'];
            } */
            
            
            if ($GLOBALS['_CFG']['use_storage'] == 0 || $num <= $goods_storage)
            {
                $goods_price = get_final_price($goods_id, $num, true, $spec,$product_id);
                $sql = "UPDATE " . $GLOBALS['ecs']->table('cart') . " SET goods_number = '$num'" .
                       " , goods_price = '$goods_price'".
                       " WHERE session_id = '" .SESS_ID. "' AND goods_id = '$goods_id' ".
                       " AND parent_id = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
                       " AND extension_code <> 'package_buy' " .
                       "AND rec_type = 'CART_GENERAL_GOODS'";
                if($product_id){
                	$sql = $sql ." AND product_id = '$product_id' ";
                }
                
                $GLOBALS['db']->query($sql);
            }
            else
            {
               $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $num), ERR_OUT_OF_STOCK);

                return false;
            }
        }
        else //购物车没有此物品，则插入
        {
            $goods_price = get_final_price($goods_id, $num, true, $spec,$product_id);
            $parent['goods_price']  = max($goods_price, 0);
            $parent['goods_number'] = $num;
            $parent['parent_id']    = 0;
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('cart'), $parent, 'INSERT');
        }
    }
    
    //插入赠品
    add_goods_gift_to_cart($goods_id);

    /* 把赠品删除 */
    /* $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '" . SESS_ID . "' AND is_gift <> 0";
    $GLOBALS['db']->query($sql); */

    return true;
}

/**
 * 清空购物车中选中的商品
 * @param   int     $type   类型：默认普通商品
 */
function clear_cart($type = CART_GENERAL_GOODS, $is_checked, $goods_id)
{
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' AND rec_type = '$type'";
    
    if(isset($is_checked)){
    	$sql = $sql." AND is_checked = '$is_checked'";
    }
    
    if(isset($goods_id)){
    	$sql = $sql." AND ((goods_id = '$goods_id' AND is_gift = 0 AND parent_id = 0)  OR is_gift = '$goods_id' OR parent_id = '$goods_id')";
    }
    $GLOBALS['db']->query($sql);
}


/**
 * 选中/反选购物车中商品
 * @param   int     $type   类型：默认普通商品
 */
function checked_cart($type = CART_GENERAL_GOODS, $is_checked = 1)
{
	$sql = "UPDATE " . $GLOBALS['ecs']->table('cart') .
	" SET is_checked = '$is_checked' WHERE session_id = '" . SESS_ID . "' AND rec_type = '$type'";

	$GLOBALS['db']->query($sql);
}

/**
 * 获得指定的商品属性
 *
 * @access      public
 * @param       array       $arr        规格、属性ID数组
 * @param       type        $type       设置返回结果类型：pice，显示价格，默认；no，不显示价格
 *
 * @return      string
 */
function get_goods_attr_info($arr, $type = 'pice')
{
    $attr   = '';

    if (!empty($arr))
    {
        $fmt = "%s:%s[%s] \n";

        $sql = "SELECT a.attr_name, ga.attr_value, ga.attr_price ".
                "FROM ".$GLOBALS['ecs']->table('goods_attr')." AS ga, ".
                    $GLOBALS['ecs']->table('attribute')." AS a ".
                "WHERE " .db_create_in($arr, 'ga.goods_attr_id')." AND a.attr_id = ga.attr_id";
        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $attr_price = round(floatval($row['attr_price']), 2);
            $attr .= sprintf($fmt, $row['attr_name'], $row['attr_value'], $attr_price);
        }

        $attr = str_replace('[0]', '', $attr);
    }

    return $attr;
}

/**
 * 取得用户信息
 * @param   int     $user_id    用户id
 * @return  array   用户信息
 */
function user_info($user_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('users') .
            " WHERE user_id = '$user_id'";
    $user = $GLOBALS['db']->getRow($sql);

    unset($user['question']);
    unset($user['answer']);

    /* 格式化帐户余额 */
    if ($user)
    {
//        if ($user['user_money'] < 0)
//        {
//            $user['user_money'] = 0;
//        }
        $user['formated_user_money'] = price_format($user['user_money'], false);
        $user['formated_frozen_money'] = price_format($user['frozen_money'], false);
    }

    return $user;
}

/**
 * 修改用户
 * @param   int     $user_id   订单id
 * @param   array   $user      key => value
 * @return  bool
 */
function update_user($user_id, $user)
{
    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'),
        $user, 'UPDATE', "user_id = '$user_id'");
}

/**
 * 取得用户地址列表
 * @param   int     $user_id    用户id
 * @return  array
 */
function address_list($user_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_address') .
            " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得用户地址信息
 * @param   int     $address_id     地址id
 * @return  array
 */
function address_info($address_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_address') .
            " WHERE address_id = '$address_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 取得用户当前可用红包
 * @param   int     $user_id        用户id
 * @param   float   $goods_amount   订单商品金额
 * @return  array   红包数组
 */
function user_bonus($user_id, $goods_amount = 0)
{
    $day    = getdate();
    $today  = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

    $sql = "SELECT t.type_id, t.type_name, t.type_money, b.bonus_id, b.used_amount " .
            "FROM " . $GLOBALS['ecs']->table('bonus_type') . " AS t," .
                $GLOBALS['ecs']->table('user_bonus') . " AS b " .
            "WHERE t.type_id = b.bonus_type_id " .
            "AND t.use_start_date <= '$today' " .
            "AND t.use_end_date >= '$today' " .
            "AND t.min_goods_amount <= '$goods_amount' " .
            "AND b.user_id<>0 " .
            " AND b.user_id = '$user_id' " .
            " AND b.used_amount < t.type_money ";
            //"AND b.order_id = 0";
    return $GLOBALS['db']->getAll($sql);
}

/**
 * 取得红包信息
 * @param   int     $bonus_id   红包id
 * @param   string  $bonus_sn   红包序列号
 * @param   array   红包信息
 */
function bonus_info($bonus_id, $bonus_sn = '')
{
    $sql = "SELECT t.*, b.* " .
            "FROM " . $GLOBALS['ecs']->table('bonus_type') . " AS t," .
                $GLOBALS['ecs']->table('user_bonus') . " AS b " .
            "WHERE t.type_id = b.bonus_type_id ";
    if ($bonus_id > 0)
    {
        $sql .= "AND b.bonus_id = '$bonus_id'";
    }
    else
    {
        $sql .= "AND b.bonus_sn = '$bonus_sn'";
    }

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 检查红包是否已使用
 * @param   int $bonus_id   红包id
 * @return  bool
 */
function bonus_used($bonus_id)
{
    $sql = "SELECT order_id FROM " . $GLOBALS['ecs']->table('user_bonus') .
            " WHERE bonus_id = '$bonus_id'";

    return  $GLOBALS['db']->getOne($sql) > 0;
}

/**
 * 设置红包为已使用
 * @param   int     $bonus_id   红包id
 * @param   int     $order_id   订单id
 * @return  bool
 */
function use_bonus($bonus_id, $order_id, $used_amount = 0.0, $use_desc='')
{
    $sql = "UPDATE " . $GLOBALS['ecs']->table('user_bonus') .
            " SET order_id = '$order_id', used_time = '" . gmtime() . "', used_amount = used_amount + $used_amount " .
            " WHERE bonus_id = '$bonus_id' LIMIT 1";
	
    //红包使用记录
    log_bonus_used($bonus_id, $order_id, $used_amount, $use_desc);
    
    return  $GLOBALS['db']->query($sql);
}

/**
 * 设置红包为未使用
 * @param   int     $bonus_id   红包id
 * @param   int     $order_id   订单id
 * @return  bool
 */
function unuse_bonus($bonus_id, $order_id = 0, $used_amount=0.0)
{
	$sql = "UPDATE " . $GLOBALS['ecs']->table('user_bonus') .
	" SET order_id = 0, used_time = 0, used_amount = used_amount - $used_amount " .
	" WHERE bonus_id = '$bonus_id' LIMIT 1";
	
	//红包使用日志
	log_bonus_used($bonus_id, $order_id, - $used_amount, '红包消费金额退回');
	
	return  $GLOBALS['db']->query($sql);
}


/**
 * 计算积分的价值（能抵多少钱）
 * @param   int     $integral   积分
 * @return  float   积分价值
 */
function value_of_integral($integral)
{
    $scale = floatval($GLOBALS['_CFG']['integral_scale']);

    return $scale > 0 ? round(($integral / 100) * $scale, 2) : 0;
}

/**
 * 计算指定的金额需要多少积分
 *
 * @access  public
 * @param   integer $value  金额
 * @return  void
 */
function integral_of_value($value)
{
    $scale = floatval($GLOBALS['_CFG']['integral_scale']);

    return $scale > 0 ? round($value / $scale * 100) : 0;
}

/**
 * 订单退款
 * @param   array   $order          订单
 * @param   int     $refund_type    退款方式 1 到帐户余额 2 到退款申请（先到余额，再申请提款） 3 不处理
 * @param   string  $refund_note    退款说明
 * @param   float   $refund_amount  退款金额（如果为0，取订单已付款金额）
 * @return  bool
 */
function order_refund($order, $refund_type, $refund_note, $refund_amount = 0)
{
    /* 检查参数 */
    $user_id = $order['user_id'];
    if ($user_id == 0 && $refund_type == 1)
    {
        die('anonymous, cannot return to account balance');
    }

    $amount = $refund_amount > 0 ? $refund_amount : $order['money_paid'];
    if ($amount <= 0)
    {
        return true;
    }

    if (!in_array($refund_type, array(1, 2, 3)))
    {
        die('invalid params');
    }

    /* 备注信息 */
    if ($refund_note)
    {
        $change_desc = $refund_note;
    }
    else
    {
        include_once(ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/admin/order.php');
        $change_desc = sprintf($GLOBALS['_LANG']['order_refund'], $order['order_sn']);
    }

    /* 处理退款 */
    if (1 == $refund_type)
    {
        log_account_change($user_id, $amount, 0, 0, 0, $change_desc);

        return true;
    }
    elseif (2 == $refund_type)
    {
        /* 如果非匿名，退回余额 */
        if ($user_id > 0)
        {
            log_account_change($user_id, $amount, 0, 0, 0, $change_desc);
        }

        /* user_account 表增加提款申请记录 */
        $account = array(
            'user_id'      => $user_id,
            'amount'       => (-1) * $amount,
            'add_time'     => gmtime(),
            'user_note'    => $refund_note,
            'process_type' => SURPLUS_RETURN,
            'admin_user'   => $_SESSION['admin_name'],
            'admin_note'   => sprintf($GLOBALS['_LANG']['order_refund'], $order['order_sn']),
            'is_paid'      => 0
        );
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_account'), $account, 'INSERT');

        return true;
    }
    else
    {
        return true;
    }
}

/**
 * 获得购物车中的商品
 *
 * @access  public
 * @return  array
 */
function get_cart_goods($is_checked)
{
    /* 初始化 */
    $goods_list = array();
    $total = array(
        'goods_price'  => 0, // 本店售价合计（有格式）
        'market_price' => 0, // 市场售价合计（有格式）
        'saving'       => 0, // 节省金额（有格式）
        'save_rate'    => 0, // 节省百分比
        'goods_amount' => 0, // 本店售价合计（无格式）
    );

    /* 循环、统计 */
    //如果用户已经登录，则根据用户编号获取购物车的内容；如果用户没有登录，则根据session_id获取用户编号
    /* $sql = "SELECT *, IF(parent_id, parent_id, goods_id) AS pid " .
    		" FROM " . $GLOBALS['ecs']->table('cart') . " " .
    		" WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'" .
    		" ORDER BY pid, parent_id"; */
    $sql = "SELECT *, IF(parent_id, parent_id, goods_id) AS pid, IF(is_gift, is_gift, goods_id) AS gid " .
    		" FROM " . $GLOBALS['ecs']->table('cart') . " " .
    		" WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";    
    if(isset($is_checked)){
    	$sql = $sql." AND is_checked = '$is_checked'";
    }    
    $sql = $sql . " ORDER BY gid, is_gift";
    
    $res = $GLOBALS['db']->query($sql);

    /* 用于统计购物车中实体商品和虚拟商品的个数 */
    $virtual_goods_count = 0;
    $real_goods_count    = 0;

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if(isset($row['is_checked']) && (intval($row['is_checked'])==1)){
        	$total['goods_price']  += $row['goods_price'] * $row['goods_number'];
        	$total['market_price'] += $row['market_price'] * $row['goods_number'];
        	$total['goods_number'] +=  $row['goods_number'];
        }
    	

        $row['subtotal']     = price_format($row['goods_price'] * $row['goods_number'], false);
        $row['goods_price']  = price_format($row['goods_price'], false);
        $row['market_price'] = price_format($row['market_price'], false);

        /* 统计实体商品和虚拟商品的个数 */
        if(isset($row['is_checked']) && (intval($row['is_checked'])==1)){
	        if ($row['is_real'])
	        {
	            $real_goods_count++;
	        }
	        else
	        {
	            $virtual_goods_count++;
	        }
	        }

        /* 查询规格 */
        if (trim($row['goods_attr']) != '')
        {
            $sql = "SELECT attr_value FROM " . $GLOBALS['ecs']->table('goods_attr') . " WHERE goods_attr_id " .
            db_create_in($row['goods_attr']);
            $attr_list = $GLOBALS['db']->getCol($sql);
            foreach ($attr_list AS $attr)
            {
                $row['goods_name'] .= ' [' . $attr . '] ';
            }
        }
        
        /*根据产品编号查询规格*/
        if ($row['product_id'] && intval($row['product_id'])>0 )
        {
        	$sql = "SELECT c.attr_name,b.attr_value from ".$GLOBALS['ecs']->table('products_attr')." AS a
					LEFT JOIN ".$GLOBALS['ecs']->table('goods_attr')." AS b ON (a.goods_attr_id = b.goods_attr_id)
					LEFT JOIN ".$GLOBALS['ecs']->table('attribute')." c ON (b.attr_id = c.attr_id)
					WHERE a.product_id = '".$row['product_id']."'";
        	$attr_list = $GLOBALS['db']->getAll($sql);
        	foreach ($attr_list AS $attr)
        	{
        		$row['goods_name'] .= (' [' .$attr['attr_name'].':'. $attr['attr_value'] . '] ');
        	}
        }        
        
        /* 增加是否在购物车里显示商品图 */
        if (($GLOBALS['_CFG']['show_goods_in_cart'] == "2" || $GLOBALS['_CFG']['show_goods_in_cart'] == "3") && $row['extension_code'] != 'package_buy')
        {
            $goods_thumb = $GLOBALS['db']->getOne("SELECT `goods_thumb` FROM " . $GLOBALS['ecs']->table('goods') . " WHERE `goods_id`='{$row['goods_id']}'");
            $row['goods_thumb'] = get_image_path($row['goods_id'], $goods_thumb, true);
        }
        if ($row['extension_code'] == 'package_buy')
        {
            $row['package_goods_list'] = get_package_goods($row['goods_id']);
            if($row['package_goods_list'] && count($row['package_goods_list']>0)){
            	$row['goods_thumb'] = $row['package_goods_list'][0]['goods_thumb'];
            }
           
        }
        
        /**商品链接**/
        $row['goods_url']              = build_uri('goods', array('gid'=>$row['goods_id'],'pid'=>$row['product_id']), $row['goods_name']);
        
        /*是否有货*/
        if($row['extension_code'] == 'package_buy'){
        	//TODO:需增加优惠套装库存判断
        	if(!judge_package_stock($row['goods_id'], $row['goods_number'])){
        		//TODO:如果有库存时，先暂时设置为1，用来在购物车中判断是否有货；如果需要的时候，可以取真实的库存
        		$row['goods_stock_number']=1;
        	}
        }else{
        	//货品
        	$row['goods_stock_number'] = get_goods_store($row['goods_id'], $row['product_id']);
        }
        
        $goods_list[] = $row;
    }
    $total['goods_amount'] = $total['goods_price'];
    $total['saving']       = price_format($total['market_price'] - $total['goods_price'], false);
    if ($total['market_price'] > 0)
    {
        $total['save_rate'] = $total['market_price'] ? round(($total['market_price'] - $total['goods_price']) *
        100 / $total['market_price']).'%' : 0;
    }
    $total['goods_amount']  = price_format($total['goods_amount'], false);
    $total['goods_price']  = price_format($total['goods_price'], false);
    $total['goods_number']  = isset($total['goods_number'])?$total['goods_number']:0;
    $total['market_price'] = price_format($total['market_price'], false);
    $total['real_goods_count']    = $real_goods_count;
    $total['virtual_goods_count'] = $virtual_goods_count;

    return array('goods_list' => $goods_list, 'total' => $total);
}


/**
 * 限时抢购限购数量(提交订单时判断)
 * @param unknown $user_id
 * @return multitype: boolean string
 */
function jude_promote_limit($user_id){
	$rtn = array('status'=>true,'msg'=>'');
	$time = isset($time)? $time : gmtime();	
	$is_checked = 1;
	
	$sql = "SELECT g.goods_id ".
			" FROM " . $GLOBALS['ecs']->table('cart') . " AS c " .
			" LEFT JOIN ". $GLOBALS['ecs']->table('goods') ." AS g ON (c.goods_id = g.goods_id) ".
			" WHERE c.session_id = '" . SESS_ID . "'  AND c.rec_type = '" . CART_GENERAL_GOODS . "'".
			" AND c.parent_id = 0 AND c.is_gift = 0 AND c.is_checked = '$is_checked' ".
			" AND (g.is_promote = 1 AND g.promote_start_date <= '$time' AND g.promote_end_date >= '$time')";
	/* $sql = "SELECT g.goods_id ".
			" FROM " . $GLOBALS['ecs']->table('cart') . " AS c " .
			" LEFT JOIN ". $GLOBALS['ecs']->table('goods') ." AS g ON (c.goods_id = g.goods_id) ".
			" WHERE c.session_id = '" . SESS_ID . "'  AND c.rec_type = '" . CART_GENERAL_GOODS . "'".
			" AND c.parent_id = 0 AND c.is_gift = 0 AND c.is_checked = '$is_checked' "; */
	$rows = $GLOBALS['db']->getAll($sql);
	foreach ($rows as $key => $row) {
		$promote_rtn = jude_promote_limit_goods($user_id, $row['goods_id'],0,$is_checked);
		if(!$promote_rtn['status']){
			$rtn['status'] = $promote_rtn['status'];
			$rtn['msg'] = $promote_rtn['msg'];
			break;
		}
	}
	return $rtn;
}

/**
 * 判断某商品限时抢购数量（加入到购物车时进行判断）
 * @param unknown $user_id
 * @param string $goods_id
 * @param number $num
 * @return multitype:boolean string
 */
function jude_promote_limit_goods($user_id,$goods_id,$num = 0,$is_checked = 0){
	$rtn = array('status'=>true,'msg'=>'');
	$time = isset($time)? $time : gmtime();	
	$day    = getdate();
	$today_start_date  = local_mktime(0, 0, 0, $day['mon'], $day['mday'], $day['year']);
	$today_end_date  = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);
	
	//查询商品信息
	$sql = "SELECT g.goods_id, g.goods_name, g.is_promote, g.promote_start_date, g.promote_end_date, g.promote_limit_num ".
			" FROM " . $GLOBALS['ecs']->table('goods') . " AS g " .					
			" WHERE g.goods_id= '$goods_id' ".
			" AND (g.is_promote = 1 AND g.promote_start_date <= '$time' AND g.promote_end_date >= '$time')";	
	/* $sql = "SELECT g.goods_id, g.goods_name, g.promote_limit_num ".
			" FROM " . $GLOBALS['ecs']->table('goods') . " AS g " .
			" WHERE g.goods_id= '$goods_id' "; */
	$goods = $GLOBALS['db']->getRow($sql);
	if(!$goods || $goods['promote_limit_num']<=0){
		//不限购直接返回
		return $rtn;
	}
	
	//购物车中已经存在的数量
	$sql_cart = "SELECT IFNULL(SUM(c.goods_number), 0) ".
			" FROM " . $GLOBALS['ecs']->table('cart') . " AS c " .		
			" WHERE c.session_id = '" . SESS_ID . "'  AND c.rec_type = '" . CART_GENERAL_GOODS . "'".
			" AND c.parent_id = 0 AND c.is_gift = 0 AND c.goods_id = '$goods_id'";	
	if($is_checked){
		//提交订单时，只判断当前选中的商品
		$sql_cart .=" AND c.is_checked = '$is_checked' ";
	}	
	$cart_num = $GLOBALS['db']->getOne($sql_cart);
	
	//查询当前登录用户已经购买该商品的数量，是否超过限购数量
	$og_sql = "SELECT IFNULL(sum(og.goods_number),0) from ecs_order_info o, ecs_order_goods og ".
			" WHERE o.order_id = og.order_id AND og.is_gift = 0 AND og.extension_code <> 'package_buy'".
			" AND o.user_id = '$user_id' AND o.add_time >= '$today_start_date' AND o.add_time <= '$today_end_date' ".
			" AND og.goods_id = '$goods_id' AND o.order_status ".db_create_in(array(OS_UNCONFIRMED, OS_CONFIRMED, OS_SPLITED,OS_SPLITING_PART));
	$order_goods_number = $GLOBALS['db']->getOne($og_sql);
	if($order_goods_number + $cart_num + $num  > $goods['promote_limit_num']){
		$rtn['status'] = false;
		/* $rtn['msg'] = sprintf($GLOBALS['_LANG']['promote_limit'], $goods['goods_name'],
							$goods['promote_limit_num'], $goods['promote_limit_num']); */
		$rtn['msg'] = sprintf($GLOBALS['_LANG']['promote_limit_1'],  $goods['promote_limit_num']);
	}			
	return $rtn;
}

/**
 * 取得收货人信息
 * @param   int     $user_id    用户编号
 * @return  array
 */
function get_consignee($user_id)
{
    if (isset($_SESSION['flow_consignee']))
    {
        /* 如果存在session，则直接返回session中的收货人信息 */

        return $_SESSION['flow_consignee'];
    }
    else
    {
        /* 如果不存在，则取得用户的默认收货人信息 */
        $arr = array();

        if ($user_id > 0)
        {
        	/* 取默认地址 */
        	$sql = "SELECT ua.*, rg_country.region_name AS country_name, ".
        			"rg_province.region_name AS province_name, ".
        			"rg_city.region_name AS city_name, ".
        			"rg_district.region_name AS district_name ".
        			' FROM ' . $GLOBALS['ecs']->table('user_address') .' AS ua ' .
        			' INNER JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON ua.address_id = u.address_id' .
        			' LEFT JOIN ' . $GLOBALS['ecs']->table('region') . ' AS rg_country ON ua.country = rg_country.region_id' .
        			' LEFT JOIN ' . $GLOBALS['ecs']->table('region') . ' AS rg_province ON ua.province = rg_province.region_id' .
        			' LEFT JOIN ' . $GLOBALS['ecs']->table('region') . ' AS rg_city ON ua.city = rg_city.region_id' .
        			' LEFT JOIN ' . $GLOBALS['ecs']->table('region') . ' AS rg_district ON ua.district = rg_district.region_id' .
        			" WHERE u.user_id = '$user_id' LIMIT 1";
            
            /* $sql = "SELECT ua.*".
                    " FROM " . $GLOBALS['ecs']->table('user_address') . "AS ua, ".$GLOBALS['ecs']->table('users').' AS u '.
                    " WHERE u.user_id='$user_id' AND ua.address_id = u.address_id"; */

            $arr = $GLOBALS['db']->getRow($sql);
        }

        return $arr;
    }
}

/**
 * 查询购物车（订单id为0）或订单中是否有实体商品
 * @param   int     $order_id   订单id
 * @param   int     $flow_type  购物流程类型
 * @return  bool
 */
function exist_real_goods($order_id = 0, $flow_type = CART_GENERAL_GOODS)
{
    if ($order_id <= 0)
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('cart') .
                " WHERE session_id = '" . SESS_ID . "' AND is_real = 1 " .
                "AND rec_type = '$flow_type'";
    }
    else
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('order_goods') .
                " WHERE order_id = '$order_id' AND is_real = 1";
    }

    return $GLOBALS['db']->getOne($sql) > 0;
}

/**
 * 检查收货人信息是否完整
 * @param   array   $consignee  收货人信息
 * @param   int     $flow_type  购物流程类型
 * @return  bool    true 完整 false 不完整
 */
function check_consignee_info($consignee, $flow_type)
{
    if (exist_real_goods(0, $flow_type))
    {
        /* 如果存在实体商品 */
        /* $res = !empty($consignee['consignee']) &&
            !empty($consignee['country']) &&
            !empty($consignee['email']) &&
            !empty($consignee['tel']); */
		
    	$res = !empty($consignee['consignee']) &&
            (!empty($consignee['mobile']) ||
            !empty($consignee['tel']));
    	
        if ($res)
        {
            if (empty($consignee['province']))
            {
                /* 没有设置省份，检查当前国家下面有没有设置省份 */
                $pro = get_regions(1, $consignee['country']);
                $res = empty($pro);
            }
            elseif (empty($consignee['city']))
            {
                /* 没有设置城市，检查当前省下面有没有城市 */
                $city = get_regions(2, $consignee['province']);
                $res = empty($city);
            }
            elseif (empty($consignee['district']))
            {
                $dist = get_regions(3, $consignee['city']);
                $res = empty($dist);
            }
        }

        return $res;
    }
    else
    {
        /* 如果不存在实体商品 */
        return !empty($consignee['consignee']) &&
            !empty($consignee['email']) &&
            !empty($consignee['tel']);
    }
}

/**
 * 获得上一次用户采用的支付和配送方式
 *
 * @access  public
 * @return  void
 */
function last_shipping_and_payment()
{
$sql = "SELECT shipping_id, pay_id " .
" FROM " . $GLOBALS['ecs']->table('order_info') .
" WHERE user_id = '$_SESSION[user_id]' " .
" ORDER BY order_id DESC LIMIT 1";
$row = $GLOBALS['db']->getRow($sql);

if (empty($row))
{
/* 如果获得是一个空数组，则返回默认值 */
$row = array('shipping_id' => 1, 'pay_id' => 0); // 修改这里的1，可以修改默认为你安装的第几个配送方式 0 是默认的支付方式
}

return $row;
}

/**
 * 取得当前用户应该得到的红包总额
 */
function get_total_bonus()
{
    $day    = getdate();
    $today  = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

    /* 按商品发的红包 */
    $sql = "SELECT SUM(c.goods_number * t.type_money)" .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, "
                    . $GLOBALS['ecs']->table('bonus_type') . " AS t, "
                    . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.session_id = '" . SESS_ID . "' " .
            "AND c.is_gift = 0 " .
            "AND c.goods_id = g.goods_id " .
            "AND g.bonus_type_id = t.type_id " .
            "AND t.send_type = '" . SEND_BY_GOODS . "' " .
            "AND t.send_start_date <= '$today' " .
            "AND t.send_end_date >= '$today' " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "'" .
    		" AND c.is_checked = 1";
    $goods_total = floatval($GLOBALS['db']->getOne($sql));

    /* 取得购物车中非赠品总金额 */
    $sql = "SELECT SUM(goods_price * goods_number) " .
            "FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' " .
            " AND is_gift = 0 " .
            " AND rec_type = '" . CART_GENERAL_GOODS . "'".
    		" AND is_checked = 1";
    $amount = floatval($GLOBALS['db']->getOne($sql));

    /* 按订单发的红包 */
    $sql = "SELECT FLOOR('$amount' / min_amount) * type_money " .
            "FROM " . $GLOBALS['ecs']->table('bonus_type') .
            " WHERE send_type = '" . SEND_BY_ORDER . "' " .
            " AND send_start_date <= '$today' " .
            "AND send_end_date >= '$today' " .
            "AND min_amount > 0 ";
    $order_total = floatval($GLOBALS['db']->getOne($sql));

    return $goods_total + $order_total;
}

/**
 * 处理红包（下订单时设为使用，取消（无效，退货）订单时设为未使用
 * @param   int     $bonus_id   红包编号
 * @param   int     $order_id   订单号
 * @param   int     $is_used    是否使用了
 */
function change_user_bonus($bonus_id, $order_id, $is_used = true, $used_amount = 0.0, $use_desc='')
{
    if ($is_used)
    {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('user_bonus') . ' SET ' .
                ' used_time = ' . gmtime() . ', ' .
                " order_id = '$order_id' " .
                " used_amount = used_amount + $used_amount " .
                " WHERE bonus_id = '$bonus_id'";
        
    }
    else
    {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('user_bonus') . ' SET ' .
                " used_time = 0, " .
                " order_id = 0, " .
                " used_amount = used_amount - $used_amount " .
                " WHERE bonus_id = '$bonus_id'";    
        $used_amount = - $used_amount;
    }
    $GLOBALS['db']->query($sql);
    
    log_bonus_used($bonus_id, $order_id, $used_amount, $use_desc);
}

/**
 * 获得订单信息
 *
 * @access  private
 * @return  array
 */
function flow_order_info()
{
    $order = isset($_SESSION['flow_order']) ? $_SESSION['flow_order'] : array();

    /* 初始化配送和支付方式 */
    if (!isset($order['shipping_id']) || !isset($order['pay_id']))
    {
        /* 如果还没有设置配送和支付 */
        if ($_SESSION['user_id'] > 0)
        {
            /* 用户已经登录了，则获得上次使用的配送和支付 */
            $arr = last_shipping_and_payment();

            if (!isset($order['shipping_id']))
            {
                $order['shipping_id'] = $arr['shipping_id'];
            }
            if (!isset($order['pay_id']))
            {
                $order['pay_id'] = $arr['pay_id'];
            }
        }
        else
        {
            if (!isset($order['shipping_id']))
            {
                $order['shipping_id'] = 0;
            }
            if (!isset($order['pay_id']))
            {
                $order['pay_id'] = 0;
            }
        }
    }

    if (!isset($order['pack_id']))
    {
        $order['pack_id'] = 0;  // 初始化包装
    }
    if (!isset($order['card_id']))
    {
        $order['card_id'] = 0;  // 初始化贺卡
    }
    if (!isset($order['bonus']))
    {
        $order['bonus'] = 0;    // 初始化红包
    }
    if (!isset($order['integral']))
    {
        $order['integral'] = 0; // 初始化积分
    }
    if (!isset($order['surplus']))
    {
        $order['surplus'] = 0;  // 初始化余额
    }

    /* 扩展信息 */
    if (isset($_SESSION['flow_type']) && intval($_SESSION['flow_type']) != CART_GENERAL_GOODS)
    {
        $order['extension_code'] = $_SESSION['extension_code'];
        $order['extension_id'] = $_SESSION['extension_id'];
    }

    return $order;
}

/**
 * 合并订单
 * @param   string  $from_order_sn  从订单号
 * @param   string  $to_order_sn    主订单号
 * @return  成功返回true，失败返回错误信息
 */
function merge_order($from_order_sn, $to_order_sn)
{
    /* 订单号不能为空 */
    if (trim($from_order_sn) == '' || trim($to_order_sn) == '')
    {
        return $GLOBALS['_LANG']['order_sn_not_null'];
    }

    /* 订单号不能相同 */
    if ($from_order_sn == $to_order_sn)
    {
        return $GLOBALS['_LANG']['two_order_sn_same'];
    }

    /* 取得订单信息 */
    $from_order = order_info(0, $from_order_sn);
    $to_order   = order_info(0, $to_order_sn);

    /* 检查订单是否存在 */
    if (!$from_order)
    {
        return sprintf($GLOBALS['_LANG']['order_not_exist'], $from_order_sn);
    }
    elseif (!$to_order)
    {
        return sprintf($GLOBALS['_LANG']['order_not_exist'], $to_order_sn);
    }

    /* 检查合并的订单是否为普通订单，非普通订单不允许合并 */
    if ($from_order['extension_code'] != '' || $to_order['extension_code'] != 0)
    {
        return $GLOBALS['_LANG']['merge_invalid_order'];
    }

    /* 检查订单状态是否是已确认或未确认、未付款、未发货 */
    if ($from_order['order_status'] != OS_UNCONFIRMED && $from_order['order_status'] != OS_CONFIRMED)
    {
        return sprintf($GLOBALS['_LANG']['os_not_unconfirmed_or_confirmed'], $from_order_sn);
    }
    elseif ($from_order['pay_status'] != PS_UNPAYED)
    {
        return sprintf($GLOBALS['_LANG']['ps_not_unpayed'], $from_order_sn);
    }
    elseif ($from_order['shipping_status'] != SS_UNSHIPPED)
    {
        return sprintf($GLOBALS['_LANG']['ss_not_unshipped'], $from_order_sn);
    }

    if ($to_order['order_status'] != OS_UNCONFIRMED && $to_order['order_status'] != OS_CONFIRMED)
    {
        return sprintf($GLOBALS['_LANG']['os_not_unconfirmed_or_confirmed'], $to_order_sn);
    }
    elseif ($to_order['pay_status'] != PS_UNPAYED)
    {
        return sprintf($GLOBALS['_LANG']['ps_not_unpayed'], $to_order_sn);
    }
    elseif ($to_order['shipping_status'] != SS_UNSHIPPED)
    {
        return sprintf($GLOBALS['_LANG']['ss_not_unshipped'], $to_order_sn);
    }

    /* 检查订单用户是否相同 */
    if ($from_order['user_id'] != $to_order['user_id'])
    {
        return $GLOBALS['_LANG']['order_user_not_same'];
    }

    /* 合并订单 */
    $order = $to_order;
    $order['order_id']  = '';
    $order['add_time']  = gmtime();

    // 合并商品总额
    $order['goods_amount'] += $from_order['goods_amount'];

    // 合并折扣
    $order['discount'] += $from_order['discount'];

    if ($order['shipping_id'] > 0)
    {
        // 重新计算配送费用
        $weight_price       = order_weight_price($to_order['order_id']);
        $from_weight_price  = order_weight_price($from_order['order_id']);
        $weight_price['weight'] += $from_weight_price['weight'];
        $weight_price['amount'] += $from_weight_price['amount'];
        $weight_price['number'] += $from_weight_price['number'];

        $region_id_list = array($order['country'], $order['province'], $order['city'], $order['district']);
        $shipping_area = shipping_area_info($order['shipping_id'], $region_id_list);

        $order['shipping_fee'] = shipping_fee($shipping_area['shipping_code'],
            unserialize($shipping_area['configure']), $weight_price['weight'], $weight_price['amount'], $weight_price['number']);

        // 如果保价了，重新计算保价费
        if ($order['insure_fee'] > 0)
        {
            $order['insure_fee'] = shipping_insure_fee($shipping_area['shipping_code'], $order['goods_amount'], $shipping_area['insure']);
        }
    }

    // 重新计算包装费、贺卡费
    if ($order['pack_id'] > 0)
    {
        $pack = pack_info($order['pack_id']);
        $order['pack_fee'] = $pack['free_money'] > $order['goods_amount'] ? $pack['pack_fee'] : 0;
    }
    if ($order['card_id'] > 0)
    {
        $card = card_info($order['card_id']);
        $order['card_fee'] = $card['free_money'] > $order['goods_amount'] ? $card['card_fee'] : 0;
    }

    // 红包不变，合并积分、余额、已付款金额
    $order['integral']      += $from_order['integral'];
    $order['integral_money'] = value_of_integral($order['integral']);
    $order['surplus']       += $from_order['surplus'];
    $order['money_paid']    += $from_order['money_paid'];

    // 计算应付款金额（不包括支付费用）
    $order['order_amount'] = $order['goods_amount'] - $order['discount']
                           + $order['shipping_fee']
                           + $order['insure_fee']
                           + $order['pack_fee']
                           + $order['card_fee']
                           - $order['bonus']
                           - $order['integral_money']
                           - $order['surplus']
                           - $order['money_paid'];

    // 重新计算支付费
    if ($order['pay_id'] > 0)
    {
        // 货到付款手续费
        $cod_fee          = $shipping_area ? $shipping_area['pay_fee'] : 0;
        $order['pay_fee'] = pay_fee($order['pay_id'], $order['order_amount'], $cod_fee);

        // 应付款金额加上支付费
        $order['order_amount'] += $order['pay_fee'];
    }

    /* 插入订单表 */
    do
    {
        $order['order_sn'] = get_order_sn();
        if ($GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), addslashes_deep($order), 'INSERT'))
        {
            break;
        }
        else
        {
            if ($GLOBALS['db']->errno() != 1062)
            {
                die($GLOBALS['db']->errorMsg());
            }
        }
    }
    while (true); // 防止订单号重复

    /* 订单号 */
    $order_id = $GLOBALS['db']->insert_id();

    /* 更新订单商品 */
    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_goods') .
            " SET order_id = '$order_id' " .
            "WHERE order_id " . db_create_in(array($from_order['order_id'], $to_order['order_id']));
    $GLOBALS['db']->query($sql);

    include_once(ROOT_PATH . 'includes/lib_clips.php');
    /* 插入支付日志 */
    insert_pay_log($order_id, $order['order_amount'], PAY_ORDER);

    /* 删除原订单 */
    $sql = 'DELETE FROM ' . $GLOBALS['ecs']->table('order_info') .
            " WHERE order_id " . db_create_in(array($from_order['order_id'], $to_order['order_id']));
    $GLOBALS['db']->query($sql);

    /* 删除原订单支付日志 */
    $sql = 'DELETE FROM ' . $GLOBALS['ecs']->table('pay_log') .
            " WHERE order_id " . db_create_in(array($from_order['order_id'], $to_order['order_id']));
    $GLOBALS['db']->query($sql);

    /* 返还 from_order 的红包，因为只使用 to_order 的红包 */
    if ($from_order['bonus_id'] > 0)
    {
        unuse_bonus($from_order['bonus_id']);
    }

    /* 返回成功 */
    return true;
}

/**
 * 查询配送区域属于哪个办事处管辖
 * @param   array   $regions    配送区域（1、2、3、4级按顺序）
 * @return  int     办事处id，可能为0
 */
function get_agency_by_regions($regions)
{
    if (!is_array($regions) || empty($regions))
    {
        return 0;
    }

    $arr = array();
    $sql = "SELECT region_id, agency_id " .
            "FROM " . $GLOBALS['ecs']->table('region') .
            " WHERE region_id " . db_create_in($regions) .
            " AND region_id > 0 AND agency_id > 0";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$row['region_id']] = $row['agency_id'];
    }
    if (empty($arr))
    {
        return 0;
    }

    $agency_id = 0;
    for ($i = count($regions) - 1; $i >= 0; $i--)
    {
        if (isset($arr[$regions[$i]]))
        {
            return $arr[$regions[$i]];
        }
    }
}

/**
 * 获取配送插件的实例
 * @param   int   $shipping_id    配送插件ID
 * @return  object     配送插件对象实例
 */
function &get_shipping_object($shipping_id)
{
    $shipping  = shipping_info($shipping_id);
    if (!$shipping)
    {
        $object = new stdClass();
        return $object;
    }

    $file_path = ROOT_PATH.'includes/modules/shipping/' . $shipping['shipping_code'] . '.php';

    include_once($file_path);

    $object = new $shipping['shipping_code'];
    return $object;
}

/**
 * 改变订单中商品库存
 * @param   int     $order_id   订单号
 * @param   bool    $is_dec     是否减少库存
 * @param   bool    $storage     减库存的时机，1，下订单时；0，发货时；
 */
function change_order_goods_storage($order_id, $is_dec = true, $storage = 0)
{
	//TODO：根据配送地址匹配仓库
	$sql_address = "select country,province,city,district from ".$GLOBALS['ecs']->table('order_info')." where order_id='$order_id' limit 1";
	$shipping_address = $GLOBALS['db'] ->getRow($sql_address);
	$storeroom = get_storeroom(array($shipping_address['country'],$shipping_address['province'],$shipping_address['city'],$shipping_address['district']));
	$store_id = isset($storeroom)?$storeroom['store_id']:null;
	
    /* 查询订单商品信息 */
    switch ($storage)
    {
        case 0 :
            $sql = "SELECT goods_id, SUM(send_number) AS num, MAX(extension_code) AS extension_code, product_id FROM " . $GLOBALS['ecs']->table('order_goods') .
                    " WHERE order_id = '$order_id' AND is_real = 1 GROUP BY goods_id, product_id";
        break;

        case 1 :
            $sql = "SELECT goods_id, SUM(goods_number) AS num, MAX(extension_code) AS extension_code, product_id FROM " . $GLOBALS['ecs']->table('order_goods') .
                    " WHERE order_id = '$order_id' AND is_real = 1 GROUP BY goods_id, product_id";
        break;
    }
    
    

    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['extension_code'] != "package_buy")
        {
            if ($is_dec)
            {
                change_goods_storage($row['goods_id'], $row['product_id'], - $row['num'],$store_id);
            }
            else
            {
                change_goods_storage($row['goods_id'], $row['product_id'], $row['num'],$store_id);
            }
            $GLOBALS['db']->query($sql);
        }
        else
        {
            $sql = "SELECT goods_id, goods_number" .
                   " FROM " . $GLOBALS['ecs']->table('package_goods') .
                   " WHERE package_id = '" . $row['goods_id'] . "'";
            $res_goods = $GLOBALS['db']->query($sql);
            while ($row_goods = $GLOBALS['db']->fetchRow($res_goods))
            {
                $sql = "SELECT is_real" .
                   " FROM " . $GLOBALS['ecs']->table('goods') .
                   " WHERE goods_id = '" . $row_goods['goods_id'] . "'";
                $real_goods = $GLOBALS['db']->query($sql);
                $is_goods = $GLOBALS['db']->fetchRow($real_goods);

                if ($is_dec)
                {
                    change_goods_storage($row_goods['goods_id'], $row['product_id'], - ($row['num'] * $row_goods['goods_number']),$store_id);
                }
                elseif ($is_goods['is_real'])
                {
                    change_goods_storage($row_goods['goods_id'], $row['product_id'], ($row['num'] * $row_goods['goods_number']),$store_id);
                }
            }
        }
    }

}

/* 代码增加_start  By www.ecshop120.com */
function change_goods_storage_kufang($good_id, $product_id, $number = 0, $store_id)
{
	if ($number == 0)
    {
        return true; // 值为0即不做、增减操作，返回true
    }

    if (empty($good_id)  || empty($store_id))
    {
        return false;
    }

	if ($product_id)
	{
		$goods_attr = $GLOBALS['db']->getOne("select goods_attr from ".$GLOBALS['ecs']->table('products')." where product_id='$product_id' limit 1");
	}
	else
	{
		$goods_attr ='';
	}

	$products_query_qq2211707 = true;

    $number = ($number > 0) ? '+ ' . $number : $number;
	
    /* 处理库房库存 */
	
    $sql_www_ecshop120_com = "UPDATE " . $GLOBALS['ecs']->table('products_storeroom') ."
            SET store_number = if((store_number $number) <0, 0, (store_number $number)) 
            WHERE goods_id = '$good_id' and goods_attr='$goods_attr' and store_id='$store_id' 
            LIMIT 1";
    $products_query_qq2211707  = $GLOBALS['db']->query($sql_www_ecshop120_com);
	return  $products_query_qq221170;  

}
/* 代码增加_end  By  www.ecshop120.com  */
/**
 * 商品库存增与减 货品库存增与减
 *
 * @param   int    $good_id         商品ID
 * @param   int    $product_id      货品ID
 * @param   int    $number          增减数量，默认0；
 * @param   int    $store_id        仓库编号，默认为null
 *
 * @return  bool               true，成功；false，失败；
 */
function change_goods_storage($good_id, $product_id, $number = 0,$store_id = null)
{
    if ($number == 0)
    {
        return true; // 值为0即不做、增减操作，返回true
    }

    if (empty($good_id) || empty($number))
    {
        return false;
    }

    $number = ($number > 0) ? '+ ' . $number : $number;
    
    //TODO:根据配送区域，匹配仓库，扣减对应的库存
    $store_query = true;
    if($store_id){
    	if(!empty($product_id)){
    		//货品
    		$sql = "UPDATE " . $GLOBALS['ecs']->table('products_store') ."
    		SET product_number = product_number $number
    		WHERE store_id = '$store_id'
    		AND product_id = '$product_id'
    		LIMIT 1";
    	}else{
    		//商品
    		$sql = "UPDATE " . $GLOBALS['ecs']->table('goods_store') ."
    		SET goods_number = goods_number $number
    		WHERE store_id = '$store_id'
    		AND goods_id = '$good_id'
    		LIMIT 1";
    	}
        $store_query = $GLOBALS['db']->query($sql);
    }

    /* 处理货品库存 */
    $products_query = true;
    if (!empty($product_id))
    {
        $sql = "UPDATE " . $GLOBALS['ecs']->table('products') ."
                SET product_number = product_number $number
                WHERE goods_id = '$good_id'
                AND product_id = '$product_id'
                LIMIT 1";
        $products_query = $GLOBALS['db']->query($sql);
    }
    
    

    /* 处理商品库存 */
    $sql = "UPDATE " . $GLOBALS['ecs']->table('goods') ."
            SET goods_number = goods_number $number
            WHERE goods_id = '$good_id'
            LIMIT 1";
    $query = $GLOBALS['db']->query($sql);
    
    if ($query && $products_query && $products_store_query)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 取得支付方式id列表
 * @param   bool    $is_cod 是否货到付款
 * @return  array
 */
function payment_id_list($is_cod)
{
    $sql = "SELECT pay_id FROM " . $GLOBALS['ecs']->table('payment');
    if ($is_cod)
    {
        $sql .= " WHERE is_cod = 1";
    }
    else
    {
        $sql .= " WHERE is_cod = 0";
    }

    return $GLOBALS['db']->getCol($sql);
}


/**
 * 根据订单状态，配送状态，付款状态，是否货到付款获取综合状态
 * @param unknown $order_status
 * @param unknown $shipping_status
 * @param unknown $pay_status
 * @param unknown $payment_is_cod
 * @return string
 */
function get_order_cs_status($order_status,$shipping_status,$pay_status,$payment_is_cod=0){
	if(in_array($order_status, array(OS_CONFIRMED, OS_SPLITED)) 
			&& in_array($shipping_status, array(SS_SHIPPED, SS_RECEIVED)) 
			&& in_array($pay_status, array(PS_PAYED, PS_PAYING))){
		return CS_FINISHED;
	}else if(in_array($order_status, array(OS_CONFIRMED, OS_SPLITED, OS_SPLITING_PART))
			&& in_array($shipping_status, array(SS_UNSHIPPED, SS_PREPARING, SS_SHIPPED_ING))
			&& (in_array($pay_status, array(PS_PAYED, PS_PAYING)) || $payment_is_cod)){
		return CS_AWAIT_SHIP;
	}else if(in_array($order_status, array(OS_CONFIRMED, OS_SPLITED))
			&& (in_array($shipping_status, array(SS_SHIPPED, SS_RECEIVED)) || !$payment_is_cod)
			&& ($pay_status == PS_UNPAYED)){
		return CS_AWAIT_PAY;
	}else if($pay_status==PS_PAYING){
		return PS_PAYING;
	}else if($shipping_status==OS_SHIPPED_PART){
		return OS_SHIPPED_PART;
	}else{
		return $order_status;
	}
}

/**
 * 生成查询订单的sql
 * @param   string  $type   类型
 * @param   string  $alias  order表的别名（包括.例如 o.）
 * @return  string
 */
function order_query_sql($type = 'finished', $alias = '')
{
    /* 已完成订单 */
    if ($type == 'finished')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
               " AND {$alias}shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) .
               " AND {$alias}pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) . " ";
    }
    /* 待发货订单 */
    elseif ($type == 'await_ship')
    {
        return " AND   {$alias}order_status " .
                 db_create_in(array(OS_CONFIRMED, OS_SPLITED, OS_SPLITING_PART)) .
               " AND   {$alias}shipping_status " .
                 db_create_in(array(SS_UNSHIPPED, SS_PREPARING, SS_SHIPPED_ING)) .
               " AND ( {$alias}pay_status " . db_create_in(array(PS_PAYED, PS_PAYING)) . " OR {$alias}pay_id " . db_create_in(payment_id_list(true)) . ") ";
    }
    /* 待付款订单 */
    elseif ($type == 'await_pay')
    {
        return " AND   {$alias}order_status " . db_create_in(array(OS_CONFIRMED, OS_SPLITED)) .
               " AND   {$alias}pay_status = '" . PS_UNPAYED . "'" .
               " AND ( {$alias}shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) . " OR {$alias}pay_id " . db_create_in(payment_id_list(false)) . ") ";
    }
    /* 未确认订单 */
    elseif ($type == 'unconfirmed')
    {
        return " AND {$alias}order_status = '" . OS_UNCONFIRMED . "' ";
    }
    /* 未处理订单：用户可操作 */
    elseif ($type == 'unprocessed')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_UNCONFIRMED, OS_CONFIRMED)) .
               " AND {$alias}shipping_status = '" . SS_UNSHIPPED . "'" .
               " AND {$alias}pay_status = '" . PS_UNPAYED . "' ";
    }
    /* 未付款未发货订单：管理员可操作 */
    elseif ($type == 'unpay_unship')
    {
        return " AND {$alias}order_status " . db_create_in(array(OS_UNCONFIRMED, OS_CONFIRMED)) .
               " AND {$alias}shipping_status " . db_create_in(array(SS_UNSHIPPED, SS_PREPARING)) .
               " AND {$alias}pay_status = '" . PS_UNPAYED . "' ";
    }
    /* 已发货订单：不论是否付款 */
    elseif ($type == 'shipped')
    {
        return " AND {$alias}order_status = '" . OS_CONFIRMED . "'" .
               " AND {$alias}shipping_status " . db_create_in(array(SS_SHIPPED, SS_RECEIVED)) . " ";
    }
    else
    {
        die('函数 order_query_sql 参数错误');
    }
}

/**
 * 生成查询订单总金额的字段
 * @param   string  $alias  order表的别名（包括.例如 o.）
 * @return  string
 */
function order_amount_field($alias = '')
{
    return "   {$alias}goods_amount + {$alias}tax + {$alias}shipping_fee" .
           " + {$alias}insure_fee + {$alias}pay_fee + {$alias}pack_fee" .
           " + {$alias}card_fee ";
}

/**
 * 生成计算应付款金额的字段
 * @param   string  $alias  order表的别名（包括.例如 o.）
 * @return  string
 */
function order_due_field($alias = '')
{
    return order_amount_field($alias) .
            " - {$alias}money_paid - {$alias}surplus - {$alias}integral_money" .
            " - {$alias}bonus - {$alias}discount ";
}

/**
 * 计算折扣：根据购物车和优惠活动
 * @return  float   折扣
 */
function compute_discount()
{
    /* 查询优惠活动 */
    $now = gmtime();
    $user_rank = ',' . $_SESSION['user_rank'] . ',';
    $sql = "SELECT *" .
            "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE start_time <= '$now'" .
            " AND end_time >= '$now'" .
            " AND CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'" .
            " AND act_type " . db_create_in(array(FAT_DISCOUNT, FAT_PRICE));
    $favourable_list = $GLOBALS['db']->getAll($sql);
    if (!$favourable_list)
    {
        return 0;
    }

    /* 查询购物车商品 */
    $sql = "SELECT c.goods_id, c.goods_price * c.goods_number AS subtotal, g.cat_id, g.brand_id " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.goods_id = g.goods_id " .
            "AND c.session_id = '" . SESS_ID . "' " .
            "AND c.parent_id = 0 " .
            "AND c.is_gift = 0 " .
            "AND c.is_checked = 1 " .
            "AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $goods_list = $GLOBALS['db']->getAll($sql);
    if (!$goods_list)
    {
        return 0;
    }

    /* 初始化折扣 */
    $discount = 0;
    $favourable_name = array();

    /* 循环计算每个优惠活动的折扣 */
    foreach ($favourable_list as $favourable)
    {
        $total_amount = 0;
        if ($favourable['act_range'] == FAR_ALL)
        {
            foreach ($goods_list as $goods)
            {
                $total_amount += $goods['subtotal'];
            }
        }
        elseif ($favourable['act_range'] == FAR_CATEGORY)
        {
            /* 找出分类id的子分类id */
            $id_list = array();
            $raw_id_list = explode(',', $favourable['act_range_ext']);
            foreach ($raw_id_list as $id)
            {
                $id_list = array_merge($id_list, array_keys(cat_list($id, 0, false)));
            }
            $ids = join(',', array_unique($id_list));

            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $ids . ',', ',' . $goods['cat_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        elseif ($favourable['act_range'] == FAR_BRAND)
        {
            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['brand_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        elseif ($favourable['act_range'] == FAR_GOODS)
        {
            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['goods_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        else
        {
            continue;
        }

        /* 如果金额满足条件，累计折扣 */
        if ($total_amount > 0 && $total_amount >= $favourable['min_amount'] && ($total_amount <= $favourable['max_amount'] || $favourable['max_amount'] == 0))
        {
            if ($favourable['act_type'] == FAT_DISCOUNT)
            {
                $discount += $total_amount * (1 - $favourable['act_type_ext'] / 100);

                $favourable_name[] = $favourable['act_name'];
            }
            elseif ($favourable['act_type'] == FAT_PRICE)
            {
                $discount += $favourable['act_type_ext'];

                $favourable_name[] = $favourable['act_name'];
            }
        }
    }

    return array('discount' => $discount, 'name' => $favourable_name);
}

/**
 * 取得购物车该赠送的积分数
 * @return  int     积分数
 */
function get_give_integral($is_checked)
{
        $sql = "SELECT ROUND(SUM(c.goods_number * IF(g.give_integral > -1, g.give_integral, IF(cat.give_integral > -1, cat.give_integral, c.goods_price/cat.give_integral_rate))),0)" .
                " FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " .
                          $GLOBALS['ecs']->table('goods') . " AS g, " .
                          $GLOBALS['ecs']->table('category') . " AS cat " .
                " WHERE c.goods_id = g.goods_id " .
                "AND c.session_id = '" . SESS_ID . "' " .
                "AND c.goods_id > 0 " .
                "AND g.cat_id = cat.cat_id " .
                "AND c.parent_id = 0 " .
                "AND c.rec_type = 0 " .
                "AND c.is_gift = 0";

        if(isset($is_checked)){
        	$sql = $sql." AND c.is_checked = '$is_checked'";
        }
        
        return intval($GLOBALS['db']->getOne($sql));
}

/**
 * 取得某订单应该赠送的积分数
 * @param   array   $order  订单
 * @return  int     积分数
 */
function integral_to_give($order)
{
    /* 判断是否团购 */
    if ($order['extension_code'] == 'group_buy')
    {
        include_once(ROOT_PATH . 'includes/lib_goods.php');
        $group_buy = group_buy_info(intval($order['extension_id']));

        return array('custom_points' => $group_buy['gift_integral'], 'rank_points' => $order['goods_amount']);
    }
    else
    {
        $sql = "SELECT ROUND(SUM(og.goods_number * IF(g.give_integral > -1, g.give_integral, IF(cat.give_integral > -1, cat.give_integral, og.goods_price/cat.give_integral_rate))),0) AS custom_points, ROUND(SUM(og.goods_number * IF(g.rank_integral > -1, g.rank_integral, IF(cat.rank_integral > -1, cat.rank_integral, og.goods_price * 0.5))),0) AS rank_points " .
                "FROM " . $GLOBALS['ecs']->table('order_goods') . " AS og, " .
                          $GLOBALS['ecs']->table('goods') . " AS g, " .
                          $GLOBALS['ecs']->table('category') . " AS cat " .
                "WHERE og.goods_id = g.goods_id " .
                "AND og.order_id = '$order[order_id]' " .
                "AND g.cat_id = cat.cat_id " .
                "AND og.goods_id > 0 " .
                "AND og.parent_id = 0 " .
                "AND og.is_gift = 0 AND og.extension_code != 'package_buy'";

        return $GLOBALS['db']->getRow($sql);
    }
}

/**
 * 发红包：发货时发红包
 * @param   int     $order_id   订单号
 * @return  bool
 */
function send_order_bonus($order_id)
{
    /* 取得订单应该发放的红包 */
    $bonus_list = order_bonus($order_id);

    /* 如果有红包，统计并发送 */
    if ($bonus_list)
    {
        /* 用户信息 */
        $sql = "SELECT u.user_id, u.user_name, u.email " .
                "FROM " . $GLOBALS['ecs']->table('order_info') . " AS o, " .
                          $GLOBALS['ecs']->table('users') . " AS u " .
                "WHERE o.order_id = '$order_id' " .
                "AND o.user_id = u.user_id ";
        $user = $GLOBALS['db']->getRow($sql);

        /* 统计 */
        $count = 0;
        $money = '';
        foreach ($bonus_list AS $bonus)
        {
            $count += $bonus['number'];
            $money .= price_format($bonus['type_money']) . ' [' . $bonus['number'] . '], ';

            /* 修改用户红包 */
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('user_bonus') . " (bonus_type_id, user_id) " .
                    "VALUES('$bonus[type_id]', '$user[user_id]')";
            for ($i = 0; $i < $bonus['number']; $i++)
            {
                if (!$GLOBALS['db']->query($sql))
                {
                    return $GLOBALS['db']->errorMsg();
                }
            }
        }

        /* 如果有红包，发送邮件 */
        if ($count > 0)
        {
            $tpl = get_mail_template('send_bonus');
            $GLOBALS['smarty']->assign('user_name', $user['user_name']);
            $GLOBALS['smarty']->assign('count', $count);
            $GLOBALS['smarty']->assign('money', $money);
            $GLOBALS['smarty']->assign('shop_name', $GLOBALS['_CFG']['shop_name']);
            $GLOBALS['smarty']->assign('send_date', local_date($GLOBALS['_CFG']['date_format']));
            $GLOBALS['smarty']->assign('sent_date', local_date($GLOBALS['_CFG']['date_format']));
            $content = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
            send_mail($user['user_name'], $user['email'], $tpl['template_subject'], $content, $tpl['is_html']);
        }
    }

    return true;
}

/**
 * 返回订单发放的红包
 * @param   int     $order_id   订单id
 */
function return_order_bonus($order_id)
{
    /* 取得订单应该发放的红包 */
    $bonus_list = order_bonus($order_id);

    /* 删除 */
    if ($bonus_list)
    {
        /* 取得订单信息 */
        $order = order_info($order_id);
        $user_id = $order['user_id'];

        foreach ($bonus_list AS $bonus)
        {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('user_bonus') .
                    " WHERE bonus_type_id = '$bonus[type_id]' " .
                    "AND user_id = '$user_id' " .
                    "AND order_id = '0' LIMIT " . $bonus['number'];
            $GLOBALS['db']->query($sql);
        }
    }
}

/**
 * 取得订单应该发放的红包
 * @param   int     $order_id   订单id
 * @return  array
 */
function order_bonus($order_id)
{
    /* 查询按商品发的红包 */
    $day    = getdate();
    $today  = local_mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);

    $sql = "SELECT b.type_id, b.type_money, SUM(o.goods_number) AS number " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') . " AS o, " .
                      $GLOBALS['ecs']->table('goods') . " AS g, " .
                      $GLOBALS['ecs']->table('bonus_type') . " AS b " .
            " WHERE o.order_id = '$order_id' " .
            " AND o.is_gift = 0 " .
            " AND o.goods_id = g.goods_id " .
            " AND g.bonus_type_id = b.type_id " .
            " AND b.send_type = '" . SEND_BY_GOODS . "' " .
            " AND b.send_start_date <= '$today' " .
            " AND b.send_end_date >= '$today' " .
            " GROUP BY b.type_id ";
    $list = $GLOBALS['db']->getAll($sql);

    /* 查询定单中非赠品总金额 */
    $amount = order_amount($order_id, false);

    /* 查询订单日期 */
    $sql = "SELECT add_time " .
            " FROM " . $GLOBALS['ecs']->table('order_info') .
            " WHERE order_id = '$order_id' LIMIT 1";
    $order_time = $GLOBALS['db']->getOne($sql);

    /* 查询按订单发的红包 */
    $sql = "SELECT type_id, type_money, IFNULL(FLOOR('$amount' / min_amount), 1) AS number " .
            "FROM " . $GLOBALS['ecs']->table('bonus_type') .
            "WHERE send_type = '" . SEND_BY_ORDER . "' " .
            "AND send_start_date <= '$order_time' " .
            "AND send_end_date >= '$order_time' ";
    $list = array_merge($list, $GLOBALS['db']->getAll($sql));

    return $list;
}

/**
 * 计算购物车中的商品能享受红包支付的总额
 * @return  float   享受红包支付的总额
 */
function compute_discount_amount()
{
    /* 查询优惠活动 */
    $now = gmtime();
    $user_rank = ',' . $_SESSION['user_rank'] . ',';
    $sql = "SELECT *" .
            "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE start_time <= '$now'" .
            " AND end_time >= '$now'" .
            " AND CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'" .
            " AND act_type " . db_create_in(array(FAT_DISCOUNT, FAT_PRICE));
    $favourable_list = $GLOBALS['db']->getAll($sql);
    if (!$favourable_list)
    {
        return 0;
    }

    /* 查询购物车商品 */
    $sql = "SELECT c.goods_id, c.goods_price * c.goods_number AS subtotal, g.cat_id, g.brand_id " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.goods_id = g.goods_id " .
            "AND c.session_id = '" . SESS_ID . "' " .
            "AND c.parent_id = 0 " .
            "AND c.is_gift = 0 " .
            "AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $goods_list = $GLOBALS['db']->getAll($sql);
    if (!$goods_list)
    {
        return 0;
    }

    /* 初始化折扣 */
    $discount = 0;
    $favourable_name = array();

    /* 循环计算每个优惠活动的折扣 */
    foreach ($favourable_list as $favourable)
    {
        $total_amount = 0;
        if ($favourable['act_range'] == FAR_ALL)
        {
            foreach ($goods_list as $goods)
            {
                $total_amount += $goods['subtotal'];
            }
        }
        elseif ($favourable['act_range'] == FAR_CATEGORY)
        {
            /* 找出分类id的子分类id */
            $id_list = array();
            $raw_id_list = explode(',', $favourable['act_range_ext']);
            foreach ($raw_id_list as $id)
            {
                $id_list = array_merge($id_list, array_keys(cat_list($id, 0, false)));
            }
            $ids = join(',', array_unique($id_list));

            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $ids . ',', ',' . $goods['cat_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        elseif ($favourable['act_range'] == FAR_BRAND)
        {
            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['brand_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        elseif ($favourable['act_range'] == FAR_GOODS)
        {
            foreach ($goods_list as $goods)
            {
                if (strpos(',' . $favourable['act_range_ext'] . ',', ',' . $goods['goods_id'] . ',') !== false)
                {
                    $total_amount += $goods['subtotal'];
                }
            }
        }
        else
        {
            continue;
        }
        if ($total_amount > 0 && $total_amount >= $favourable['min_amount'] && ($total_amount <= $favourable['max_amount'] || $favourable['max_amount'] == 0))
        {
            if ($favourable['act_type'] == FAT_DISCOUNT)
            {
                $discount += $total_amount * (1 - $favourable['act_type_ext'] / 100);
            }
            elseif ($favourable['act_type'] == FAT_PRICE)
            {
                $discount += $favourable['act_type_ext'];
            }
        }
    }


    return $discount;
}

/**
 * 添加礼包到购物车
 *
 * @access  public
 * @param   integer $package_id   礼包编号
 * @param   integer $num          礼包数量
 * @return  boolean
 */
function add_package_to_cart($package_id, $num = 1)
{
    $GLOBALS['err']->clean();

    /* 取得礼包信息 */
    $package = get_package_info($package_id);

    if (empty($package))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['goods_not_exists'], ERR_NOT_EXISTS);

        return false;
    }

    /* 是否正在销售 */
    if ($package['is_on_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['not_on_sale'], ERR_NOT_ON_SALE);

        return false;
    }

    /* 现有库存是否还能凑齐一个礼包 */
    if ($GLOBALS['_CFG']['use_storage'] == '1' && judge_package_stock($package_id))
    {
        $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], 1), ERR_OUT_OF_STOCK);

        return false;
    }

    /* 检查库存 */
//    if ($GLOBALS['_CFG']['use_storage'] == 1 && $num > $package['goods_number'])
//    {
//        $num = $goods['goods_number'];
//        $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $num), ERR_OUT_OF_STOCK);
//
//        return false;
//    }

    /* 初始化要插入购物车的基本件数据 */
    $parent = array(
        'user_id'       => $_SESSION['user_id'],
        'session_id'    => SESS_ID,
        'goods_id'      => $package_id,
        'goods_sn'      => '',
        'goods_name'    => addslashes($package['package_name']),
        'market_price'  => $package['market_package'],
        'goods_price'   => $package['package_price'],
        'goods_number'  => $num,
        'goods_attr'    => '',
        'goods_attr_id' => '',
        'is_real'       => $package['is_real'],
        'extension_code'=> 'package_buy',
        'is_gift'       => 0,
        'rec_type'      => CART_GENERAL_GOODS,
    	'is_checked'    => 1	
    );

    /* 如果数量不为0，作为基本件插入 */
    if ($num > 0)
    {
         /* 检查该商品是否已经存在在购物车中 */
        $sql = "SELECT goods_number FROM " .$GLOBALS['ecs']->table('cart').
                " WHERE session_id = '" .SESS_ID. "' AND goods_id = '" . $package_id . "' ".
                " AND parent_id = 0 AND extension_code = 'package_buy' " .
                " AND rec_type = '" . CART_GENERAL_GOODS . "'";

        $row = $GLOBALS['db']->getRow($sql);

        if($row) //如果购物车已经有此物品，则更新
        {
            $num += $row['goods_number'];
            if ($GLOBALS['_CFG']['use_storage'] == 0 || $num > 0)
            {
                $sql = "UPDATE " . $GLOBALS['ecs']->table('cart') . " SET goods_number = '" . $num . "'" .
                       " WHERE session_id = '" .SESS_ID. "' AND goods_id = '$package_id' ".
                       " AND parent_id = 0 AND extension_code = 'package_buy' " .
                       " AND rec_type = '" . CART_GENERAL_GOODS . "'";
                $GLOBALS['db']->query($sql);
            }
            else
            {
                $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $num), ERR_OUT_OF_STOCK);
                return false;
            }
        }
        else //购物车没有此物品，则插入
        {
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('cart'), $parent, 'INSERT');
        }
    }

    /* 把赠品删除 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '" . SESS_ID . "' AND is_gift <> 0";
    $GLOBALS['db']->query($sql);

    //TODO: 重新计算赠品 added by tiger.guo 20151225
    
    return true;
}

/**
 * 得到新发货单号
 * @return  string
 */
function get_delivery_sn()
{
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);

    return date('YmdHi') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * 得到新退货申请单号
 * @return  string
 */
function get_order_back_sn()
{
	/* 选择一个随机的方案 */
	mt_srand((double) microtime() * 1000000);

	return date('YmdHi') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * 检查礼包内商品的库存
 * @return  boolen
 */
function judge_package_stock($package_id, $package_num = 1)
{
    $sql = "SELECT goods_id, product_id, goods_number
            FROM " . $GLOBALS['ecs']->table('package_goods') . "
            WHERE package_id = '" . $package_id . "'";
    $row = $GLOBALS['db']->getAll($sql);
    if (empty($row))
    {
        return true;
    }

    /* 分离货品与商品 */
    $goods = array('product_ids' => '', 'goods_ids' => '');
    foreach ($row as $value)
    {
        if ($value['product_id'] > 0)
        {
            $goods['product_ids'] .= ',' . $value['product_id'];
            continue;
        }

        $goods['goods_ids'] .= ',' . $value['goods_id'];
    }

    /* 检查货品库存 */
    if ($goods['product_ids'] != '')
    {
        $sql = "SELECT p.product_id
                FROM " . $GLOBALS['ecs']->table('products') . " AS p, " . $GLOBALS['ecs']->table('package_goods') . " AS pg
                WHERE pg.product_id = p.product_id
                AND pg.package_id = '$package_id'
                AND pg.goods_number * $package_num > p.product_number
                AND p.product_id IN (" . trim($goods['product_ids'], ',') . ")";
        $row = $GLOBALS['db']->getAll($sql);

        if (!empty($row))
        {
            return true;
        }
    }

    /* 检查商品库存 */
    if ($goods['goods_ids'] != '')
    {
        $sql = "SELECT g.goods_id
                FROM " . $GLOBALS['ecs']->table('goods') . "AS g, " . $GLOBALS['ecs']->table('package_goods') . " AS pg
                WHERE pg.goods_id = g.goods_id
                AND pg.goods_number * $package_num > g.goods_number
                AND pg.package_id = '" . $package_id . "'
                AND pg.goods_id IN (" . trim($goods['goods_ids'], ',') . ")";
        $row = $GLOBALS['db']->getAll($sql);

        if (!empty($row))
        {
            return true;
        }
    }

    return false;
}

/*设置某个商品在在某些地区可以包邮，某些地区不能*/  
/**
 * 购物车中不包邮商品数量
 * @param unknown $region_id_list
 */
function get_not_free_shipping_count($region_id_list)
{
	/* 取得购物类型 */
	$flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;
	
	$sql = 'SELECT c.goods_id FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c, ' .
                $GLOBALS['ecs']->table('goods_free_shipping_area') . ' AS r, ' .
                $GLOBALS['ecs']->table('goods') . ' AS g ' .
            'WHERE r.region_id ' . db_create_in($region_id_list) .
            " AND g.is_shipping = 1 AND c.goods_id = r.goods_id AND c.goods_id = g.goods_id ".
            " AND rec_type = '$flow_type' AND c.is_checked = 1 AND session_id = '".SESS_ID."' AND c.extension_code != 'package_buy'";
			
    $goods_ids =  $GLOBALS['db']->getCol($sql);
	$where =" AND rec_type = '$flow_type' AND c.is_checked = 1 AND session_id = '".SESS_ID."' AND c.goods_id " . db_create_in($goods_ids) ."";
	$where = str_replace('IN','NOT IN',$where);
	
	
    $sql = 'SELECT count(*) FROM ' . $GLOBALS['ecs']->table('cart') . ' AS c ' .
            " where 1 $where";
			
			
    return $GLOBALS['db']->getOne($sql);
}
/*设置某个商品在在某些地区可以包邮，某些地区不能end*/  


/**
 * 选中，反选购物车内容
 * @param unknown $rec_id
 * @param unknown $is_checked
 */
function check_cart_goods($rec_id,$is_checked){	
	//TODO:更新 配件的选中状态
	
	//更新赠品的选中状态(优惠活动中的赠品更新不包含)
	$sql = "SELECT goods_id FROM " . $GLOBALS['ecs']->table('cart') . "WHERE rec_id = '" . $rec_id . "'" ;
	$goods_id = $GLOBALS['db']->getOne($sql);
	$sql = "UPDATE " . $GLOBALS['ecs']->table('cart') . " SET " .
			"is_checked = '" .$is_checked . "' " .
			"WHERE is_gift = '" . $goods_id . "' AND extension_code <> 'package_buy' AND rec_type = '".CART_GENERAL_GOODS."'" ;
	$GLOBALS['db']->query($sql);
	
	//更新主商品的选中状态
	$sql = "UPDATE " . $GLOBALS['ecs']->table('cart') . " SET " .
			"is_checked = '" .$is_checked . "' " .
			"WHERE rec_id = '" . $rec_id . "'" ;
	return $GLOBALS['db']->query($sql);
}

/**
 * 选中，反选全部购物车内容
 * @param unknown $rec_id
 * @param unknown $is_checked
 */
function check_all_cart_goods($is_checked){
	$sql = "UPDATE " . $GLOBALS['ecs']->table('cart') . " SET " .
			"is_checked = '" .$is_checked . "' " .
			"WHERE session_id = '" . SESS_ID . "'" ;
	return $GLOBALS['db']->query($sql);
}

/**
 *  获取门店自提的自提点
 *  @param   array   $region_id_list     收货人地区id数组
 */
function get_pick_up_point_list($region_id_list){
	$sql = "SELECT point_id, point_name, point_addr, regions, status FROM ". $GLOBALS['ecs']->table('pick_up_point');
	$rows = $GLOBALS['db']->getAll($sql);
	$arr= array();
	foreach ($rows as $key => $row) {
		$support_pick_up = false;
		if(!empty($row['regions']) && $region_id_list){
			$pick_up_area_ids = explode(',', $row['regions']);
			foreach ($region_id_list as $key => $value) {
				if(in_array($value, $pick_up_area_ids)){
					$support_pick_up = true;
					break;
				}
			}
		}
		if($support_pick_up){
			$arr[] = $row;
		}
	}
	return $arr;	
}

/**
 * 更新购物车中的商品数量 - 某个商品的数量
 * @param unknown $rec_id
 * @param unknown $goods_number
 */
function flow_update_cart_goods($rec_id,$goods_number)
{

	$key = $rec_id;
	$val = $goods_number;
	/* 处理 */
	$val = intval(make_semiangle($val));
	if ($val <= 0 || !is_numeric($key))
	{
		continue;
	}

	$result = jude_cart_goods($rec_id, $goods_number);
	if(!$result['success']){
		//检查不合格返回
		return $result;
	}

	/* 查询：检查该项是否为基本件 以及是否存在配件 */
	/* 此处配件是指添加商品时附加的并且是设置了优惠价格的配件 此类配件都有parent_id goods_number为1 */
	$sql = "SELECT b.goods_number, b.rec_id
                FROM " .$GLOBALS['ecs']->table('cart') . " a, " .$GLOBALS['ecs']->table('cart') . " b
                WHERE a.rec_id = '$key'
                AND a.session_id = '" . SESS_ID . "'
                AND a.extension_code <> 'package_buy'
                AND b.parent_id = a.goods_id
                AND b.session_id = '" . SESS_ID . "'";

	$offers_accessories_res = $GLOBALS['db']->query($sql);

	//订货数量大于0
	if ($val > 0)
	{
		/* 判断是否为超出数量的优惠价格的配件 删除*/
		$row_num = 1;
		while ($offers_accessories_row = $GLOBALS['db']->fetchRow($offers_accessories_res))
		{
			if ($row_num > $val)
			{
				$sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
				" WHERE session_id = '" . SESS_ID . "' " .
				"AND rec_id = '" . $offers_accessories_row['rec_id'] ."' LIMIT 1";
				$GLOBALS['db']->query($sql);
			}

			$row_num ++;
		}
		
		//根据$rec_id获取商品编号 added by tiger.tuo 20170311
		$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('cart') ." WHERE rec_id = '$rec_id'";
		$goods = $GLOBALS['db']->getRow($sql);		

		/* 处理超值礼包 */
		if ($goods['extension_code'] == 'package_buy')
		{
			//更新购物车中的商品数量
			$sql = "UPDATE " .$GLOBALS['ecs']->table('cart').
			" SET goods_number = '$val' WHERE rec_id='$key' AND session_id='" . SESS_ID . "'";
		}
		/* 处理普通商品或非优惠的配件 */
		else
		{
			$attr_id    = empty($goods['goods_attr_id']) ? array() : explode(',', $goods['goods_attr_id']);
			
			//获取最终商品价格
			$goods_price = get_final_price($goods['goods_id'], $val, true, $attr_id,$goods['product_id']);

			//更新购物车中的商品数量
			$sql = "UPDATE " .$GLOBALS['ecs']->table('cart').
			" SET goods_number = '$val', goods_price = '$goods_price' WHERE rec_id='$key' AND session_id='" . SESS_ID . "'";
		}
	}
	//订货数量等于0
	else
	{
		/* 如果是基本件并且有优惠价格的配件则删除优惠价格的配件 */
		while ($offers_accessories_row = $GLOBALS['db']->fetchRow($offers_accessories_res))
		{
			$sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
			" WHERE session_id = '" . SESS_ID . "' " .
			"AND rec_id = '" . $offers_accessories_row['rec_id'] ."' LIMIT 1";
			$GLOBALS['db']->query($sql);
		}

		$sql = "DELETE FROM " .$GLOBALS['ecs']->table('cart').
		" WHERE rec_id='$key' AND session_id='" .SESS_ID. "'";
	}

	$GLOBALS['db']->query($sql);

	//更新赠品的数量
	add_goods_gift_to_cart($goods['goods_id']);

	return ['success'=>true,'msg'=>''];
}

/**
 * 检查购物车中商品数量是否合法
 * @param unknown $rec_id
 * @param unknown $goods_number
 */
function jude_cart_goods($rec_id,$goods_number)
{

	$key = $rec_id;
	$val = $goods_number;
	/* 处理 */
	$val = intval(make_semiangle($val));
	if ($val <= 0 || !is_numeric($key))
	{
		continue;
	}

	//查询：
	$sql = "SELECT `goods_id`, `goods_attr_id`, `product_id`, `extension_code` FROM" .$GLOBALS['ecs']->table('cart').
	" WHERE rec_id='$key' AND session_id='" . SESS_ID . "'";
	$goods = $GLOBALS['db']->getRow($sql);

	$sql = "SELECT g.goods_name, g.goods_number, g.is_promote, g.promote_start_date, g.promote_end_date, g.promote_limit_num ".
			"FROM " .$GLOBALS['ecs']->table('goods'). " AS g, ".
			$GLOBALS['ecs']->table('cart'). " AS c ".
			"WHERE g.goods_id = c.goods_id AND c.rec_id = '$key'";
	$row = $GLOBALS['db']->getRow($sql);

	//查询：系统启用了库存，检查输入的商品数量是否有效
	if (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] != 'package_buy')
	{
		if ($row['goods_number'] < $val)
		{

			/* show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
			 $row['goods_number'], $row['goods_number']));
				exit; */
			return ['success'=>false,'msg'=>sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
					$row['goods_number'], $row['goods_number'])];
		}
			
		//特价商品限购
		$time = isset($time)? $time : gmtime();
		if($row['is_promote']==1 && $row['promote_start_date']<=$time && $row['promote_end_date']>=$time){
			if ($row['promote_limit_num']>0 && $row['promote_limit_num'] < $val){
				/* return ['success'=>false,'msg'=>sprintf($GLOBALS['_LANG']['promote_limit'], $row['goods_name'],
						$row['promote_limit_num'], $row['promote_limit_num'])]; */
				return ['success'=>false,'msg'=>sprintf($GLOBALS['_LANG']['promote_limit_1'], $row['promote_limit_num'])];
			}
		}
			
		/* 是货品 */
		$goods['product_id'] = trim($goods['product_id']);
		if (!empty($goods['product_id']))
		{
			/* 查询规格名称 start */
			$sql = "SELECT c.attr_name,b.attr_value from ".$GLOBALS['ecs']->table('products_attr')." AS a
					LEFT JOIN ".$GLOBALS['ecs']->table('goods_attr')." AS b ON (a.goods_attr_id = b.goods_attr_id)
					LEFT JOIN ".$GLOBALS['ecs']->table('attribute')." c ON (b.attr_id = c.attr_id)
					WHERE a.product_id = '".$goods['product_id']."'";
			$attr_list = $GLOBALS['db']->getAll($sql);
			foreach ($attr_list AS $attr)
			{
				$row['goods_name'] .= (' [' .$attr['attr_name'].':'. $attr['attr_value'] . '] ');
			}
			/* 查询规格名称 end */

			$sql = "SELECT product_number FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '" . $goods['goods_id'] . "' AND product_id = '" . $goods['product_id'] . "'";

			$product_number = $GLOBALS['db']->getOne($sql);

			if ($product_number < $val)
			{
				return ['success'=>false,'msg'=>sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
						$product_number['product_number'], $product_number['product_number'])];
				/* show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
				 $product_number['product_number'], $product_number['product_number']));
				exit; */
			}
		}
	}
	elseif (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] == 'package_buy')
	{
		if (judge_package_stock($goods['goods_id'], $val))
		{
			/* show_message($GLOBALS['_LANG']['package_stock_insufficiency']);
				exit; */
			return ['success'=>false,'msg'=>$GLOBALS['_LANG']['package_stock_insufficiency']];
		}
	}

	return ['success'=>true,'msg'=>''];
}

?>