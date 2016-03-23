<?php
require_once("./API/qqConnectAPI.php");
$qc = new QC();
$acs = $qc->qq_callback();
$qq_openid = $qc->get_openid();

$qc_get_info = new QC($acs,$qq_openid);
$arr = $qc_get_info->get_user_info();
//	echo '<meta charset="UTF-8">';
//	echo "<pre>";
//
//	var_dump($arr);die();
	define('IN_ECS', true);
	require('../includes/init.php');
	$sessionbk=$_SESSION;

				if($arr['nickname'] !== "" && $arr['nickname'] !== "qzuser"){
						// $username = $aResult['nickname'];
						//	$username=iconv('UTF-8','gb2312',$aResult['nickname']);
						$username = 'user_' . trim(substr($qq_openid, 0 , 5)) ;
//						$username = $qq_openid;
						$password=time();//随便弄个密码 反正没有用 你再试试
						$email='@qq.com';//QQG开放平台没有返回邮箱 所以随便弄个 其他的可以根据返回情况而定
//						  if(isset($_COOKIE["XPH_return_uri"])){
//					   	$back_act =$_COOKIE["XPH_return_uri"];
//						    }else{
						
						//手机版与电脑版跳转到不同的页面
						$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
						$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";
						if(($ua == '' || preg_match($uachar, $ua)) && !strpos(strtolower($_SERVER['REQUEST_URI']),'wap')){
							$back_act ="/mobile/user.php";
						}else{
							$back_act ="/user.php";
						}
//						    }
						if (check_user($qq_openid , $username) !== false){
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
									 "(`email`,  `user_name`, `password`, `reg_time` , `last_login`, `last_ip`,`qq_openid`) 
							   VALUES ('$email', '$username', '$password', '$reg_date', '$reg_date', '$ip'  ,  '$qq_openid')" ;
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
			
function check_user($qq_openid , $username){
$sql = "SELECT user_id, password, salt " .
                   " FROM " . $GLOBALS['ecs']->table("users").
                   " WHERE qq_openid='$qq_openid' AND user_name='$username'";
            $row = $GLOBALS['db']->getRow($sql);
            if(!empty($row)){
            	return true;
            }else{
            	return false;
            }
}