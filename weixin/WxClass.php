<?php 
class WxClass{
	var $appid = "wx54747450599e343c";
	var $appsecret = "b84f27c03fcf5941b0fe66b2f3a0c68f";
	var $access_token = "E7aK_sEHX4tmXon2vXX7Cdf6v5Ry_HTwQuRoWMwNuQSFQ_cgWCK4hV6ATm5qUOFG";
	var $lasttime = 1399280261;
	
	public function __construct(){
		
		if(time() > ($this->lasttime+7200)){
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".
					$this->appid."&secret=".$this->appsecret;
					
			$res = $this->https_request($url);
			$result = json_decode($res,TRUE);
			$this->access_token = $result["access_token"];
			$this->lasttime = time();
		}

	}
	
	public function get_user_list($next_openid = null){
		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".
				$this->access_token."&next_openid=".$next_openid;
				
		$res = $this->https_request($url);
		return json_decode($res,true);
	}
	
	public function get_user_info($openid){
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".
				$this->access_token."&openid=".$openid."&lang=zh_CN";
		$res = $this->https_request($url);
		return json_decode($res,true);
	}
	
	public function create_menu($data){
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".
				$this->access_token;
				
		$res = $this->https_request($url,$data);
		return json_decode($res,true);
		
	}
	
	public function delivery($data){
		$url = "https://api.weixin.qq.com/pay/delivernotify?access_token=".
				$this->access_token;
				
		$res = $this->https_request($url,$data);
		return json_decode($res,true);
	}
	
	public function updatefeedback($openid,$feedbackid){
		$url = "https://api.weixin.qq.com/payfeedback/update?access_token=".
				$this->access_token.'&openid='.$openid.'&feedbackid='.$feedbackid;
				
		$res = $this->https_request($url);
		return json_decode($res,true);
	}
	
	public function postNotice(){
		$url = "http://www.123121.com/weixin/notice.php";
		
		$res = $this->https_request($url,array('test'=>'test'));
		return $res;
	}
	
	public function sendCustomMessage(){
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".
				$this->access_token;
				
		$data = '{"touser":"of9wzuFWr0vQd1Bk0ONbspyXuXAY","msgtype":"text","text":{"content":"hey!"}}';
		$res = $this->https_request($url,$data);
		return json_decode($res,true);
	}
	
