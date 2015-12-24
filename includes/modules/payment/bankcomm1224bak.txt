<?php

/**
 * 交通银行在线支付插件
 * ============================================================================
 * $Author: aaliwen $
 * $Id: bankcomm.php aaliwen $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/bankcomm.php';

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
    $modules[$i]['desc']    = 'bankcomm_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 支付费用，由配送决定 */
    $modules[$i]['pay_fee'] = '0';

    /* 作者 */
    $modules[$i]['author']  = 'aaliwen';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.XXXXXX.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0.0';

    /* 配置信息 */
    $modules[$i]['config']  = array(
	);

    return;
}

/**
 * 类
 */
class bankcomm
{
	function __construct()
	{
		$this->bankcomm();
	}
    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function bankcomm()
    {
        //define('JAVA_DEBUG', true); //调试设置	
        define('JAVA_HOSTS', '127.0.0.1:8080'); //设置javabridge监听端口，如果开启javabridge.jar设置的端口不是8080，可通过此语句更改

        require_once(ROOT_PATH.'includes/java/Java.inc'); //php调用java的接口，必须的

        $libPath = realpath('/') . 'bocommjava/';

        java_set_library_path($libPath . 'lib'); //设置java开发包路径
        java_set_file_encoding('GBK');      //设置java编码
        $client=new java('com.bocom.netpay.b2cAPI.BOCOMB2CClient');
        $ret=$client->initialize($libPath.'ini/B2CMerchant.xml');
        $ret = java_values($ret);
        if ($ret != "0")
        {
            $err=$client->getLastErr();
            //为正确显示中文对返回java变量进行转换，如果用java_set_file_encoding进行过转换则不用再次转换
            //$err = java_values($err->getBytes('GBK')); 
            $err=java_values($err);
            print '初始化失败,错误信息：' . $err . '<br>';
            exit(1);
        }
    }

    

    /**
     * 提交函数
     */
    function get_code($order, $payment)
    {

        //获得java对象
        $BOCOMSetting=java('com.bocom.netpay.b2cAPI.BOCOMSetting');

        //获得表单传过来的数据
        $interfaceVersion = '1.0.0.0'; //*消息版本号
        $merID            = java_values($BOCOMSetting->MerchantID); //*商户号为固定
        $orderid          = $order['order_sn']; //*订单编号
        $orderDate        = date('Ymd', $order['add_time']); //*商户订单日期
        $orderTime        = date('His', $order['add_time']); //*商户订单时间
        $tranType         = '0'; //*交易类别0:B2C
        $amount           = $order['order_amount']; //*订单总金额
        $curType          = 'CNY';//*交易币种
        $orderContent     = ''; //订单内容
        $orderMono        = ''; //商家备注
        $phdFlag          = '0'; //物流配送标志
        $notifyType       = '1'; //*通知方式0 不通知 1 通知 2 抓取页面
        $merURL           = return_url(basename(__FILE__, '.php')); //主动通知URL
        $goodsURL         = ''; //取货URL
        $jumpSeconds      = '1'; // 自动跳转时间（秒）
        $payBatchNo       = ''; // 商户批次号(商家对账使用)
        $proxyMerName     = ''; // 代理商家名称
        $proxyMerType     = ''; // 代理商家类型
        $proxyMerCredentials = ''; // 代理商家证件号码
        $netType          = '0'; //*渠道编号0:html渠道
        /* 交易参数 */
        $parameter = array(
            'interfaceVersion' => $interfaceVersion,
            'merID'            => $merID,
            'orderid'          => $orderid,
            'orderDate'        => $orderDate,
            'orderTime'        => $orderTime,
            'tranType'         => $tranType,
            'amount'           => $amount,
            'curType'          => $curType,
            'orderContent'     => $orderContent,
            'orderMono'        => $orderMono,
            'phdFlag'          => $phdFlag,
            'notifyType'       => $notifyType,
            'merURL'           => $merURL,
            'goodsURL'         => $goodsURL,
            'jumpSeconds'      => $jumpSeconds,
            'payBatchNo'       => $payBatchNo,
            'proxyMerName'     => $proxyMerName,
            'proxyMerType'     => $proxyMerType,
            'proxyMerCredentials' => $proxyMerCredentials,
            'netType'          => $netType,
        );

        //连接字符串
        $source = '';
        $source = implode('|', $parameter);
        $sourceMsg = new java('java.lang.String', $source);

        //下为生成数字签名
        $nss = new java('com.bocom.netpay.b2cAPI.NetSignServer');

        $merchantDN = $BOCOMSetting->MerchantCertDN;
        $nss->NSSetPlainText($sourceMsg->getBytes('GBK'));

        $bSignMsg = $nss->NSDetachedSign($merchantDN);
        $signMsg = new java('java.lang.String', $bSignMsg, 'GBK');

        $parameter['merSignMsg'] = java_values($signMsg);

        $button  = '<br /><form action="'.java_values($BOCOMSetting->OrderURL).'" target="_blank" method="post" style="text-align:center;margin:0px;padding:0px" >';

        foreach ($parameter AS $key=>$val)
        {
            $button  .= '<input type="hidden" name="'.$key.'" value="'.$val.'" />';
        }

        $button  .= '<input type="submit" value="' . $GLOBALS['_LANG']['pay_button'] . '" ONclick="formsto=false;" class="input_10font_red">'.'</form><br />';
        return $button;
    }

    /**
     * 处理函数
     */
    function respond()
    {
        $notifyMsg = $_REQUEST['notifyMsg'];
        $lastIndex = strripos($notifyMsg, '|');
        $signMsg   = substr($notifyMsg, $lastIndex + 1);   //签名信息
        $srcMsg    = substr($notifyMsg, 0, $lastIndex + 1); //原文

        $signMsg   = new java('java.lang.String', $signMsg);
        $srcMsg    = new java('java.lang.String', $srcMsg);
        $veriyCode = -1;

        $nss       = new java('com.bocom.netpay.b2cAPI.NetSignServer');
        $nss->NSDetachedVerify($signMsg->getBytes('GBK'), $srcMsg->getBytes('GBK')); //验签

        $veriyCode = java_values($nss->getLastErrnum());

        if ($veriyCode < 0) {
            error_log('bankcomm_veriyCode_faild');
            return false;
        }
        $arr=preg_split("/\|{1,}/", java_values($srcMsg));
        if ($arr[9] == '1')
		{
            $orderId = get_order_id_by_sn($arr[1]);
            if ($orderId > 0)
            {/* 改变订单状态 */
                order_paid($orderId);
            }
			return true;
		}
        else
        {
            error_log('bankcomm_veriyCode_faild_2');
            return false;
        }
    }
}

?>