var t; 
var speed = 2;//鍥剧墖鍒囨崲閫熷害 
var nowlan=0;//鍥剧墖寮€濮嬫椂闂� 


function changepicimg(v,obj) { 
var imglen = $(v).find("li").length; 
//alert(imglen);

if(nowlan==1){
$(v).find("li").eq("1").fadeOut(); 
$(v).find("li").eq("0").fadeIn(); 
}
if(nowlan==0){
	$(v).find("li").eq("0").fadeOut(); 
$(v).find("li").eq("1").fadeIn(); 
	}
$(v).find("li").fadeOut();
$(v).find("li").eq(nowlan).fadeIn();
 
nowlan = ((nowlan+1)==imglen)?0:nowlan + 1; 
//alert(nowlan);
obj = setTimeout("changepicimg('"+v+"')", 2500); 
hover(v);
leaver(v);

} 



function hover(v){
	$(v).hover(function () { clearInterval(t); }); 
}
function leaver(v){
	$(v).mouseleave(function () { changepic(v); }); 
}