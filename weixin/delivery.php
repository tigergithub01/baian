<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "fdsfsdf");

include_once("WxPayHelper.php");

file_put_contents('./delivery_order_info.txt', serialize($_GET));

file_put_contents('./delivery.txt', ' one ', FILE_APPEND);
file_put_contents('./delivery.txt', ' two ', FILE_APPEND);

die('success');

$wxPayHelper = new WxPayHelper();

echo "<pre>";
$str = 'a:15:{s:7:"GLOBALS";a:15:{s:7:"GLOBALS";a:15:{s:7:"GLOBALS";N;s:18:"HTTP_RAW_POST_DATA";s:352:"<xml><OpenId><![CDATA[of9wzuFWr0vQd1Bk0ONbspyXuXAY]]></OpenId>
<AppId><![CDATA[wx54747450599e343c]]></AppId>
<IsSubscribe>1</IsSubscribe>
<TimeStamp>1399291752</TimeStamp>
<NonceStr><![CDATA[y1PWO9ceLfVBvmsG]]></NonceStr>
<AppSignature><![CDATA[c97a23a2eecaae23fae92f14b084686acc64fef6]]></AppSignature>
<SignMethod><![CDATA[sha1]]></SignMethod>
</xml>";s:4:"_ENV";a:6:{s:4:"TERM";s:5:"xterm";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:3:"PWD";s:1:"/";s:4:"LANG";s:1:"C";s:5:"SHLVL";s:1:"2";s:1:"_";s:29:"/www/wdlinux/apache/bin/httpd";}s:13:"HTTP_ENV_VARS";a:6:{s:4:"TERM";s:5:"xterm";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:3:"PWD";s:1:"/";s:4:"LANG";s:1:"C";s:5:"SHLVL";s:1:"2";s:1:"_";s:29:"/www/wdlinux/apache/bin/httpd";}s:5:"_POST";a:0:{}s:14:"HTTP_POST_VARS";a:0:{}s:4:"_GET";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}s:13:"HTTP_GET_VARS";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}s:7:"_COOKIE";a:0:{}s:16:"HTTP_COOKIE_VARS";a:0:{}s:7:"_SERVER";a:30:{s:9:"HTTP_HOST";s:14:"www.123121.com";s:14:"HTTP_X_REAL_IP";s:13:"140.207.54.73";s:20:"HTTP_X_FORWARDED_FOR";s:13:"140.207.54.73";s:15:"HTTP_CONNECTION";s:5:"close";s:15:"HTTP_USER_AGENT";s:11:"Mozilla/4.0";s:11:"HTTP_ACCEPT";s:3:"*/*";s:12:"CONTENT_TYPE";s:8:"text/xml";s:14:"CONTENT_LENGTH";s:3:"352";s:11:"HTTP_PRAGMA";s:8:"no-cache";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:14:"www.123121.com";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:13:"140.207.54.73";s:13:"DOCUMENT_ROOT";s:28:"/www/web/default/public_html";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:48:"/www/web/default/public_html/weixin/delivery.php";s:11:"REMOTE_PORT";s:5:"55822";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.0";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"REQUEST_URI";s:448:"/weixin/delivery.php?bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"SCRIPT_NAME";s:20:"/weixin/delivery.php";s:8:"PHP_SELF";s:20:"/weixin/delivery.php";s:12:"REQUEST_TIME";i:1399291752;s:4:"argv";a:1:{i:0;s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";}s:4:"argc";i:1;}s:16:"HTTP_SERVER_VARS";a:30:{s:9:"HTTP_HOST";s:14:"www.123121.com";s:14:"HTTP_X_REAL_IP";s:13:"140.207.54.73";s:20:"HTTP_X_FORWARDED_FOR";s:13:"140.207.54.73";s:15:"HTTP_CONNECTION";s:5:"close";s:15:"HTTP_USER_AGENT";s:11:"Mozilla/4.0";s:11:"HTTP_ACCEPT";s:3:"*/*";s:12:"CONTENT_TYPE";s:8:"text/xml";s:14:"CONTENT_LENGTH";s:3:"352";s:11:"HTTP_PRAGMA";s:8:"no-cache";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:14:"www.123121.com";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:13:"140.207.54.73";s:13:"DOCUMENT_ROOT";s:28:"/www/web/default/public_html";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:48:"/www/web/default/public_html/weixin/delivery.php";s:11:"REMOTE_PORT";s:5:"55822";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.0";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"REQUEST_URI";s:448:"/weixin/delivery.php?bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"SCRIPT_NAME";s:20:"/weixin/delivery.php";s:8:"PHP_SELF";s:20:"/weixin/delivery.php";s:12:"REQUEST_TIME";i:1399291752;s:4:"argv";a:1:{i:0;s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";}s:4:"argc";i:1;}s:6:"_FILES";a:0:{}s:15:"HTTP_POST_FILES";a:0:{}s:8:"_REQUEST";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}}s:18:"HTTP_RAW_POST_DATA";s:352:"<xml><OpenId><![CDATA[of9wzuFWr0vQd1Bk0ONbspyXuXAY]]></OpenId>
<AppId><![CDATA[wx54747450599e343c]]></AppId>
<IsSubscribe>1</IsSubscribe>
<TimeStamp>1399291752</TimeStamp>
<NonceStr><![CDATA[y1PWO9ceLfVBvmsG]]></NonceStr>
<AppSignature><![CDATA[c97a23a2eecaae23fae92f14b084686acc64fef6]]></AppSignature>
<SignMethod><![CDATA[sha1]]></SignMethod>
</xml>";s:4:"_ENV";a:6:{s:4:"TERM";s:5:"xterm";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:3:"PWD";s:1:"/";s:4:"LANG";s:1:"C";s:5:"SHLVL";s:1:"2";s:1:"_";s:29:"/www/wdlinux/apache/bin/httpd";}s:13:"HTTP_ENV_VARS";a:6:{s:4:"TERM";s:5:"xterm";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:3:"PWD";s:1:"/";s:4:"LANG";s:1:"C";s:5:"SHLVL";s:1:"2";s:1:"_";s:29:"/www/wdlinux/apache/bin/httpd";}s:5:"_POST";a:0:{}s:14:"HTTP_POST_VARS";a:0:{}s:4:"_GET";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}s:13:"HTTP_GET_VARS";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}s:7:"_COOKIE";a:0:{}s:16:"HTTP_COOKIE_VARS";a:0:{}s:7:"_SERVER";a:30:{s:9:"HTTP_HOST";s:14:"www.123121.com";s:14:"HTTP_X_REAL_IP";s:13:"140.207.54.73";s:20:"HTTP_X_FORWARDED_FOR";s:13:"140.207.54.73";s:15:"HTTP_CONNECTION";s:5:"close";s:15:"HTTP_USER_AGENT";s:11:"Mozilla/4.0";s:11:"HTTP_ACCEPT";s:3:"*/*";s:12:"CONTENT_TYPE";s:8:"text/xml";s:14:"CONTENT_LENGTH";s:3:"352";s:11:"HTTP_PRAGMA";s:8:"no-cache";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:14:"www.123121.com";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:13:"140.207.54.73";s:13:"DOCUMENT_ROOT";s:28:"/www/web/default/public_html";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:48:"/www/web/default/public_html/weixin/delivery.php";s:11:"REMOTE_PORT";s:5:"55822";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.0";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"REQUEST_URI";s:448:"/weixin/delivery.php?bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"SCRIPT_NAME";s:20:"/weixin/delivery.php";s:8:"PHP_SELF";s:20:"/weixin/delivery.php";s:12:"REQUEST_TIME";i:1399291752;s:4:"argv";a:1:{i:0;s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";}s:4:"argc";i:1;}s:16:"HTTP_SERVER_VARS";a:30:{s:9:"HTTP_HOST";s:14:"www.123121.com";s:14:"HTTP_X_REAL_IP";s:13:"140.207.54.73";s:20:"HTTP_X_FORWARDED_FOR";s:13:"140.207.54.73";s:15:"HTTP_CONNECTION";s:5:"close";s:15:"HTTP_USER_AGENT";s:11:"Mozilla/4.0";s:11:"HTTP_ACCEPT";s:3:"*/*";s:12:"CONTENT_TYPE";s:8:"text/xml";s:14:"CONTENT_LENGTH";s:3:"352";s:11:"HTTP_PRAGMA";s:8:"no-cache";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:14:"www.123121.com";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:13:"140.207.54.73";s:13:"DOCUMENT_ROOT";s:28:"/www/web/default/public_html";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:48:"/www/web/default/public_html/weixin/delivery.php";s:11:"REMOTE_PORT";s:5:"55822";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.0";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"REQUEST_URI";s:448:"/weixin/delivery.php?bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"SCRIPT_NAME";s:20:"/weixin/delivery.php";s:8:"PHP_SELF";s:20:"/weixin/delivery.php";s:12:"REQUEST_TIME";i:1399291752;s:4:"argv";a:1:{i:0;s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";}s:4:"argc";i:1;}s:6:"_FILES";a:0:{}s:15:"HTTP_POST_FILES";a:0:{}s:8:"_REQUEST";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}}s:18:"HTTP_RAW_POST_DATA";s:352:"<xml><OpenId><![CDATA[of9wzuFWr0vQd1Bk0ONbspyXuXAY]]></OpenId>
<AppId><![CDATA[wx54747450599e343c]]></AppId>
<IsSubscribe>1</IsSubscribe>
<TimeStamp>1399291752</TimeStamp>
<NonceStr><![CDATA[y1PWO9ceLfVBvmsG]]></NonceStr>
<AppSignature><![CDATA[c97a23a2eecaae23fae92f14b084686acc64fef6]]></AppSignature>
<SignMethod><![CDATA[sha1]]></SignMethod>
</xml>";s:4:"_ENV";a:6:{s:4:"TERM";s:5:"xterm";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:3:"PWD";s:1:"/";s:4:"LANG";s:1:"C";s:5:"SHLVL";s:1:"2";s:1:"_";s:29:"/www/wdlinux/apache/bin/httpd";}s:13:"HTTP_ENV_VARS";a:6:{s:4:"TERM";s:5:"xterm";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:3:"PWD";s:1:"/";s:4:"LANG";s:1:"C";s:5:"SHLVL";s:1:"2";s:1:"_";s:29:"/www/wdlinux/apache/bin/httpd";}s:5:"_POST";a:0:{}s:14:"HTTP_POST_VARS";a:0:{}s:4:"_GET";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}s:13:"HTTP_GET_VARS";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}s:7:"_COOKIE";a:0:{}s:16:"HTTP_COOKIE_VARS";a:0:{}s:7:"_SERVER";a:30:{s:9:"HTTP_HOST";s:14:"www.123121.com";s:14:"HTTP_X_REAL_IP";s:13:"140.207.54.73";s:20:"HTTP_X_FORWARDED_FOR";s:13:"140.207.54.73";s:15:"HTTP_CONNECTION";s:5:"close";s:15:"HTTP_USER_AGENT";s:11:"Mozilla/4.0";s:11:"HTTP_ACCEPT";s:3:"*/*";s:12:"CONTENT_TYPE";s:8:"text/xml";s:14:"CONTENT_LENGTH";s:3:"352";s:11:"HTTP_PRAGMA";s:8:"no-cache";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:14:"www.123121.com";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:13:"140.207.54.73";s:13:"DOCUMENT_ROOT";s:28:"/www/web/default/public_html";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:48:"/www/web/default/public_html/weixin/delivery.php";s:11:"REMOTE_PORT";s:5:"55822";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.0";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"REQUEST_URI";s:448:"/weixin/delivery.php?bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"SCRIPT_NAME";s:20:"/weixin/delivery.php";s:8:"PHP_SELF";s:20:"/weixin/delivery.php";s:12:"REQUEST_TIME";i:1399291752;s:4:"argv";a:1:{i:0;s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";}s:4:"argc";i:1;}s:16:"HTTP_SERVER_VARS";a:30:{s:9:"HTTP_HOST";s:14:"www.123121.com";s:14:"HTTP_X_REAL_IP";s:13:"140.207.54.73";s:20:"HTTP_X_FORWARDED_FOR";s:13:"140.207.54.73";s:15:"HTTP_CONNECTION";s:5:"close";s:15:"HTTP_USER_AGENT";s:11:"Mozilla/4.0";s:11:"HTTP_ACCEPT";s:3:"*/*";s:12:"CONTENT_TYPE";s:8:"text/xml";s:14:"CONTENT_LENGTH";s:3:"352";s:11:"HTTP_PRAGMA";s:8:"no-cache";s:4:"PATH";s:29:"/sbin:/usr/sbin:/bin:/usr/bin";s:16:"SERVER_SIGNATURE";s:0:"";s:15:"SERVER_SOFTWARE";s:6:"Apache";s:11:"SERVER_NAME";s:14:"www.123121.com";s:11:"SERVER_ADDR";s:9:"127.0.0.1";s:11:"SERVER_PORT";s:2:"80";s:11:"REMOTE_ADDR";s:13:"140.207.54.73";s:13:"DOCUMENT_ROOT";s:28:"/www/web/default/public_html";s:12:"SERVER_ADMIN";s:15:"you@example.com";s:15:"SCRIPT_FILENAME";s:48:"/www/web/default/public_html/weixin/delivery.php";s:11:"REMOTE_PORT";s:5:"55822";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.0";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"QUERY_STRING";s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"REQUEST_URI";s:448:"/weixin/delivery.php?bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";s:11:"SCRIPT_NAME";s:20:"/weixin/delivery.php";s:8:"PHP_SELF";s:20:"/weixin/delivery.php";s:12:"REQUEST_TIME";i:1399291752;s:4:"argv";a:1:{i:0;s:427:"bank_billno=201405058980076&bank_type=2032&discount=0&fee_type=1&input_charset=GBK&notify_id=NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc&out_trade_no=LQhLEz7oWkPYiTVx&partner=1218715401&product_fee=1&sign=E020AA8A260E4CAEC378FBFD8BEFFAA2&sign_type=MD5&time_end=20140505200908&total_fee=1&trade_mode=1&trade_state=0&transaction_id=1218715401201405053186477338&transport_fee=0";}s:4:"argc";i:1;}s:6:"_FILES";a:0:{}s:15:"HTTP_POST_FILES";a:0:{}s:8:"_REQUEST";a:17:{s:11:"bank_billno";s:15:"201405058980076";s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc";s:12:"out_trade_no";s:16:"LQhLEz7oWkPYiTVx";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";s:4:"sign";s:32:"E020AA8A260E4CAEC378FBFD8BEFFAA2";s:9:"sign_type";s:3:"MD5";s:8:"time_end";s:14:"20140505200908";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";s:14:"transaction_id";s:28:"1218715401201405053186477338";s:13:"transport_fee";s:1:"0";}}';
var_dump(unserialize($str));
die('ok!');

//  ["_GET"]=>
//  array(17) {
//    ["bank_billno"]=>
//    string(15) "201405058980076"
//    ["bank_type"]=>
//    string(4) "2032"
//    ["discount"]=>
//    string(1) "0"
//    ["fee_type"]=>
//    string(1) "1"
//    ["input_charset"]=>
//    string(3) "GBK"
//    ["notify_id"]=>
//    string(96) "NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc"
//    ["out_trade_no"]=>
//    string(16) "LQhLEz7oWkPYiTVx"
//    ["partner"]=>
//    string(10) "1218715401"
//    ["product_fee"]=>
//    string(1) "1"
//    ["sign"]=>
//    string(32) "E020AA8A260E4CAEC378FBFD8BEFFAA2"
//    ["sign_type"]=>
//    string(3) "MD5"
//    ["time_end"]=>
//    string(14) "20140505200908"
//    ["total_fee"]=>
//    string(1) "1"
//    ["trade_mode"]=>
//    string(1) "1"
//    ["trade_state"]=>
//    string(1) "0"
//    ["transaction_id"]=>
//    string(28) "1218715401201405053186477338"
//    ["transport_fee"]=>
//    string(1) "0"
//  }


//die('true');
//$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
//die();
//微信支付返回数据
//$data = file_get_contents($GLOBALS);
file_put_contents('./delivery.txt', serialize($GLOBALS));
die('success');

include_once("WxPayHelper.php");

$commonUtil = new CommonUtil();


$wxPayHelper = new WxPayHelper();

$wxPayHelper->setParameter("bank_type", "WX");



$wxPayHelper->setParameter("body", "欠王冠诏款100w，现还款！");
$wxPayHelper->setParameter("partner", "1218715401");
$wxPayHelper->setParameter("out_trade_no", $commonUtil->create_noncestr());
$wxPayHelper->setParameter("total_fee", "1");
$wxPayHelper->setParameter("fee_type", "1");
$wxPayHelper->setParameter("notify_url", "htttp://www.baidu.com");
$wxPayHelper->setParameter("spbill_create_ip", "127.0.0.1");
$wxPayHelper->setParameter("input_charset", "GBK");



echo $wxPayHelper->create_native_package();

//die('hello');

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>