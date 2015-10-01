<?php
session_start ();

include_once ('config.php');
include_once ('./rennclient/RennClient.php');

$rennClient = new RennClient ( APP_KEY, APP_SECRET );
$rennClient->setDebug ( DEBUG_MODE );

// 处理code -- 根据code来获得token
if (isset ( $_REQUEST ['code'] )) {
	$keys = array ();
	
	// 验证state，防止伪造请求跨站攻击
	$state = $_REQUEST ['state'];
	if (empty ( $state ) || $state !== $_SESSION ['renren_state']) {
		echo '非法请求！';
		exit ();
	}
	unset ( $_SESSION ['renren_state'] );
	
	// 获得code
	$keys ['code'] = $_REQUEST ['code'];
	$keys ['redirect_uri'] = CALLBACK_URL;
	try {
		// 根据code来获得token
		$token = $rennClient->getTokenFromTokenEndpoint ( 'code', $keys );
	} catch ( RennException $e ) {
		var_dump ( $e );
	}
}

if ($token) {
//	print_r ( $token );
	?>
授权完成,
<a href="demo.php">demo</a>
<br />
<?php
} else {
	?>
授权失败。
<?php
}

//$user_info = $rennClient->getUserService()->getUser('');
//$user_info = $rennClient->getUserService()->getUserLogin();
//echo "<pre>";
//$rennClient->authWithStoredToken ();
//$renn_client = new RennClient ( APP_KEY, APP_SECRET );
//$renn_client->setDebug ( DEBUG_MODE );
// 获得保存的token
//$renn_client->authWithStoredToken();
// 获得用户接口
echo "<pre>";
var_dump($GLOBALS);die();


$user_service = $rennClient->getUserService();
// 获得当前登录用户
$user_info = $user_service->getUserLogin();
var_dump($user_info);die();

//$user = array();
//$user['name'] = $user_obj->name;
//$user['id'] = $user_obj->id;

//$user = $rennClient->getUserService()->getUserLogin();

//echo "<pre>";
//var_dump(md5('梁炳校'));die();
//var_dump($user_obj);die();
//echo "<br>";
//var_dump($user);die();
//array(9) {
//  ["name"]=>
//  string(9) "梁炳校"
//  ["id"]=>
//  int(575656563)

	define('IN_ECS', true);
	require('../../includes/init.php');
	$sessionbk=$_SESSION;
//	die('here?');

//	echo "<pre>";
//	var_dump($user);
//	var_dump($user['name']);die();

				if($user_info['name'] !== ""){
						// $username = $aResult['nickname'];
						//	$username=iconv('UTF-8','gb2312',$aResult['nickname']);
						
						$username = 'user_renren_' . trim(substr(md5($user_info['id']), 0 , 5)) ;
						$password=time();//随便弄个密码 反正没有用 你再试试
						$email='@renren.com';//QQG开放平台没有返回邮箱 所以随便弄个 其他的可以根据返回情况而定
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
									 "(`email`,  `user_name`, `password`, `reg_time` , `last_login`, `last_ip`,`renren_id`) 
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
			
function check_user($renren_id , $username){
$sql = "SELECT user_id, password, salt " .
                   " FROM " . $GLOBALS['ecs']->table("users").
                   " WHERE renren_id='$renren_id' AND user_name='$username'";
            $row = $GLOBALS['db']->getRow($sql);
            if(!empty($row)){
            	return true;
            }else{
            	return false;
            }
}

?>
