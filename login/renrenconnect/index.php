<?php
session_start ();

include_once ('config.php');
include_once ('./rennclient/RennClient.php');
// include_once ('renrenoauth/oauth2-client.class.php');
// include_once ('renrenoauth/renn-client.class.php');

$rennClient = new RennClient ( APP_KEY, APP_SECRET );
$rennClient->setDebug ( DEBUG_MODE );

// 生成state并存入SESSION，以供CALLBACK时验证使用
$state = uniqid ( 'renren_', true );
$_SESSION ['renren_state'] = $state;

// 得认证授权的url
$code_url = $rennClient->getAuthorizeURL ( CALLBACK_URL, 'code', $state );

header("Location:$code_url");
?>
