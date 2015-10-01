<?php
/**
 * PHP SDK for www.kaixin001.com (using OAuth2)
 * 
 * @author vboy <vboy1010@gmail.com>
 */
session_start();

include_once( 'config.php' );
include_once( 'KxApiBase.class.php' );

$o = new KxApiBase( WB_AKEY , WB_SKEY );

//state参数用于防止CSRF攻击，授权成功后回调时会原样返回
$_SESSION['state'] = md5(uniqid(rand(), TRUE));
$code_url = $o->getAuthorizeURL( WB_CALLBACK_URL, 'code', $_SESSION['state']);

header("Location:$code_url");

//echo "<pre>";
//var_dump($code_url);die();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>开心网 PHP SDK For OAuth2.0 V2版 Demo - Powered by Kaixin001.com</title>
</head>

<body>
    <p><a href="<?=$code_url?>"><img src="kx.png" title="点击进入授权页面" alt="点击进入授权页面" border="0" /></a></p>

</body>
</html>
