<?php 
    $appid = "269574350000034684";
    $appsecret = "1d3e66f20f441eedf01bd9910e1ab082";
    $redirectUri = "http://www.123121.com/login/189/redirect.php";
    $authorizeAPI = "https://oauth.api.189.cn/emp/oauth2/v2/authorize";
    $tokenAPI = "https://oauth.api.189.cn/emp/oauth2/v2/access_token";
/**
 * curl�ຯ��
 */
//post��ʽ�ύ��ȡ���
function curl_post($url='', $postdata='', $options=array()){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    if (!empty($options)){
        curl_setopt_array($ch, $options);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

//get��ʽ�ύ��ȡ���
function curl_get($url='', $options=array()){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    if (!empty($options)){
        curl_setopt_array($ch, $options);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
?>