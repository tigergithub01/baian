<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<title>PHP简易中文分词(SCWS) 第4版在线演示 (by hightman)</title>
</head>
<body>


<?php
$cws = scws_new();
$cws->set_charset('utf8');
$cws->set_ignore('yes');
//$cws->set_respond('xml'); // php/json/xml

// 这里没有调用 set_dict 和 set_rule 系统会自动试调用 ini 中指定路径下的词典和规则文件
$cws->send_text("三段 奶粉");
// $cws->send_text("奶粉,");

$result = array();

while ($tmp = $cws->get_result())
{
	foreach ($tmp as $key => $value) {
// 		var_dump($value['word']);
		$result[] = $value['word'];
	}
//   print_r($tmp);
}
var_dump($result);


$cws->close();


?>

</body>
</html>