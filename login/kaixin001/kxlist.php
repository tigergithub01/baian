<?php
/**
 * PHP SDK for www.kaixin001.com (using OAuth2)
 * 
 * @author vboy <vboy1010@gmail.com>
 */
session_start();

include_once( 'config.php' );
include_once( 'KxApiBase.class.php' );

$o = new KxApiClient( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
$fields = $_REQUEST['fields'];
$userInfo = $o->users_me( $fields );//获取当前登录用户的个人资料

echo "<pre>";
var_dump($userInfo);die();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>开心网 PHP SDK For OAuth2.0 V2版 接口演示程序-Powered by Kaixin001.com</title>
</head>

<body>
	<?=$userInfo['name']?>,您好！ 
	<h2 align="left">获取当前登录用户的个人资料</h2>
	<form action="kxlist.php" method="get">
		<input type="text" name="fields" style="width:300px;" />
		<input type="submit" name="submit"/>
	</form>
</body>
</html>