	//https请求(支持GET和POST)
	protected function https_request($url,$data = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		if(!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
	
}

$test = new WxClass();

echo "<pre>";

var_dump($test->sendCustomMessage());die();
//var_dump($test->access_token);
//var_dump($test->lasttime);
//
//var_dump($test->get_user_list());
//var_dump($test->get_user_info('of9wzuIan4TncdGkRd4bcL3IatJ0'));

//$data = '{"button":[{"type":"click","name":"维权","key":"维权"},{"type":"click","name":"测试","key":"测试"}]}';
//var_dump($test->create_menu($data));die();


//    ["openid"]=>
//    array(7) {
//      [0]=>
//      string(28) "of9wzuIan4TncdGkRd4bcL3IatJ0"
//      [1]=>
//      string(28) "of9wzuLs_S3HEelKoAoUrFsr5Sgg"
//      [2]=>
//      string(28) "of9wzuD3lwYy1IFQhTvFej1H4p9s"
//      [3]=>
//      string(28) "of9wzuOpyZg1oPxtTnmml5RvS-98"
//      [4]=>
//      string(28) "of9wzuMa5nAAE77qUUunlZcgOuU8"
//      [5]=>
//      string(28) "of9wzuKvb_aL20w8IMEKfftuBzIc"
//      [6]=>//wanggz:
//      string(28) "of9wzuFWr0vQd1Bk0ONbspyXuXAY"
//    }


//  ["_GET"]=>
//  array(17) {
//    ["bank_billno"]=>
//    string(15) "201405058980076"
//    ["bank_type"]=>
//    string(4) "2032"
//    ["discount"]=>
//    string(1) "0"
//    ["fee_type"]=>
//    string(1) "1"
//    ["input_charset"]=>
//    string(3) "GBK"
//    ["notify_id"]=>
//    string(96) "NyjqpTaZCefJDslwkpWJFbLAkXG4ujP61cTLzgEYDrbXb25isDGJJd4fN4g4g0H1HRmCSKH5JVK0qzkLqzebNBd3FCPgFUVc"
//    ["out_trade_no"]=>
//    string(16) "LQhLEz7oWkPYiTVx"
//    ["partner"]=>
//    string(10) "1218715401"
//    ["product_fee"]=>
//    string(1) "1"
//    ["sign"]=>
//    string(32) "E020AA8A260E4CAEC378FBFD8BEFFAA2"
//    ["sign_type"]=>
//    string(3) "MD5"
//    ["time_end"]=>
//    string(14) "20140505200908"
//    ["total_fee"]=>
//    string(1) "1"
//    ["trade_mode"]=>
//    string(1) "1"
//    ["trade_state"]=>
//    string(1) "0"
//    ["transaction_id"]=>
//    string(28) "1218715401201405053186477338"
//    ["transport_fee"]=>
//    string(1) "0"
//  }

//OpenId of9wzuFWr0vQd1Bk0ONbspyXuXAY

//a:16:{s:9:"bank_type";s:4:"2032";s:8:"discount";s:1:"0";
//s:8:"fee_type";s:1:"1";s:13:"input_charset";s:3:"GBK";s:9:"notify_id";s:96:"NyjqpTaZCefJDslwkpWJFdvoLzvuEzp-fEyNMxaXfUYEILpsmX5NzSOAmpqmCatTugNMXYuEj7ayyfLLrM7sLK8Bt4miufEE";
//s:12:"out_trade_no";s:16:"FDM8CBvgtg10HbfV";s:7:"partner";s:10:"1218715401";s:11:"product_fee";s:1:"1";
//s:4:"sign";s:32:"99FC388BE8EDAAF97AFA7CCAC9776CB3";s:9:"sign_type";s:3:"MD5";
//s:8:"time_end";s:14:"20140506185330";s:9:"total_fee";s:1:"1";s:10:"trade_mode";s:1:"1";s:11:"trade_state";s:1:"0";
//s:14:"transaction_id";s:28:"1218715401201405063224576497";s:13:"transport_fee";s:1:"0";}

include_once("WxPayHelper.php");

$wxPayHelper = new WxPayHelper();

$transid = '1218715401201405163384332657';
$out_trade_no = 'vZV0mA1On58inUfL';

$arr = array();
$arr['appid'] = $test->appid;
$arr['appkey'] = APPKEY;
$arr['openid'] = 'of9wzuFWr0vQd1Bk0ONbspyXuXAY';
$arr['transid'] = $transid;
$arr['out_trade_no'] = $out_trade_no;
$arr['deliver_timestamp'] = time();
$arr['deliver_status'] = 1;
$arr['deliver_msg'] = 'ok';

$app_signature = $wxPayHelper->get_biz_sign($arr);

$params = array();
$params['appid'] = $test->appid;
$params['openid'] = 'of9wzuFWr0vQd1Bk0ONbspyXuXAY';
$params['transid'] = $transid;
$params['out_trade_no'] = $out_trade_no;
$params['deliver_timestamp'] = time();
$params['deliver_status'] = 1;
$params['deliver_msg'] = 'ok';
$params['app_signature'] = $app_signature;
$params['sign_method'] = 'sha1';

$data = json_encode($params);

echo "<pre>";
var_dump($test->delivery($data));die();

$openid = 'of9wzuFWr0vQd1Bk0ONbspyXuXAY';
$feedbackid = '13273194965010126084';
var_dump($test->updatefeedback($openid,$feedbackid));die();

//var_dump($test->postNotice());die();




?>