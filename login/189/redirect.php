<?php
include "appSettings.php";
if(isset($_GET))
{  
    if(isset($_GET["code"]))
        $code = $_GET["code"];//AC模式得到AC授权码
    else if(isset($_GET["access_token"]))
    {    
         $access_token = $_GET["access_token"];//IG模式直接获得Access_Token
	     echo "Access_Token:".$access_token;
    }
} 

//echo "<pre>";
//var_dump($GLOBALS);die();

if(isset($_POST) && isset($_POST["app_id"]) && isset($_POST["app_secret"]) && isset($_POST["grant_type"]))
{
   
    $refreshtoken = $_POST['refresh_token'];
    $app_id = $_POST["app_id"];
    $app_secret = $_POST["app_secret"];
    $grant_type = $_POST["grant_type"];
   
    $send = 'app_id='.$app_id.'&app_secret='.$app_secret.'&grant_type='.$grant_type;
    if($grant_type=="refresh_token")
    $send .='&refresh_token='.$refreshtoken;
    $access_token = curl_post("https://oauth.api.189.cn/emp/oauth2/v2/access_token", $send);
    $access_token = json_decode($access_token, true);
    if($grant_type=="refresh_token")
    {
        echo "Access_Token has been refreshed!";
        echo "<br/>The latest Access_Token is ".$access_token['access_token'];
    }
}


$redirect_uri = "http://www.123121.com/login/189/redirect.php";
#AC模式根据AC授权码请求Access_Token
if(!$access_token)
{  
	$send = 'app_id='.$appid.'&redirect_uri='.urlencode($redirect_uri).'&app_secret='.$appsecret.'&grant_type=authorization_code&code='.$code;
	$access_token = curl_post("https://oauth.api.189.cn/emp/oauth2/v2/access_token", $send);
//	echo "<pre>";
//	var_dump($access_token);die();
	$access_token = json_decode($access_token, true);
	
//	echo "<pre>";
//	var_dump($access_token);die();
	
//	echo "Access_Token:".$access_token['access_token'];//AC模式得到Access_Token
//    echo "<br/>Refresh_Token:".$access_token['refresh_token'];//AC模式得到Refresh_Token,可以用于刷新Access_Token
}
//print_r($access_token);

	define('IN_ECS', true);
	require('../../includes/init.php');
	$sessionbk=$_SESSION;
//	die('here?');


	$user_info = $access_token;
//	echo "<pre>";
//	var_dump($user_info);
//	var_dump($user_info['name']);die();

				if($user_info['open_id'] !== ""){
						// $username = $aResult['nickname'];
						//	$username=iconv('UTF-8','gb2312',$aResult['nickname']);
						
//						$username = 'user_189_' . trim(substr(md5($user_info['open_id']), 0 , 5)) ;
						$username = $user_info['open_id'];
						$password=time();//随便弄个密码 反正没有用 你再试试
						$email='@189.com';//QQG开放平台没有返回邮箱 所以随便弄个 其他的可以根据返回情况而定
//						  if(isset($_COOKIE["XPH_return_uri"])){
//					   	$back_act =$_COOKIE["XPH_return_uri"];
//						    }else{
						$back_act ="/user.php";
//						    }
						if (check_user($user_info['open_id'] , $username) !== false){
							
								echo "<pre>";
								var_dump($username);
								echo "</pre>";//die('here1');
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
									 "(`email`,  `user_name`, `password`, `reg_time` , `last_login`, `last_ip`,`open_id_189`) 
							   VALUES ('$email', '$username', '$password', '$reg_date', '$reg_date', '$ip'  ,  '$user_info[open_id]')" ;
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
			
function check_user($open_id_189 , $username){
$sql = "SELECT user_id, password, salt " .
                   " FROM " . $GLOBALS['ecs']->table("users").
                   " WHERE open_id_189='$open_id_189' AND user_name='$username'";
            $row = $GLOBALS['db']->getRow($sql);
            if(!empty($row)){
            	return true;
            }else{
            	return false;
            }
}


?>