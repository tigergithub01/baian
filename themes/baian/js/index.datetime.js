//+---------------------------------------------------  
//| 取得日期数据信息  
//| 参数 interval 表示数据类型  
//| y 年 m月 d日 w星期 ww周 h时 n分 s秒  
//+---------------------------------------------------  
function DatePart(interval)  
{   
	var myDate = new Date();  
	var partStr='';  
	var Week = ['日','一','二','三','四','五','六'];  
	switch (interval)  
	{   
		case 'y' :partStr = myDate.getFullYear();break;  
		case 'm' :partStr = myDate.getMonth()+1;break;  
		case 'd' :partStr = myDate.getDate();break;  
		case 'w' :partStr = Week[myDate.getDay()];break;  
		case 'ww' :partStr = myDate.WeekNumOfYear();break;  
		case 'h' :partStr = myDate.getHours();break;  
		case 'n' :partStr = myDate.getMinutes();break;  
		case 's' :partStr = myDate.getSeconds();break;  
	}  
	return partStr;  
} 

$(document).ready(function(){

	var width = window.screen.width;
	document.cookie="screen="+width;
	
	var timestr = DatePart("y")+"年"+DatePart("m")+"月"+DatePart("d")+"日星期"+DatePart("w");
	$("#times").text(timestr);
});