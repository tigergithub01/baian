<?php

/**
 * ECSHOP 微信支付插件 V3.3版本
 * ============================================================================
 * * 版权所有 2005-2014 麦穗工作室 版权所有
 * 网站地址: http://www.maisui.net.cn；
 */

if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/wxpay.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'wxpay_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = '麦穗工作室';

    /* 网址 */
    $modules[$i]['website'] = 'http://mp.weixin.qq.com/';

    /* 版本号 */
    $modules[$i]['version'] = '3.3';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'wxpay_app_id',           'type' => 'text',   'value' => 'wx54747450599e343c'),
        array('name' => 'wxpay_app_secret',       'type' => 'text',   'value' => 'dc4c70f5414c6eb600faf2db9352fb7f'),
        array('name' => 'wxpay_partnerid',      'type' => 'text',   'value' => '1218715401'),		
        array('name' => 'wxpay_partnerkey',      'type' => 'text', 'value' => 'b1a2i3a1n21YRSwentaoweiwang19768'),
// 		array('name' => 'wxpay_signtype',      'type' => 'text', 'value' => 'sha1')
    );

    return;
}

require_once ROOT_PATH . 'includes/cls_log.php';


/**
 * 类
 */

class wxpay
{

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
	var $parameters; //cft 参数
	var $payments; //配置信息
    /* function wxpay()
    {
    } */

    function __construct()
    {
    	$payment    = get_payment('wxpay');
    	
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
        if (!defined('EC_CHARSET'))
        {
            $charset = 'utf-8';
        }
        else
        {
            $charset = EC_CHARSET;
        }
		$charset = strtoupper($charset);
		
		$logHandler= new CLogFileHandler(ROOT_PATH. "logs/payment/wxpay/".date('Y-m-d').'.log');
		$log = Log::Init($logHandler, 15);
		
		
		
		//配置参数
		$this->payments = $payment;
		//查找openid
		//$orderuserid = $GLOBALS['db']->getOne("SELECT user_id FROM".$GLOBALS['ecs']->table('order_info')."WHERE order_id='$order[order_id]'");
		//$openid = $GLOBALS['db']->getOne("SELECT wxid FROM".$GLOBALS['ecs']->table('users')."WHERE user_id='$orderuserid'");
		$openid= isset($_SESSION['OPENID'])?$_SESSION['OPENID']:NULL;
		$this->setParameter("openid",$openid);
		//商品描述
		$this->setParameter("body", $order['order_sn']);
		//商户订单号
		$this->setParameter("out_trade_no", $order['order_sn']);
		//$this->setParameter("out_trade_no", $order['order_sn'] .'O'. $order['log_id']);
		//订单总金额
		$this->setParameter("total_fee", $order['order_amount'] * 100);
		//支付币种
		//$this->setParameter("fee_type", "1");
		//通知URL
		
		$this->setParameter("notify_url", $GLOBALS['ecs']->url() . 'respondWx.php');
		
		$log::DEBUG('notify_url==>' . $GLOBALS['ecs']->url() . 'respondWx.php');
		
// 		$this->setParameter("notify_url", return_url(basename(__FILE__, '.php')));
		//订单生成的机器IP
		$this->setParameter("spbill_create_ip", real_ip());
		//传入参数字符编码
		$this->setParameter("input_charset", $charset);
		//交易类型
		$this->setParameter("trade_type","JSAPI");
		
		//获取预付款编号
		$prepay_id = $this->getPrepayId();
		
		$this->setPrepayId($prepay_id);
		//生成jsapi支付请求json
		
		$jsapi = $this->getParameters();
		
		$log::DEBUG($jsapi);

		$button = <<<EOT
<div style="text-align:center"><input class="s-btn1" type="submit" value="{$GLOBALS['_LANG']['pay_button']}" onclick="callpay_{$order['order_sn']}()" /></div>
<script type="text/javascript">
// 当微信内置浏览器完成内部初始化后会触发WeixinJSBridgeReady事件。
// 其实中间部分可以写成一个事件，点击某个按钮触发
//document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	function jsApiCall(){
	     WeixinJSBridge.invoke('getBrandWCPayRequest',$jsapi,function(res){
	        if(res.err_msg == "get_brand_wcpay_request:ok" ) {
	            window.location.href = 'http://{$_SERVER['HTTP_HOST']}/mobile/user.php?act=order_list&status=101';
	        }else if(res.err_msg == "get_brand_wcpay_request:cancel" ) {
	            //alert('取消支付');
	        }else {
	            // 这里是取消支付或者其他意外情况，可以弹出错误信息或做其他操作
	           WeixinJSBridge.log(res.err_msg);
			   //alert(res.err_code+res.err_desc+res.err_msg);
				alert('支付请求不成功');
	           //alert('未知原因支付失败，请改用其他支付方式');
				
	        }
	     });
    }
		
//}, false)
	       
