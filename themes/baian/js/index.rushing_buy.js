//使用div时，请保证colee_left2与colee_left1是在同一行上.
var speed=15;
var tab=document.getElementById("mscroll");
var tab1=document.getElementById("scroll1");
var tab2=document.getElementById("scroll2");
tab2.innerHTML=tab1.innerHTML;
var left = tab.scrollLeft;
function Marquee(){
	//alert(tab2.offsetWidth);
	//alert(tab.scrollLeft);
	if(tab2.offsetWidth-tab.scrollLeft<=0){
		tab.scrollLeft-=tab1.offsetWidth;
	}
	else{
		tab.scrollLeft++;
	}
}
var MyMar=setInterval(Marquee,10);
tab.onmouseover=function() {clearInterval(MyMar)};
tab.onmouseout=function() {MyMar=setInterval(Marquee,10)};