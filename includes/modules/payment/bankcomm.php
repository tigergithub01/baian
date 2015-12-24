<?php

/** 
 
 * 交通银行在线支付插件 For Ecshop 
 
 * Author: Reson 
 
 * Date: 2014/03/31 
 
 */
if (! defined ( 'IN_ECS' )) 

{
	
	die ( 'Hacking attempt' );
}

$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS ['_CFG'] ['lang'] . '/payment/bankcomm.php';

if (file_exists ( $payment_lang )) 

{
	
	global $_LANG;
	
	include_once ($payment_lang);
}

/* 模块的基本信息 */

if (isset ( $set_modules ) && $set_modules == TRUE) 

{
	
	$i = isset ( $modules ) ? count ( $modules ) : 0;
	
	/* 代码 */
	
	$modules [$i] ['code'] = basename ( __FILE__, '.php' );
	
	/* 描述对应的语言项 */
	
	$modules [$i] ['desc'] = 'bankcomm_desc';
	
	/* 是否支持货到付款 */
	
	$modules [$i] ['is_cod'] = '0';
	
	/* 是否支持在线支付 */
	
	$modules [$i] ['is_online'] = '1';
	
	/* 支付费用，由配送决定 */
	
	$modules [$i] ['pay_fee'] = '0';
	
	/* 作者 */
	
	$modules [$i] ['author'] = 'Reson';
	
	/* 网址 */
	
	$modules [$i] ['website'] = 'http://www.daixiaorui.com';
	
	/* 版本号 */
	
	$modules [$i] ['version'] = '1.0.0.0';
	
	/* 配置信息 */
	
	$modules [$i] ['config'] = array (
		array('name' => 'bankcomm_account',           'type' => 'text',   'value' => '')
	);
	
	return;
}

/**
 * 类
 */
class bankcomm 

{
	
	function __construct()
	
	{
		$this->bankcomm ();
	}
	/**
	 *
	 * 构造函数
	 *
	 *
	 *
	 * @return void
	 *
	 *
	 */
	function bankcomm() 

	{
	}
	
	
	/**
	 * 提交函数
	 */
	function get_code($order) 

