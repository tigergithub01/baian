<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php

function check_user($alipay_id , $username){
$sql = "SELECT user_id, password, salt " .
                   " FROM " . $GLOBALS['ecs']->table("users").
                   " WHERE alipay_id='$alipay_id' AND user_name='$username'";
            $row = $GLOBALS['db']->getRow($sql);
            if(!empty($row)){
            	return true;
            }else{
            	return false;
            }
}
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//支付宝用户号
	$user_id = $_GET['user_id'];

	//授权令牌
	$token = $_GET['token'];


	//判断是否在商户网站中已经做过了这次通知返回的处理
		//如果没有做过处理，那么执行商户的业务程序
		//如果有做过处理，那么不执行商户的业务程序
		
//	echo "验证成功<br />";
	define('IN_ECS', true);
	require('../../includes/init.php');
	$sessionbk=$_SESSION;
//	die('here?');

//	echo "<pre>";
//	var_dump($user_id);die();
//	var_dump($user['name']);die();

				if($user_id !== ""){
						// $username = $aResult['nickname'];
						//	$username=iconv('UTF-8','gb2312',$aResult['nickname']);
						
//						$username = 'user_renren_' . trim(substr(md5($user_info['id']), 0 , 5)) ;
						$username = $user_id ;
						$password=time();//随便弄个密码 反正没有用 你再试试
						$email='@alipay.com';//QQG开放平台没有返回邮箱 所以随便弄个 其他的可以根据返回情况而定
//						  if(isset($_COOKIE["XPH_return_uri"])){
//					   	$back_act =$_COOKIE["XPH_return_uri"];
//						    }else{
// 						$back_act ="/user.php";
						
						//手机版与电脑版跳转到不同的页面
						$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
						$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";
						if(($ua == '' || preg_match($uachar, $ua)) && !strpos(strtolower($_SERVER['REQUEST_URI']),'wap')){
							$back_act ="/mobile/user.php";
						}else{
							$back_act ="/user.php";
						}
						
//						    }
						if (check_user($user_id , $username) !== false){
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
									 "(`email`,  `user_name`, `password`, `reg_time` , `last_login`, `last_ip`,`alipay_id`) 
							   VALUES ('$email', '$username', '$password', '$reg_date', '$reg_date', '$ip'  ,  '$user_id')" ;
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
			
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "验证失败";
}
?>
        <title>支付宝快捷登录接口</title>
	</head>
    <body>
    </body>
</html>