<!--  
 * ====================================================================
 *
 *                Send.php 由网银在线技术支持提供
 *
 *  本页面接收来自上页所有订单信息,并提交支付表单信息到网线在线支付平台....
 *
 * 
 * ====================================================================
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>在线支付接口PHP版</title>

<link href="css/index.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="javascript:document.E_FORM.submit()">
<?php
//****************************************
	$v_mid = '**********';								    // 1001是网银在线的测试商户号，商户要替换为自己的商户号。

	$v_url = 'http://123121.com/chinabak/Receive.php';	// 商户自定义返回接收支付结果的页面。对应Receive.php示例。
	                                                    //参照"网银在线支付B2C系统商户接口文档v4.1.doc"中2.3.3.1
	
	$key   = '**********';								    // 参照"网银在线支付B2C系统商户接口文档v4.1.doc"中2.4.1进行设置。

	$remark2 = '[url:=http://123121.com/chinabank/AutoReceive.php]'; //服务器异步通知的接收地址。对应AutoReceive.php示例。必须要有[url:=]格式。
																//参照"网银在线支付B2C系统商户接口文档v4.1.doc"中2.3.3.2。

//****************************************


if(trim($_POST['v_oid'])<>"")					//判断是否有传递订单号
{
	   $v_oid = trim($_POST['v_oid']); 
}
else
{
	   $v_oid = date('Ymd',time())."-".$v_mid."-".date('His',time());//订单号，建议构成格式 年月日-商户号-小时分钟秒

}
	 
	$v_amount = trim($_POST['v_amount']);                   //支付金额                 
    $v_moneytype = "CNY";                                            //币种

	$text = $v_amount.$v_moneytype.$v_oid.$v_mid.$v_url.$key;        //md5加密拼凑串,注意顺序不能变
    $v_md5info = strtoupper(md5($text));                             //md5函数加密并转化成大写字母

	 $remark1 = trim($_POST['remark1']);					 //备注字段1
	 



	$v_rcvname   = trim($_POST['v_rcvname'])  ;		// 收货人
	$v_rcvaddr   = trim($_POST['v_rcvaddr'])  ;		// 收货地址
	$v_rcvtel    = trim($_POST['v_rcvtel'])   ;		// 收货人电话
	$v_rcvpost   = trim($_POST['v_rcvpost'])  ;		// 收货人邮编
	$v_rcvemail  = trim($_POST['v_rcvemail']) ;		// 收货人邮件
	$v_rcvmobile = trim($_POST['v_rcvmobile']);		// 收货人手机号

	$v_ordername   = trim($_POST['v_ordername'])  ;	// 订货人姓名
	$v_orderaddr   = trim($_POST['v_orderaddr'])  ;	// 订货人地址
	$v_ordertel    = trim($_POST['v_ordertel'])   ;	// 订货人电话
	$v_orderpost   = trim($_POST['v_orderpost'])  ;	// 订货人邮编
	$v_orderemail  = trim($_POST['v_orderemail']) ;	// 订货人邮件
	$v_ordermobile = trim($_POST['v_ordermobile']);	// 订货人手机号 

?>

<!--以下信息为标准的 HTML 格式 + PHP 语言 拼凑而成的 网银在线 支付接口标准演示页面 无需修改-->

<form method="post" name="E_FORM" action="https://Pay3.chinabank.com.cn/PayGate">
	<input type="hidden" name="v_mid"         value="<?php echo $v_mid;?>">
	<input type="hidden" name="v_oid"         value="<?php echo $v_oid;?>">
	<input type="hidden" name="v_amount"      value="<?php echo $v_amount;?>">
	<input type="hidden" name="v_moneytype"   value="<?php echo $v_moneytype;?>">
	<input type="hidden" name="v_url"         value="<?php echo $v_url;?>">
	<input type="hidden" name="v_md5info"     value="<?php echo $v_md5info;?>">
 
 <!--以下几项项为网上支付完成后，随支付反馈信息一同传给信息接收页 -->	
	
	<input type="hidden" name="remark1"       value="<?php echo $remark1;?>">
	<input type="hidden" name="remark2"       value="<?php echo $remark2;?>">



<!--以下几项只是用来记录客户信息，可以不用，不影响支付 -->
	<input type="hidden" name="v_rcvname"      value="<?php echo $v_rcvname;?>">
	<input type="hidden" name="v_rcvtel"       value="<?php echo $v_rcvtel;?>">
	<input type="hidden" name="v_rcvpost"      value="<?php echo $v_rcvpost;?>">
	<input type="hidden" name="v_rcvaddr"      value="<?php echo $v_rcvaddr;?>">
	<input type="hidden" name="v_rcvemail"     value="<?php echo $v_rcvemail;?>">
	<input type="hidden" name="v_rcvmobile"    value="<?php echo $v_rcvmobile;?>">

	<input type="hidden" name="v_ordername"    value="<?php echo $v_ordername;?>">
	<input type="hidden" name="v_ordertel"     value="<?php echo $v_ordertel;?>">
	<input type="hidden" name="v_orderpost"    value="<?php echo $v_orderpost;?>">
	<input type="hidden" name="v_orderaddr"    value="<?php echo $v_orderaddr;?>">
	<input type="hidden" name="v_ordermobile"  value="<?php echo $v_ordermobile;?>">
	<input type="hidden" name="v_orderemail"   value="<?php echo $v_orderemail;?>">

</form>

</body>
</html>
