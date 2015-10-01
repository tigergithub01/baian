<?php

session_start();

header("Content-type:text/html; charset=UTF-8");

function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
}
function xml_to_array($xml){
	$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
	if(preg_match_all($reg, $xml, $matches)){
		$count = count($matches[0]);
		for($i = 0; $i < $count; $i++){
		$subxml= $matches[2][$i];
		$key = $matches[1][$i];
			if(preg_match( $reg, $subxml )){
				$arr[$key] = xml_to_array( $subxml );
			}else{
				$arr[$key] = $subxml;
			}
		}
	}
	return $arr;
}
function random($length = 6 , $numeric = 0) {
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	if($numeric) {
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	} else {
		$hash = '';
		$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
	}
	return $hash;
}
function write_file($file_name,$content){
	mkdirs(date('Ymd'));
	$filename = date('Ymd').'/'.$file_name.'.log';
	$Ts=fopen($filename,"a+");
	fputs($Ts,"\r\n".$content);
	fclose($Ts);
}
function mkdirs($dir, $mode = 0777){
	if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
	if (!self::mkdirs(dirname($dir), $mode)) return FALSE;
	return @mkdir($dir, $mode);
}
function read_file($file_name) {
	$content = '';
	$filename = date('Ymd').'/'.$file_name.'.log';
	if(function_exists('file_get_contents')) {
		@$content = file_get_contents($filename);
	} else {
		if(@$fp = fopen($filename, 'r')) {
			@$content = fread($fp, filesize($filename));
			@fclose($fp);
		}
	}
	$content = explode("\r\n",$content);
	return end($content);
}












$mobile = @$_POST['mobile'];
$mobile_code = @$_POST['mobile_code'];

if($_GET['act']=='check'){
	if($mobile!=$_SESSION['mobile'] or $mobile_code!=$_SESSION['mobile_code']){
		exit(json_encode(array('msg'=>'手机验证码输入错误。')));	
	}else{
		exit(json_encode(array('code'=>'2')));	
	}
}

if($_GET['act']=='send'){
	if(empty($mobile)){
		exit(json_encode(array('msg'=>'手机号码不能为空')));
	}
	
	$preg='/^1[0-9]{10}$/';//简单的方法
	if(!preg_match($preg,$mobile)) {
		exit(json_encode(array('msg'=>'手机号码格式不正确')));
	 }	
	
	$mobile_code = random(4,1);
	$target = "http://121.199.16.178/webservice/sms.php?method=Submit";
	//替换成自己的测试账号,参数顺序和wenservice对应

	//exit(json_encode(array('msg'=>strtotime( read_file($mobile))."\r\n".(time()-60) )));
	if($_SESSION['mobile']){
		//exit(json_encode(array('msg'=> read_file($mobile) )));
		if(strtotime(read_file($mobile))>(time()-60)){
			exit(json_encode(array('msg'=>'获取验证码太过频繁，一分钟之内只能获取一次。')));	
		}
	}
	
	$post_data = "account=cf_baia&password=baia123&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$mobile_code."。如非本人操作，请勿理会！");
	//密码可以使用明文密码或使用32位MD5加密

	$get = Post($post_data, $target);

	$gets =  xml_to_array($get);
	
	write_file($mobile,$get."\r\n".date("Y-m-d H:i:s"));

	if($gets['SubmitResult']['code']==2){
		$_SESSION['mobile']=$mobile;
		$_SESSION['mobile_code']=$mobile_code;	
		exit(json_encode(array('code'=>2)));	
	}else{
		exit(json_encode(array('msg'=>'手机验证码发送失败。')));	
	}
}


?>
