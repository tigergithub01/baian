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

if (isset($_REQUEST['code']) && $_REQUEST['state'] == $_SESSION['state']) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

if ($token) {
	$_SESSION['token'] = $token;

	$oc = new KxApiClient( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
	$fields = $_REQUEST['fields'];
	$userInfo = $oc->users_me( $fields );//获取当前登录用户的个人资料
	
	define('IN_ECS', true);
	require('../../includes/init.php');
	$sessionbk=$_SESSION;

	$user_kaixin_uid = $userInfo['uid'];
//	echo "<pre>";
//	var_dump($user_info);
//	var_dump($user_info['name']);die();

				if($user_kaixin_uid !== ""){
						// $username = $aResult['nickname'];
						//	$username=iconv('UTF-8','gb2312',$aResult['nickname']);
						
						$username = 'kaixin_'.$user_kaixin_uid;
						$password=time();//随便弄个密码 反正没有用 你再试试
						$email='@kaixin001.com';//QQG开放平台没有返回邮箱 所以随便弄个 其他的可以根据返回情况而定
//						  if(isset($_COOKIE["XPH_return_uri"])){
//					   	$back_act =$_COOKIE["XPH_return_uri"];
//						    }else{
						$back_act ="/user.php";
//						    }
						if (check_user($username) !== false){
								$GLOBALS['user']->set_session($username);
								$GLOBALS['user']->set_cookie($username);
								header("Location: ".$back_act."\n");  //验证成功,跳转页面
								exit;
						}else{
								$reg_date = time();
								$password =md5($password);
								$ip=real_ip();
							//	var_dump($qq_openid); die;//再试试
								$sql = "INSERT INTO " . $GLOBALS['ecs']->table("users") .
									 "(`email`,  `user_name`, `password`, `reg_time` , `last_login`, `last_ip`,`kaixin_uid`) 
							   VALUES ('$email', '$username', '$password', '$reg_date', '$reg_date', '$ip'  ,  '$username')" ;
								$GLOBALS['db']->query($sql);
								echo "<pre>";
								var_dump($username);
								echo "</pre>";//die('here2');
								$GLOBALS['user']->set_session($username);
								$GLOBALS['user']->set_cookie($username);
								header("Location: ".$back_act."\n");  //验证成功,跳转页面
								exit;
						}
			}else{
				echo "<script>alert('获取用户信息失败。');location.href='/index.php';</script>";
				$_SESSION=$sessionbk;
				exit;
			}
			
?>

授权完成,<a href="kxlist.php">进入你的API2.0列表页面</a><br />

<?php
} else {
?>

授权失败。

<?php
}
function check_user($kaixin_uid){
	$sql = "SELECT kaixin_uid FROM ".$GLOBALS['ecs']->table("users").
			" WHERE kaixin_uid='$kaixin_uid'";
	$row = $GLOBALS['db']->getRow($sql);
	
	if(!empty($row)){
		return true;
	}
	return false;
}
//echo "<pre>";
//var_dump($token);die();
//array(4) {
//  ["access_token"]=>
//  string(42) "159333659_c1bcfba1d3d27a7978cd8cfad7124039"
//  ["expires_in"]=>
//  string(7) "2592000"
//  ["scope"]=>
//  string(5) "basic"
//  ["refresh_token"]=>
//  string(42) "159333659_c2cad68a94014f704a990bd2261f96b5"
//}
	

//echo "<pre>";
//var_dump($userInfo);die();
//array(4) {
//  ["uid"]=>
//  string(9) "159333659"
//  ["name"]=>
//  string(9) "王冠诏"
//  ["gender"]=>
//  string(1) "1"
//  ["logo50"]=>
//  string(40) "http://img.kaixin001.com.cn/i/50_1_0.gif"
//}
?>
