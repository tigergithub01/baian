<?php 
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if(!empty($_SESSION['admin_id']) && !empty($_SESSION['user_name']) && $_SESSION['admin_id']==1 && $_SESSION['user_name']=='admin'){
	die('yes');
}else{
	die('no');
}
