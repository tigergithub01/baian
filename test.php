<?php

header("Content-type:text/html;charset=utf-8");
/* echo md5(md5('baianAdmin@2015').'1603');
echo "<p>";
echo md5(md5('baianAdmin@2015').'6796'); */

// var_dump($_SERVER); 

var_dump(round(64.60,1));
var_dump(round(64.59,1));

//phpinfo();
echo md5('123456');
echo "<p>";
echo md5('1234567');
echo "<p>";
echo md5(md5('admin').'3783');
echo "<p>";
echo md5(md5('baianAdmin@2015').'6796');
echo "<p>";
echo date('Y-m-d H:i:s', 1447344000);
echo "<p>";
echo time();
echo "<p>";
echo date('Z');
echo "<p>";
echo date_default_timezone_get();
echo "<p>";
echo (time() - date('Z'));
echo "<p>";
echo (date('Z') / 3600);
echo "<p>";
$empty = trim(" ");
if(empty($empty)){
	echo 'empty';
}else{
	echo 'not empty';
}

var_dump(md5(md5("19910117weihe").'963'));
var_dump(md5(md5("111111").'0'));
var_dump(md5("111111"));
var_dump(date('Y'));
var_dump(intval('43366'));

var_dump(floor((1/5)*7));
var_dump(floor((1/ 2) * 1));

define('IN_ECS',1);
require('includes/lib_time.php');
var_dump(gmtime());


var_dump(substr_cut("an199117"));

/* define('IN_ECS',1);
require('includes/lib_time.php');
var_dump(gmtime());

var_dump(strtotime("-1 month"));
var_dump(gmstr2time("-1 month"));
var_dump(date('Y-m-d H:i:s',strtotime("-1 month")));
var_dump(date('Y-m-d H:i:s',gmstr2time("-1 month")));
var_dump(date('Y-m-d H:i:s',strtotime("-1 year")));
var_dump(date('Y-m-d H:i:s',gmstr2time("-1 year")));
var_dump(mktime(0,0,0,1,1,date('Y')));
var_dump(date('Y-m-d H:i:s',mktime(0,0,0,1,1,date('Y'))));
var_dump(basename(__FILE__, '.php'));
var_dump(basename(__FILE__, '.php'));

$order_sn = 'xxxx';
$sql = 'SELECT order_id FROM ' . "order_info". " WHERE order_sn like '%$order_sn%'";
var_dump($sql);


$s = file_get_contents('http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=13724346621');
var_dump($s);
preg_match_all("/(\w+):'([^']+)/", $s, $m);
$a = array_combine($m[1], $m[2]);
var_dump($a);

$mobile = '13724346621';
$timeout = 5;
$url='http://tcc.taobao.com/cc/json/mobile_tel_segment.htm';
$ch=curl_init();
curl_setopt($ch,CURLOPT_HTTPHEADER,array("content-type: application/x-www-form-urlencoded; charset=gb2312"));
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,'tel='.$mobile);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$data=curl_exec($ch);
curl_close($ch);
var_dump($data);
preg_match_all("/(\w+):'([^']+)/", $data, $m);
$a = array_combine($m[1], $m[2]);
var_dump($a);
var_dump($a['province']);
var_dump('中国'); */
/* $data=simplexml_load_string($data);
if(strpos($data,'http://')){
	return '手机号码格式错误！';
}else{
	return $data;
} */


// var_dump(local_mktime(0,0,0,1,1));
// var_dump(local_mktime(23,59,59,12,31));
/* echo gmtime();

function gmtime()
{
	return (time() - date('Z'));
} */

/* var_dump(curl_https('http://tcc.taobao.com/cc/json/mobile_tel_segment.htm', 'tel=13724346621'));
function curl_https($url, $data=array(), $header=array(), $timeout=30){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

	$response = curl_exec($ch);

	if($error=curl_error($ch)){
		die($error);
	}

	curl_close($ch);

	return $response;

}

var_dump(ppost('http://tcc.taobao.com/cc/json/mobile_tel_segment.htm', 'tel=13724346621'));

function ppost($url,$data){ // 模拟提交数据函数
	$curl = curl_init(); // 启动一个CURL会话
	curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
// 	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
// 	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
// 	curl_setopt($curl, CURLOPT_REFERER, $ref);
	curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
// 	curl_setopt($curl, CURLOPT_COOKIEFILE,$GLOBALS ['cookie_file']); // 读取上面所储存的Cookie信息
// 	curl_setopt($curl, CURLOPT_COOKIEJAR, $GLOBALS['cookie_file']); // 存放Cookie信息的文件名称

	curl_setopt($curl, CURLOPT_HTTPHEADER,array('Accept-Encoding: gzip, deflate'));
	curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
	curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	$tmpInfo = curl_exec($curl); // 执行操作
	if (curl_errno($curl)) {
		echo 'Errno'.curl_error($curl);
	}
	curl_close($curl); // 关键CURL会话
	return $tmpInfo; // 返回数据
}
 */
////////////////////////////////////////////////////////////////////
// PHP截取中英文及标点符号混合的字符串函数（绝对不会出现乱码）
// 本程序在utf-8、gb2312中测试通过。使用者自行测试big5。
// 函数 left( 源字符串, 截取指定的字符串个数, 编码（可省略，默认为utf-8） )
////////////////////////////////////////////////////////////////////

function left($str, $len, $charset="utf-8")
{
	//如果截取长度小于等于0，则返回空
	if( !is_numeric($len) or $len <= 0 )
	{
		return "";
	}

	//如果截取长度大于总字符串长度，则直接返回当前字符串
	$sLen = strlen($str);
	if( $len >= $sLen )
	{
		return $str;
	}

	//判断使用什么编码，默认为utf-8
	if ( strtolower($charset) == "utf-8" )
	{
		$len_step = 3; //如果是utf-8编码，则中文字符长度为3
	}else{
		$len_step = 2; //如果是gb2312或big5编码，则中文字符长度为2
	}

	//执行截取操作
	$len_i = 0;
	//初始化计数当前已截取的字符串个数，此值为字符串的个数值（非字节数）
	$substr_len = 0; //初始化应该要截取的总字节数

	for( $i=0; $i < $sLen; $i++ )
	{
		if ( $len_i >= $len ) break; //总截取$len个字符串后，停止循环
		//判断，如果是中文字符串，则当前总字节数加上相应编码的中文字符长度
		if( ord(substr($str,$i,1)) > 0xa0 )
		{
			$i += $len_step - 1;
			$substr_len += $len_step;
		}else{ //否则，为英文字符，加1个字节
			$substr_len ++;
		}
		$len_i ++;
	}
	$result_str = substr($str,0,$substr_len );
	return $result_str;
}

/**
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
 * @param string $user_name 姓名
 * @return string 格式化后的姓名
 */
function substr_cut($user_name){
	$strlen     = mb_strlen($user_name, 'utf-8');
	$firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
	$lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
	return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}

////////////////////////////////////////////////////////////////////
// 调用示例
////////////////////////////////////////////////////////////////////
// $str = "空格也算一个字符";
// echo '</br>';
// echo mb_substr($str, 1,1,'utf-8');
// echo "截取后的字符串：".left($str,4);