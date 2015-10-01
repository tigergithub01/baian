<?php
session_start();

include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );

//define('IN_ECS', true);
//require('../../includes/init.php');

$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

//$url = "https://api.weibo.com/2/users/show.json?";

//,'blog_uids'=>$token['uid']

//$params = array('appkey'=>WB_AKEY,'access_token'=>$token['access_token'],'user_ip'=>real_ip());

//$url .= http_build_query($params);
//die($url);
//$res = file_get_contents($url);

$m = new SaeTClientV2( WB_AKEY , WB_SKEY , $token['access_token']);
$user_info = $m->show_user_by_id($token['uid']);

//echo "<pre>";
//var_dump($o);
//var_dump($token);
//var_dump($res);
//die();
	define('IN_ECS', true);
	require('../../includes/init.php');
	$sessionbk=$_SESSION;
//	die('here?');

//	echo "<pre>";
//	var_dump($user_info);die();
//	var_dump($user['name']);die();

				if($user_info['screen_name'] !== ""){
						// $username = $aResult['nickname'];
						//	$username=iconv('UTF-8','gb2312',$aResult['nickname']);
						
						$username = 'user_sina_' . trim(substr(md5($user_info['id']), 0 , 5)) ;
						$password=time();//随便弄个密码 反正没有用 你再试试
						$email='@sina.com';//QQG开放平台没有返回邮箱 所以随便弄个 其他的可以根据返回情况而定
//						  if(isset($_COOKIE["XPH_return_uri"])){
//					   	$back_act =$_COOKIE["XPH_return_uri"];
//						    }else{
						$back_act ="/user.php";
//						    }
						if (check_user($user_info['id'] , $username) !== false){
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
									 "(`email`,  `user_name`, `password`, `reg_time` , `last_login`, `last_ip`,`sina_id`) 
							   VALUES ('$email', '$username', '$password', '$reg_date', '$reg_date', '$ip'  ,  '$user_info[id]')" ;
								$GLOBALS['db']->query($sql);
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
			
function check_user($sina_id , $username){
$sql = "SELECT user_id, password, salt " .
                   " FROM " . $GLOBALS['ecs']->table("users").
                   " WHERE sina_id='$sina_id' AND user_name='$username'";
            $row = $GLOBALS['db']->getRow($sql);
            if(!empty($row)){
            	return true;
            }else{
            	return false;
            }
}
