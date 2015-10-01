<?php
function GetHtmlCode($filee)
{
$str=file_get_contents($filee);
return $str;
}

function Getkeywords()
{
	  //分词搜索
		require("fenci/lib_splitword_full.php");
		function ExecTime()
		{ 
			$time = explode(" ", microtime());
			$usec = (double)$time[0]; 
			$sec = (double)$time[1]; 
			return $sec + $usec; 
		}
		$str = $_REQUEST['keywords'];//搜索的内容
		$sp = new SplitWord();
		$SearchString= $sp->SplitRMM($str);
		$SearchSplit= explode("|",trim($str) . "|" . $SearchString);
		foreach ($SearchSplit as &$value) { //重组
			
			if (trim($value)<>"")
			{
			   $SQLXX= $SQLXX . "$value|" ;
			 }
		}
		
		$sp->Clear();
		return explode("|",$SQLXX);	
}


function SetColor($arr,$Str) //颜色
{
				foreach ($arr as &$value) {
					$Str = str_replace($value ,"<span style='color:#F00'>$value</span>",$Str);
				}
				return $Str;
}
function Fenci($SearchSplit)
{
		 $SQLXX="";
		foreach ($SearchSplit as &$value) {
			if ($value<>"")
			{
			   $SQLXX= $SQLXX . " or g.goods_name like '%$value%'";
			 }
		}
		return $SQLXX;
	
		 //分词搜索
}
$KeySplit=Getkeywords(); 
?>