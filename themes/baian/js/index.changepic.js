var t; 
var speed = 2;//图片切换速度 
var nowlan=0;//图片开始时间 
function changepic(v) { 
var imglen = $(v).find("li").length; /*alert(imglen);*/
$(v).find("li").fadeOut(); 
$(v).find("li").eq(nowlan).fadeIn(); 
nowlan = ((nowlan+1)==imglen)?0:nowlan + 1; /*alert(nowlan);*/
t = setTimeout("changepic('"+v+"')", speed * 1000); 
//hover(v);
//leaver(v);
} 
function hover(v){
	$(v).hover(function () { clearInterval(t); }); 
}
function leaver(v){
	$(v).mouseleave(function () { changepic(v); }); 
}