	{
		
		// 获得表单传过来的数据
		$param ['interfaceVersion'] = '1.0.0.0'; // 消息版本号*
		
// 		$param ['merID'] = '301310063009501'; // 商户号 (测试号,后期可自行更改)

		$param ['merID'] = $payment['bankcomm_account']; // 商户号 
		
		$param ['orderid'] = $order ['orderid']; // 订单号*
		
		$param ['orderDate'] = local_date ( "Ymd", gmtime () ); // 商户订单日期* yyyyMMdd
		
		$param ['orderTime'] = local_date ( "His", gmtime () ); // 商户订单时间* HHmmss
		
		$param ['tranType'] = 0; // 交易类别* 0:B2C
		
		$param ['amount'] = $order ['amount']; // 订单金额*
		
		$param ['curType'] = 'CNY'; // 交易币种* 默认CNY
		
		$param ['orderContent'] = '';
		
		$param ['orderMono'] = $order ['orderMono']; // 商家备注
		
		$param ['phdFlag'] = ''; // 物流配送标志
		
		$param ['notifyType'] = 1; // 通知方式* 1 通知
		
		$param ['merURL'] = '';
		
		$param ['goodsURL'] = $order ['goodsURL']; // 取货URL
		
		$param ['jumpSeconds'] = '';
		
		$param ['payBatchNo'] = '';
		
		$param ['proxyMerName'] = '';
		
		$param ['proxyMerType'] = '';
		
		$param ['proxyMerCredentials'] = '';
		
		$param ['netType'] = 0; // 渠道编号* 0:html渠道
		
		$param ['issBankNo'] = '';
		
		$tranCode = "cb2200_sign";
		
		htmlentities ( $param ['orderMono'], "ENT_QUOTES", "utf-8" );
		
		// 连接字符串
		
		$source = '';
		
		foreach ( $param as $key => $val ) {
			
			if ($key != 'issBankNo')
				
				$source .= $val . '|';
		}
		
		$source = substr ( $source, 0, strlen ( $source ) - 1 );
		
		// 连接地址 www.111cn.net
		
		$socketUrl = "tcp://127.0.0.1:8891"; // 这里的端口根据自己配置的情况
		
		$fp = stream_socket_client ( $socketUrl, $errno, $errstr, 30 );
		
		$retMsg = "";
		
		//
		
		if (! $fp) {
			
			echo "$errstr ($errno)<br /> ";
		} else 

		{
			
			$in = "<?xml version='1.0' encoding='UTF-8'?>";
			
			$in .= "<Message>";
			
			$in .= "<TranCode>" . $tranCode . "</TranCode>";
			
			$in .= "<MsgContent>" . $source . "</MsgContent>";
			
			$in .= "</Message>";
			
			fwrite ( $fp, $in );
			
			while ( ! feof ( $fp ) ) {
				
				$retMsg = $retMsg . fgets ( $fp, 1024 );
			}
			
			fclose ( $fp );
		}
		
		// 解析返回xml
		
		$dom = new DOMDocument ();
		
		$dom->loadXML ( $retMsg );
		
		$retCode = $dom->getElementsByTagName ( 'retCode' );
		
		$retCode_value = $retCode->item ( 0 )->nodeValue;
		
		$errMsg = $dom->getElementsByTagName ( 'errMsg' );
		
		$errMsg_value = $errMsg->item ( 0 )->nodeValue;
		
		$signMsg = $dom->getElementsByTagName ( 'signMsg' );
		
		$signMsg_value = $signMsg->item ( 0 )->nodeValue;
		
		$orderUrl = $dom->getElementsByTagName ( 'orderUrl' );
		
		$orderUrl_value = $orderUrl->item ( 0 )->nodeValue;
		
		$MerchID = $dom->getElementsByTagName ( 'MerchID' );
		
		$merID = $MerchID->item ( 0 )->nodeValue;
		
		// echo "retMsg=".$retMsg;
		
		// echo $retCode_value." ".$errMsg_value." ".$signMsg_value." ".$orderUrl_value;
		
		if ($retCode_value != "0") {
			
			// echo "交易返回码：".$retCode_value."<br>";
			
			// echo "交易错误信息：" .$errMsg_value."<br>";
			
			return "交易错误信息：" . $errMsg_value . "<br>";
		} else {
			
			$param ['signMsg_value'] = $signMsg_value;
			
			$param ['orderUrl_value'] = $orderUrl_value;
			
			$form_code = $this->create_html ( $param ); // 创建提交表单
			
			return $form_code;
		}
	}
	
