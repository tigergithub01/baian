<?php 
//file_put_contents('info_call_get.txt',serialize($_GET));


if(isset($_GET['out_trade_no']) && isset($_GET['sign'])){
	$pay_id = $_GET['out_trade_no'];
	$sign = $_GET['sign'];
//if(isset($_GET['out_trade_no']) && isset($_GET['sign'])){
//	$pay_id = $_GET['pay_id'];
//	$sign = $_GET['sign'];
	
//	if(substr(md5(md5('wxpay').$pay_id),0,10)==$sign){
		define('IN_ECS', true);
		
		require('../includes/init.php');
		require(ROOT_PATH . 'includes/lib_payment.php');
		
        	$sql = "select order_id from ".$GLOBALS['ecs']->table('order_info')." where order_sn='$pay_id'";
        	$order_info = $GLOBALS['db']->getRow($sql);
        	
        	$sql = "select log_id from ".$GLOBALS['ecs']->table('pay_log')." where order_id='$order_info[order_id]'";
        	$pay_log = $GLOBALS['db']->getRow($sql);
        	$pay_id = $pay_log['log_id'];
			//pay_log中的log_id即为$pay_id
			order_paid($pay_id, PS_PAYED);
			die('success');
//			header("Location:http://www.123121.com/");
//	}
}else{
	die('fail');
}


?>