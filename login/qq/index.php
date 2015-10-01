<?php

/*
 *调用接口代码
 *
 **/
//require_once("../../API/qqConnectAPI.php");
//$qc = new QC();

//echo '<pre>';
//var_dump($qc);die();

//APP ID：100583866
//APP KEY：0d48968dd87c3c06673a5e8a94d02e4b
$redirect_uri = urlencode('www.123121.com/login/qq/callback.php');//die($redirect_uri);

$res = 'https://graph.qq.com/oauth2.0/authorize?'.
	"response_type=token&client_id=100583866&redirect_uri=$redirect_uri&scope=get_user_info";

header("Location:$res");
echo '<pre>';
var_dump($res);
die();





$arr = $qc->get_user_info();

echo '<pre>';
var_dump($arr);die();


echo '<meta charset="UTF-8">';
echo "<p>";
echo "Gender:".$arr["gender"];
echo "</p>";
echo "<p>";
echo "NickName:".$arr["nickname"];
echo "</p>";
echo "<p>";
echo "<img src=\"".$arr['figureurl']."\">";
echo "<p>";
echo "<p>";
echo "<img src=\"".$arr['figureurl_1']."\">";
echo "<p>";
echo "<p>";
echo "<img src=\"".$arr['figureurl_2']."\">";
echo "<p>";
echo "vip:".$arr["vip"];
echo "</p>";
echo "level:".$arr["level"];
echo "</p>";
echo "is_yellow_year_vip:".$arr["is_yellow_year_vip"];
echo "</p>";

?>
