<?php
// PHP version of merchant.jsp
//这是B2CAPI通用版的php客户端调用测试
//作    者：bocomm
//创建时间：2013-12-12
?>

<?php
	//获得表单传过来的数据
	$interfaceVersion = $_REQUEST["interfaceVersion"];		
	$orderid = $_REQUEST["orderid"];
	$orderDate = $_REQUEST["orderDate"];
	$orderTime = $_REQUEST["orderTime"];
	$tranType = $_REQUEST["tranType"];
	$amount = $_REQUEST["amount"];
	$curType = $_REQUEST["curType"];
	$orderContent = $_REQUEST["orderContent"];
	$orderMono = $_REQUEST["orderMono"];
	$phdFlag = $_REQUEST["phdFlag"];
	$notifyType = $_REQUEST["notifyType"];
	$merURL = $_REQUEST["merURL"];
	$goodsURL = $_REQUEST["goodsURL"];
	$jumpSeconds = $_REQUEST["jumpSeconds"];
	$payBatchNo = $_REQUEST["payBatchNo"];
	$proxyMerName = $_REQUEST["proxyMerName"];
	$proxyMerType = $_REQUEST["proxyMerType"];
	$proxyMerCredentials = $_REQUEST["proxyMerCredentials"];
	$netType = $_REQUEST["netType"];
	$issBankNo = $_REQUEST["issBankNo"];
	$tranCode = "cb2200_sign";

	$source = "";
	
	//htmlentities($orderMono,"ENT_QUOTES","GB2312");
	//连接字符串
	$source = $interfaceVersion."|merID|".$orderid."|".$orderDate."|".$orderTime."|".$tranType."|"
	.$amount."|".$curType."|".$orderContent."|".$orderMono."|".$phdFlag."|".$notifyType."|".$merURL."|"
	.$goodsURL."|".$jumpSeconds."|".$payBatchNo."|".$proxyMerName."|".$proxyMerType."|".$proxyMerCredentials."|".$netType;


	//连接地址
//	$socketUrl = "tcp://127.0.0.1:8891";
//	$fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
//	$retMsg="";
//	//
//	if (!$fp) {
//		echo "$errstr ($errno)<br />\n";
//	} else 
//	{
//		$in  = "<?xml version='1.0' encoding='UTF-8'";
//		$in .= "<Message>";
//		$in .= "<TranCode>".$tranCode."</TranCode>";
//		$in .= "<MsgContent>".$source."</MsgContent>";
//		$in .= "</Message>";
//		fwrite($fp, $in);
//		while (!feof($fp)) {
//			$retMsg =$retMsg.fgets($fp, 1024);
//			
//		}
//		fclose($fp);
//	}	
//	//解析返回xml
//	$dom = new DOMDocument;
//	$dom->loadXML($retMsg);
//
//	$retCode = $dom->getElementsByTagName('retCode');
//	$retCode_value = $retCode->item(0)->nodeValue;
//	
//	$errMsg = $dom->getElementsByTagName('errMsg');
//	$errMsg_value = $errMsg->item(0)->nodeValue;
//
//	$signMsg = $dom->getElementsByTagName('signMsg');
//	$signMsg_value = $signMsg->item(0)->nodeValue;
//
//	$orderUrl = $dom->getElementsByTagName('orderUrl');
//	$orderUrl_value = $orderUrl->item(0)->nodeValue;
//	
//	$MerchID = $dom->getElementsByTagName('MerchID');
//	$merID = $MerchID->item(0)->nodeValue;
//	echo "retMsg=".$retMsg;
//	echo $retCode_value." ".$errMsg_value." ".$signMsg_value." ".$orderUrl_value;
//
//	if($retCode_value != "0")
//       {
//            echo "交易返回码：".$retCode_value."<br>";
//            echo "交易错误信息：" .$errMsg_value."<br>";
//       }
//       else
//       		echo "<pre>";
//
//       		var_dump($orderUrl_value);die();
//       		string(47) "https://pbanktest.95559.com.cn/netpay/MerPayB2C"
?> 

<html>
    <head>
        <title>商户订单测试</title>
        <meta http-equiv = "Content-Type" content = "text/html;charset=gbk">
    </head>

	<body bgcolor = "#FFFFFF" text = "#000000" >
        <form name = "form1" method = "post" action = "https://ebanktest.95559.com.cn/corporbank/NsTrans">
            <input type = "hidden" name = "interfaceVersion" value = "<?php echo($interfaceVersion); ?>">
            <input type = "hidden" name = "orderid" value = "<?php echo($orderid); ?>">
            <input type = "hidden" name = "orderDate" value = "<?php echo($orderDate); ?>">
            <input type = "hidden" name = "orderTime" value = "<?php echo($orderTime); ?>">
            <input type = "hidden" name = "tranType" value = "<?php echo($tranType); ?>">
            <input type = "hidden" name = "amount" value = "<?php echo($amount); ?>">
            <input type = "hidden" name = "curType" value = "<?php echo($curType); ?>">
            <input type = "hidden" name = "orderContent" value = "<?php echo($orderContent); ?>">
            <input type = "hidden" name = "orderMono" value = "<?php echo($orderMono); ?>">
            <input type = "hidden" name = "phdFlag" value = "<?php echo($phdFlag); ?>">
            <input type = "hidden" name = "notifyType" value = "<?php echo($notifyType); ?>">
            <input type = "hidden" name = "merURL" value = "<?php echo($merURL); ?>">
            <input type = "hidden" name = "goodsURL" value = "<?php echo($goodsURL); ?>">
            <input type = "hidden" name = "jumpSeconds" value = "<?php echo($jumpSeconds); ?>">
            <input type = "hidden" name = "payBatchNo" value = "<?php echo($payBatchNo); ?>">
            <input type = "hidden" name = "proxyMerName" value = "<?php echo($proxyMerName); ?>">
            <input type = "hidden" name = "proxyMerType" value = "<?php echo($proxyMerType); ?>">
            <input type = "hidden" name = "proxyMerCredentials" value = "<?php echo($proxyMerCredentials); ?>">
            <input type = "hidden" name = "netType" value = "<?php echo($netType); ?>">
            <input type = "hidden" name = "issBankNo" value = "OTHERS">
            <input type = "submit" value="提交" />
        </form>
    </body>
 
</html>
<?php
?>