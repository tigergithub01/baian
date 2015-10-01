<?php
/**
 * ECSHOP 导出会员信息插件
 * ============================================================================
 * 作者: pgge
 *   QQ: 1101728473
*/
define('IN_ECS', true);
require(dirname(__FILE__) . '/../../includes/init.php');


$keywords = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
$rank = empty($_REQUEST['rank']) ? 0 : intval($_REQUEST['rank']);
$birth_pr = empty($_REQUEST['birth_pr']) ? 0 : intval($_REQUEST['birth_pr']);
$pay_points_gt = empty($_REQUEST['pay_points_gt']) ? 0 : intval($_REQUEST['pay_points_gt']);
$pay_points_lt = empty($_REQUEST['pay_points_lt']) ? 0 : intval($_REQUEST['pay_points_lt']);
$start_time = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
$end_time = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);

$ex_where = ' WHERE 1 ';
if($keywords){
	$ex_where .= " AND user_name LIKE '%" . mysql_like_quote($keywords)."%'";
}
if ($birth_pr) /* 生日提示 */
{
	$birthday_prompt = $GLOBALS['db']->GetOne("SELECT `value` FROM " .$GLOBALS['ecs']->table('shop_config') . " WHERE `code`= 'birthday_prompt' LIMIT 1");
	$ex_where .= " AND MONTH(baby_birthday)=MONTH(NOW()) AND DAY(baby_birthday)-DAY(NOW())>=0 AND DAY(baby_birthday)-DAY(NOW())<=" .$birthday_prompt;
}
	if ($rank) 
	{
		$sql = "SELECT min_points, max_points, special_rank FROM ".$GLOBALS['ecs']->table('user_rank')." WHERE rank_id = '$rank'";
		$row = $GLOBALS['db']->getRow($sql);
	if ($row['special_rank'] > 0)
	{
		 /* 特殊等级 */
		$ex_where .= " AND user_rank = '$rank' ";
	}
	else
	{
		$ex_where .= " AND rank_points >= " . intval($row['min_points']) . " AND rank_points < " . intval($row['max_points']);
	}
        }
        if ($pay_points_gt)
        {
             $ex_where .=" AND pay_points >= '$pay_points_gt' ";
        }
        if ($filter['pay_points_lt'])
        {
            $ex_where .=" AND pay_points < '$pay_points_lt' ";
        }
if (!empty($start_time))
{
	$ex_where .= " AND reg_time >= '$start_time' ";
}
if (!empty($end_time))
{
	$ex_where .= " AND reg_time < '$end_time' ";
}

        $sql = "SELECT user_id, user_name, email, mobile_phone, sex, birthday,pay_points, rank_points, reg_time,last_time,last_ip,visit_count,user_rank,baby_sex, baby_birthday ".
                " FROM " . $GLOBALS['ecs']->table('users') . $ex_where .
                " ORDER by user_id desc ";
		
//echo "<pre>";var_dump($sql);die();		
		
header("Content-type:application/vnd.ms-excel");
header("Accept-Ranges:bytes");
header("Content-Disposition:filename=members_".time().".xls");
header("Pragma: no-cache");

echo '
	<html xmlns:o="urn:schemas-microsoft-com:office:office"
	xmlns:x="urn:schemas-microsoft-com:office:excel"
	xmlns="http://www.w3.org/TR/REC-html40">
	<head>
	<meta http-equiv="expires" content="Mon, 06 Jan 1999 00:00:01 GMT">
	<meta http-equiv=Content-Type content="text/html; charset=utf-8">
	<!--[if gte mso 9]><xml>
	<x:ExcelWorkbook>
	<x:ExcelWorksheets>
	<x:ExcelWorksheet>
	<x:Name></x:Name>
	<x:WorksheetOptions>
	<x:DisplayGridlines/>
	</x:WorksheetOptions>
	</x:ExcelWorksheet>
	</x:ExcelWorksheets>
	</x:ExcelWorkbook>
	</xml><![endif]-->
	</head>
';

echo '<table>';
echo '<tr>';
echo '<td>编号</td>';
echo '<td>用户名</td>';
echo '<td>邮箱</td>';
echo '<td>手机号</td>';
echo '<td>性别</td>';
echo '<td>生日</td>';
echo '<td>消费积分</td>';
echo '<td>等级积分</td>';
echo '<td>注册时间</td>';
echo '<td>最后登录时间</td>';
echo '<td>上次登录ip</td>';
echo '<td>访问次数</td>';
echo '<td>用户等级</td>';
echo '<td>宝宝性别</td>';
echo '<td>宝宝生日</td>';
echo '</tr>';
		
$res = $GLOBALS['db']->query($sql);
while ($row = $GLOBALS['db']->fetchRow($res))
{
	echo '<tr>';
	echo "<td style='vnd.ms-excel.numberformat:@'>$row[user_id]</td>";
	echo "<td>$row[user_name]</td>";
	echo "<td>$row[email]</td>";
	echo "<td>$row[mobile_phone]</td>";
	if($row[sex] == 1){echo "<td>男</td>";} else if($row[sex] == 2){echo "<td>女</td>";} else{echo "<td>保密</td>";} 
	echo "<td>".date("Y-m-d H:i:s",$row['birthday'])."</td>";
	echo "<td>$row[pay_points]</td>";
	echo "<td>$row[rank_points]</td>";
	echo "<td>".date("Y-m-d H:i:s",$row['reg_time'])."</td>";
	echo "<td>".date("Y-m-d H:i:s",$row['last_time'])."</td>";
	echo "<td>".$row[last_ip]."</td>";
	echo "<td>$row[visit_count]</td>";
	echo "<td>$row[user_rank]</td>";
	if($row[baby_sex] == 1){echo "<td>男</td>";} else if($row[baby_sex] == 2){echo "<td>女</td>";} else{echo "<td>保密</td>";} 
	echo "<td>".date("Y-m-d H:i:s",$row['baby_birthday'])."</td>";
	echo '</tr>';
}

echo '</table>';
?>