	function callpay_{$order['order_sn']}()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
</script>
		
EOT;
		return $button;

        
    }

    /**
     * 响应操作
     */
    function respond()
    {
    	$logHandler= new CLogFileHandler(ROOT_PATH. "logs/payment/wxpay/".date('Y-m-d').'.log');
    	$log = Log::Init($logHandler, 15);
    	$log::DEBUG("--start respond------");
    	
    	//获取返回的数组
    	$arr= $this->getReturnValues();
    	$log::DEBUG(json_encode($arr));
    	
    	//获取返回结果
    	if($arr['return_code'] != 'SUCCESS'){
    		$log::ERROR("支付不成功!");
    		return false;
    	}
    	
    	//验证签名
    	$sign= $this->getSign($arr);
    	$log::DEBUG("sign===> $sign");
//     	if($arr['sign'] != $sign){
//     		//签名验证错误!
//     		$log::ERROR("签名验证错误!");
//     		return false;
//     	}
    	
    	$order_sn   = $arr['out_trade_no'];
    	$log_id = get_order_id_by_sn($order_sn);
    	$log::DEBUG("order_sn==>$order_sn log_id===>$log_id");
    	
    	/* 检查支付的金额是否相符 */
    	if (!check_money($log_id, $arr['total_fee'] / 100))
    	{
    		return false;
    	}
    	
    	order_paid($log_id);
    	return true;
    }
    
    
    
    /**
     * 将xml转为array
     */
    public function getReturnValues()
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
    	$values = json_decode($str, true); //转换为数组
    	return $values;
    }
    

	// 设置请求参数
	function setParameter($parameter, $parameterValue) {
		$this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}

	// 设置参数时需要用到的字符处理函数	
	function trimString($value){
		$ret = null;
		if (null != $value) {
			$ret = $value;
			if (strlen($ret) == 0) {
				$ret = null;
			}
		}
		return $ret;
	}

	//生成随机数
	function create_noncestr( $length = 32 ) {  
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
		$str ="";  
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);			  
		}  
		return $str;  
	}
	
	
	 // 获取prepay_id	
	function getPrepayId()
	{
		$this->postXml();
		$this->result = $this->xmlToArray($this->response);
		//var_dump($this->result);
		$prepay_id = $this->result["prepay_id"];
		return $prepay_id;
	}
	
	
	 //	post请求xml
	function postXml()
	{
	    $xml = $this->createXml();		
		$this->response = $this->postXmlCurl($xml,'https://api.mch.weixin.qq.com/pay/unifiedorder','30');	
		//var_dump($this->response);
		return $this->response;
	}
	
	
	// 设置标配的请求参数，生成签名，生成接口参数xml
	function createXml()
	{
		$this->parameters["appid"] = $this->payments['wxpay_app_id'];//公众账号ID
	   	$this->parameters["mch_id"] = $this->payments['wxpay_partnerid'];//商户号
	    $this->parameters["nonce_str"] = $this->create_noncestr();//随机字符串
	    $this->parameters["sign"] = $this->getSign($this->parameters);//签名		
	    return  $this->arrayToXml($this->parameters);
	}
	
	function xmlToArray($xml)
	{		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}
	
	function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
        	 if (is_numeric($val))
        	 {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 

        	 }
        	 else
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";		
        return $xml; 
    }

	function postXmlCurl($xml,$url,$second=30)
	{		
        //初始化curl        
       	$ch = curl_init();		
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
        $data = curl_exec($ch);
		curl_close($ch);		
		//var_dump($data);
		if($data)
		{
			//curl_close($ch);
			return $data;
		}
		else 
		{ 
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>"; 
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}	
	}
	
	//	作用：生成签名
	public function getSign($Obj)
	{
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$this->payments['wxpay_partnerkey'];
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
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
	 * 	格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}
	
	function setPrepayId($prepayId)
	{
		$this->prepay_id = $prepayId;
	}
	/**
	 * 	设置jsapi的参数
	 */
	function getParameters()
	{
		$jsApiObj["appId"] = $this->payments['wxpay_app_id'];
		$timeStamp = time();
	    $jsApiObj["timeStamp"] = "$timeStamp";
	    $jsApiObj["nonceStr"] = $this->create_noncestr();
		$jsApiObj["package"] = "prepay_id=$this->prepay_id";
	    $jsApiObj["signType"] = "MD5";
	    $jsApiObj["paySign"] = $this->getSign($jsApiObj);
	    $this->parameters = json_encode($jsApiObj);		
		return $this->parameters;
	}
	
	
}

?>