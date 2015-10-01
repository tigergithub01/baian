<?php 
//header("Location:http://m.baidu.com/app?action=content&docid=6182325&f=pc");


$ua = @strtolower($_SERVER['HTTP_USER_AGENT']);

//iphone客户端微信中打开
if(preg_match("/iphone/", $ua)){
	//微信中打开
	if(preg_match("/micromessenger/", $ua)){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
		die('<font size="7" color="red">在微信浏览器中无法下载ios版app，请点击右上角在浏览器中打开下载！</font>');
	}else{
		header("Location:http://www.123121.com/download/baian.ipa");
		die();
	}
}else{
	header("Location:http://a.app.qq.com/o/simple.jsp?pkgname=com.chuanshuo.tbbaian&g_f=995295");
}

exit();

$file_name="download/Android.apk";//需要下载的文件
//$file_name=iconv("utf-8","gb2312","$file_name");
$fp=fopen($file_name,"r+");//下载文件必须先要将文件打开，写入内存
if(!file_exists($file_name)){//判断文件是否存在
    echo "文件不存在";
    exit();
}
//$file_name = 'baian.apk';
$file_size=filesize($file_name);//判断文件大小
//返回的文件
Header("Content-type: application/octet-stream");
//按照字节格式返回
Header("Accept-Ranges: bytes");
//返回文件大小
Header("Accept-Length: ".$file_size);
//弹出客户端对话框，对应的文件名
Header("Content-Disposition: attachment; filename=baian_android.apk");
//防止服务器瞬时压力增大，分段读取
$buffer=1024;
while(!feof($fp)){
    $file_data=fread($fp,$buffer);
    echo $file_data;
}
//关闭文件
fclose($fp);


?>