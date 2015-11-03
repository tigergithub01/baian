<?php

/**
 * ECSHOP 招商银行一网通直付插件
 * ============================================================================
 * 版权所有 2005-2008 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: sxc_shop $
 * $Id: yeepay_cmbchina.php 15797 2009-04-15 10:46:09Z sxc_shop $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/cmb.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'cmbchina_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'LITING';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.cmbchina.com/';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.1';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'yp_account', 'type' => 'text', 'value' => ''),
        array('name' => 'yp_key',     'type' => 'text', 'value' => ''),
    );

    return;
}

/**
 * 类
 */
class cmb
{
    
	function __construct()
	{
		// $this->yeepay_cmbchina();
	}
	
    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function cmb()
    {
        //$this->frpid = 'CMBCHINA-NET';
    }

    

    /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
        $data_merchant_id = $payment['yp_account'];
        $data_order_id    = $order['order_sn'];
        $data_amount      = $order['order_amount'];
        //$message_type     = 'Buy';
       // $data_cur         = 'CNY';
        //$product_id       = '';
        //$product_cat      = '';
        //$product_desc     = '';
        //$address_flag     = '0';

        $data_return_url  = return_url(basename(__FILE__, '.php'));

        $data_branchid     = $payment['yp_key'];
        $data_pay_account = $payment['yp_account'];
        date_default_timezone_set('UTC');
        $today = date("Ymd");
        $def_url =  $data_merchant_id . $data_order_id . $data_amount . $data_return_url.$today.$data_branchid ;
        $def_url  = "\n<form action='https://netpay.cmbchina.com/netpayment/BaseHttp.dll?PrePayC1' method='post' target='_blank'>\n";
        $def_url .= "<input type='hidden' name='CoNo' value='".$data_pay_account."'>\n";
        $def_url .= "<input type='hidden' name='BillNo' value='".$data_order_id."'>\n";
       //$def_url .= "<input type='hidden' name='BillNo' value='12312323433'>\n";
        $def_url .= "<input type='hidden' name='Amount' value='".$data_amount."'>\n";
        $def_url .= "<input type='hidden' name='MerchantUrl' value='".$data_return_url."'>\n";
       $def_url .= "<input type='hidden' name='Date' value='".$today."'>\n";
        $def_url .= "<input type='hidden' name='BranchID' value='".$data_branchid."'>\n";
        $def_url .= "<input type='submit' value='" . $GLOBALS['_LANG']['pay_button'] . "'>";
        $def_url .= "</form>\n";

        return $def_url;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        $payment        = get_payment('cmb');

        $merchant_id    = $payment['yp_account'];       // 获取商户编号
        $merchant_key   = $payment['yp_key'];           // 获取秘钥

        $succeed        = trim($_REQUEST['Succeed']);   // 获取交易结果,Y成功,N失败
        $amount         = trim($_REQUEST['Amount']);    // 获取订单金额
        $BillNo        = trim($_REQUEST['BillNo']);  // 获取订单ID
	$Msg = trim($_REQUEST['Msg']);
	//echo $Msg;
        $v_result = false;

        if (strtoupper($mac) == strtoupper($mymac))
        {
            if ($succeed == 'Y')
            {
                ///支付成功
                $v_result = true;

                //$order_id = str_replace($orderid, '', $product_id);
                order_paid($BillNo, PS_PAYED);
            }
        }

        return $v_result;
    }
}


?>