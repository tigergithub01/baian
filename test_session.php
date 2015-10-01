<?php 
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');


$data = $db->query('SELECT * 
FROM  `ecs_goods` 
WHERE 1 
AND purchase_price =0
AND goods_id <1992
ORDER BY goods_id DESC ');

while($val= mysql_fetch_array($data)){
	echo $val[0]."&nbsp;".$val[1]."&nbsp;".$val[2]."&nbsp;".$val[3]."&nbsp;".$val[4]."&nbsp;".$val[5]."&nbsp;".$val[6]."&nbsp;".$val[7]."&nbsp;".$val[8]."&nbsp;".$val[9].
	$val[10]."&nbsp;".$val[11]."&nbsp;".$val[12]."&nbsp;".$val[13]."<br>";

}
die();