<?php
include_once("WxPayHelper.php");


$commonUtil = new CommonUtil();
$wxPayHelper = new WxPayHelper();

//"_GET";a:16:{s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:5:"UTF-8";s:9:"notify_id";s:96:"NyjqpTaZCedtwM64Da4WXZNDXD79fIeKsMSodHqoXJQfMYEYtW6PfuzT-64f0RcwJgr8OhvZu-IH-KUGPqxp3UtlTzvAQYFp";s:12:"out_trade_no";s:13:"2014062293099";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"AF3FE3FDBFA773B0B85BDA0D101A2713";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140622160656";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201406223169894366";s:13:"transport_fee";s:1:"0";}

//AF3FE3FDBFA773B0B85BDA0D101A2713

$wxPayHelper->setParameter("bank_type", "WX");
$wxPayHelper->setParameter("body", "订单2014062930156");
$wxPayHelper->setParameter("partner", "1218715401");
$wxPayHelper->setParameter("out_trade_no", '2014062930156');
$wxPayHelper->setParameter("total_fee", "1");
$wxPayHelper->setParameter("fee_type", "1");
$wxPayHelper->setParameter("notify_url", "http://www.123121.com/weixin/callback.php");
$wxPayHelper->setParameter("spbill_create_ip", "127.0.0.1");
$wxPayHelper->setParameter("input_charset", "UTF-8");

echo "<pre>";
var_dump($wxPayHelper->create_biz_package());die();



?>
<html>
<script language="javascript">
function callpays()
{
	WeixinJSBridge.invoke('getBrandWCPayRequest',<?php //echo $wxPayHelper->create_biz_package(); ?>,function(res){
	WeixinJSBridge.log(res.err_msg);
	alert(res.err_code+res.err_desc+res.err_msg);
	});
}
</script>
<body>
<button type="button" onclick="callpay()">wx pay test</button>
</body>
</html>
