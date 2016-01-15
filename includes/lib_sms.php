<?php

/**
短信验证码的发送与验证
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

function Post($curlPost,$url){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30); //设定超时时间为30秒
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
	$sms_dir = "data/sms/".date('Ymd');
	if (!file_exists($sms_dir)){
		make_dir($sms_dir);
	}
	$filename = $sms_dir.'/'.$file_name.'.log';
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

/**
 * 验证短信验证码
 * @param unknown $mobile
 * @param unknown $mobile_code
 */
function check_sms($mobile,$mobile_code){
	$result = array('success'=>true,'message'=>'');
	
	if(empty($mobile)){
		$result['success'] = false;
		$result['message'] = "手机号码不能为空";
	}
		
	if(empty($mobile_code)){
		$result['success'] = false;
		$result['message'] = "短信验证码不能为空";
	}
	
	$sms_code = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('sms_code') . " WHERE phone_number = '$mobile' AND verify_code ='$mobile_code' AND is_success = 1 order by sent_time desc LIMIT 1" );
	if($sms_code){
		if($sms_code['expiration_time'] < gmtime()){
			$result['success'] = false;
			$result['message'] = "您输入的短信验证码已过期";
		}
	}else{
		$result['success'] = false;
		$result['message'] = "您输入的短信验证码无效";
	}
	
	return $result;
	
}

/**
 * 验证邮箱验证码
 * @param unknown $email
 * @param unknown $email_code
 */
function check_email($email, $email_code){
	$result = array('success'=>true,'message'=>'');

	if(empty($email)){
		$result['success'] = false;
		$result['message'] = "邮箱地址不能为空";
	}

	if(empty($email_code)){
		$result['success'] = false;
		$result['message'] = "邮箱验证码不能为空";
	}

	$email_record= $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('email_code') . " WHERE email = '$email' AND verify_code ='$email_code' order by sent_time desc LIMIT 1" );
	if($email_record){
		if($email_record['expiration_time'] < gmtime()){
			$result['success'] = false;
			$result['message'] = "您输入的邮箱验证码已过期";
		}
	}else{
		$result['success'] = false;
		$result['message'] = "您输入的邮箱验证码无效";
	}

	return $result;

}

?>