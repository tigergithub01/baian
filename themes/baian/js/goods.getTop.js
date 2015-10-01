var obj11 = document.getElementById("inner");
var top11 = getTop(obj11);
var isIE6 = /msie 6/i.test(navigator.userAgent);
window.onscroll = function(){

	var bodyScrollTop = document.documentElement.scrollTop || document.body.scrollTop;
	if (bodyScrollTop > top11){
		//obj11.style.position = (isIE6) ? "absolute" : "fixed";
		obj11.style.position = (isIE6) ? "relative" : "fixed";
		obj11.style.top = (isIE6) ? bodyScrollTop + "px" : "0px"; 
		document.getElementById("flowChart").className="flow_show";
		for(i=1;i<=4;i++){
			document.getElementById("goods"+i).firstChild.href="#product-detail";
		}
	} else {
		obj11.style.position = "static";
		document.getElementById("flowChart").className="flow_hidden";
		for(i=1;i<=4;i++){
			document.getElementById("goods"+i).firstChild.href="#detail";
		}
	}
}
function getTop(e){

	var offset = e.offsetTop;
	if(e.offsetParent != null) offset += getTop(e.offsetParent);
	return offset;
}