	/**
	 * 创建提交表单
	 */
	function create_html($param) {
		$pay_html = '<form name = "form1" method = "post" action = "' . $param ['orderUrl_value'] . '"> 
 
<input type = "hidden" name = "interfaceVersion" value = "' . $param ['interfaceVersion'] . '"> 
 
<input type = "hidden" name = "merID" value = "' . $param ['merID'] . '"> 
 
<input type = "hidden" name = "orderid" value = "' . $param ['orderid'] . '"> 
 
<input type = "hidden" name = "orderDate" value = "' . $param ['orderDate'] . '"> 
 
<input type = "hidden" name = "orderTime" value = "' . $param ['orderTime'] . '"> 
 
<input type = "hidden" name = "tranType" value = "' . $param ['tranType'] . '"> 
 
<input type = "hidden" name = "amount" value = "' . $param ['amount'] . '"> 
 
<input type = "hidden" name = "curType" value = "' . $param ['curType'] . '"> 
 
<input type = "hidden" name = "orderContent" value = "' . $param ['orderContent'] . '"> 
 
<input type = "hidden" name = "orderMono" value = "' . $param ['orderMono'] . '"> 
 
<input type = "hidden" name = "phdFlag" value = "' . $param ['phdFlag'] . '"> 
 
<input type = "hidden" name = "notifyType" value = "' . $param ['notifyType'] . '"> 
 
<input type = "hidden" name = "merURL" value = "' . $param ['merURL'] . '"> 
 
<input type = "hidden" name = "goodsURL" value = "' . $param ['goodsURL'] . '"> 
 
<input type = "hidden" name = "jumpSeconds" value = "' . $param ['jumpSeconds'] . '"> 
 
<input type = "hidden" name = "payBatchNo" value = "' . $param ['payBatchNo'] . '"> 
 
<input type = "hidden" name = "proxyMerName" value = "' . $param ['proxyMerName'] . '"> 
 
<input type = "hidden" name = "proxyMerType" value = "' . $param ['proxyMerType'] . '"> 
 
<input type = "hidden" name = "proxyMerCredentials" value = "' . $param ['proxyMerCredentials'] . '"> 
 
<input type = "hidden" name = "netType" value = "' . $param ['netType'] . '"> 
 
<input type = "hidden" name = "merSignMsg" value = "' . $param ['signMsg_value'] . '"> 
 
<input type = "hidden" name = "issBankNo" value = "' . $param ['issBankNo'] . '">//开源代码phpfensi.com 
 
<input type="submit" value=" " class="pay_button" /> 
 
</form>';
		
		return $pay_html;
	}
	
	/**
	 * 处理函数
	 */
	function respond() 

	{
		$tranCode = "cb2200_verify";
		
		$notifyMsg = $_REQUEST ["notifyMsg"];
		
		$lastIndex = strripos ( $notifyMsg, "|" );
		
		$signMsg = substr ( $notifyMsg, $lastIndex + 1 ); // 签名信息
		
		$srcMsg = substr ( $notifyMsg, 0, $lastIndex + 1 ); // 原文
		                                             
		// 连接地址
		
		$socketUrl = "tcp://127.0.0.1:8891";
		
		$fp = stream_socket_client ( $socketUrl, $errno, $errstr, 30 );
		
		$retMsg = "";
		
		if (! $fp) {
			
			// echo "$errstr ($errno)<br /> ";
			
			return false;
		} else {
			
			$in = "<?xml version='1.0' encoding='UTF-8'?>";
			
			$in .= "<Message>";
			
			$in .= "<TranCode>" . $tranCode . "</TranCode>";
			
			$in .= "<MsgContent>" . $notifyMsg . "</MsgContent>";
			
			$in .= "</Message>";
			
			fwrite ( $fp, $in );
			
			while ( ! feof ( $fp ) ) {
				
				$retMsg = $retMsg . fgets ( $fp, 1024 );
			}
			
			fclose ( $fp );
		}
		
		// 解析返回xml
		
		$dom = new DOMDocument ();
		
		$dom->loadXML ( $retMsg );
		
		$retCode = $dom->getElementsByTagName ( 'retCode' );
		
		$retCode_value = $retCode->item ( 0 )->nodeValue;
		
		$errMsg = $dom->getElementsByTagName ( 'errMsg' );
		
		$errMsg_value = $errMsg->item ( 0 )->nodeValue;
		
		$signMsg = $dom->getElementsByTagName ( 'signMsg' );
		
		$signMsg_value = $signMsg->item ( 0 )->nodeValue;
		
		if ($retCode_value != '') {
			
			// echo "交易返回码：".$retCode_value."<br>";
			
			// echo "交易错误信息：" .$errMsg_value."<br>";
			
			return false;
		} else {
			
			$arr = preg_split ( "/|{1,}/", $srcMsg );
			
			$pay_id = $arr [1];
			
			$action_note = base64_decode ( $arr [16] );
			
			// 完成订单。
			
			order_paid ( $pay_id, PS_PAYED, $action_note );
			
			// 告诉用户交易完成
			
			return true;
		}
		
		// /////////////// respond END ///////////////
	}
}

?> 