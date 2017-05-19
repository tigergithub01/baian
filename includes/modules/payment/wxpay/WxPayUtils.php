<?php


class WxPayUtils{
	
	private $app_id;
	private $mch_id;
	private $app_key;
	
	/**
	 * 初始化
	 */
	function __construct() {
		$this->app_id = \Yii::$app->params['wx_pay']['app_id'];
		$this->mch_id = \Yii::$app->params['wx_pay']['mch_id'];
		$this->app_key = \Yii::$app->params['wx_pay']['app_key'];
	}	
	
	/**
	 * Generate a nonce string
	 *
	 * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=4_3
	 */
	function generateNonce()
	{
		return md5(uniqid('', true));
	}
	
	/**
	 * Get a sign string from array using app key
	 *
	 * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=4_3
	 */
	function calculateSign($arr)
	{
		ksort($arr);
	
		$buff = "";
		foreach ($arr as $k => $v) {
			if ($k != "sign" && $k != "key" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
	
		$buff = trim($buff, "&");
	
		return strtoupper(md5($buff . "&key=" . $this->app_key));
	}
	
	/**
	 * Get xml from array
	 */
	function getXMLFromArray($arr)
	{
		$xml = "<xml>";
		foreach ($arr as $key => $val) {
			if (is_numeric($val)) {
				$xml .= sprintf("<%s>%s</%s>", $key, $val, $key);
			} else {
				$xml .= sprintf("<%s><![CDATA[%s]]></%s>", $key, $val, $key);
			}
		}
	
		$xml .= "</xml>";
	
		return $xml;
	}
	
	/**
	 * Generate a prepay id
	 *
	 * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=9_1
	 */
	function generatePrepayId($order_code, $body, $total_fee, $notify_url)
	{
		$params = array(
				'appid'            => $this->app_id,
				'mch_id'           => $this->mch_id,
				'nonce_str'        => $this->generateNonce(),
				'body'             => $body,
				'out_trade_no'     => $order_code .'-'. CommonUtils::random(5, 1),
				'total_fee'        => $total_fee,
				'spbill_create_ip' => '8.8.8.8',
				'notify_url'       => $notify_url,
				'trade_type'       => 'APP',
		);
	
		// add sign
		$params['sign'] = $this->calculateSign($params);
		
		// create xml
		$xml = $this->getXMLFromArray($params);
	
		\Yii::info('-----generatePrepayId params-------- ');
		\Yii::info($xml);
	
		// send request
		$ch = curl_init();
	
		curl_setopt_array($ch, array(
				CURLOPT_URL            => "https://api.mch.weixin.qq.com/pay/unifiedorder",
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER     => array('Content-Type: text/xml'),
				CURLOPT_POSTFIELDS     => $xml,
				CURLOPT_SSL_VERIFYPEER => false
		));
	
		$result = curl_exec($ch);
		//var_dump(curl_error($ch));
		curl_close($ch);
	
		// get the prepay id from response
		$xml = simplexml_load_string($result);
		//var_dump($result);
		return (string)$xml->prepay_id;
	}
	
	/**
	 
	 * @param unknown $body
	 * @param unknown $total_fee
	 * @param unknown $notify_url
	 */
	public function sendPayReq($order_code, $body, $total_fee, $notify_url){
		$prepay_id = $this->generatePrepayId($order_code, $body, $total_fee, $notify_url);
		
		// re-sign it
		$response = array(
				'appid'     => $this->app_id,
				'partnerid' => $this->mch_id,
				'prepayid'  => $prepay_id,
				'package'   => 'Sign=WXPay',
				'noncestr'  => $this->generateNonce(),
				'timestamp' => time(),
		);
		$response['sign'] = $this->calculateSign($response);
		
		\Yii::info('-----sendPayReq re-sign params-------- ');
		\Yii::info(json_encode($response));	
		
		return $response;
	}
	
	/**
	 * 将xml转为array
	 */
	public function FromXml()
	{
		//获取微信post或来的xml数据
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		
		if(!$xml){
			return null;
		}
		//将XML转为array
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		
		$str =  json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
		\Yii::info('-----FromXml str-------- ');
		\Yii::info($str); //输出到日志
		$values = json_decode($str, true); //转换为数组
		return $values;
	}
	
	/**
	 * 微信回调处理
	 * @return \app\models\b2b2c\common\JsonObj
	 */
	public function notify(){
		$jsonObj = new JsonObj(false);
		
		//获取返回的数组
		$values = $this->FromXml();
		$jsonObj->value = $values;
		
		//获取返回结果
		if($values['return_code'] != 'SUCCESS'){
			$jsonObj->message = '支付出错!';
			return $jsonObj;
		}
		
		//验证签名		
		$sign= $this->calculateSign($values);
		if($values['sign'] != $sign){
			$jsonObj->message = '签名验证错误!';
			return $jsonObj;
		}
		
		//支付回调成功
		$jsonObj->status = true;
		return $jsonObj;		
	}
	
	
}