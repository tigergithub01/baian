<?php
header ( 'Content-Type: text/html; charset=UTF-8' );

// 调试模式开关
define ( 'DEBUG_MODE', false );

if (! function_exists ( 'curl_init' )) {
	echo '您的服务器不支持 PHP 的 Curl 模块，请安装或与服务器管理员联系。';
	exit ();
}

// App Key
define ( "APP_KEY", '912bf04053514ecf97833ee9f156b308' );
// App Secret
define ( "APP_SECRET", '083b87a2226b4a26a08fc341a0dbe5f3' );
// 应用回调页地址
//define ( "CALLBACK_URL", "http://xun-ubuntu/renren-api2-sdk-php/examples/callback.php" );
define ( "CALLBACK_URL", "http://123121.com/login/renrenconnect/callback.php" );

if (DEBUG_MODE) {
	error_reporting ( E_ALL );
	ini_set ( 'display_errors', true );
}
