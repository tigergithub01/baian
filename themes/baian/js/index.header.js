function tops(){
  var doc = (document.documentElement.scrollTop) ? document.documentElement : document.body;
  var time;
  time = setInterval(function(){
  doc.scrollTop -= Math.ceil(doc.scrollTop * 0.7);
  if(doc.scrollTop <= 0)  clearInterval(time);
  }, 1);
}








window.onload = function()
{
  fixpng();
}
function checkSearchForm()
{
    if(document.getElementById('keyword').value)
    {
        return true;
    }
    else
    {
		    alert("请输入关键词！");
        return false;
    }
}

function myValue1()
{
	document.getElementById('keyword').value = "请输入商品名称或编号...";
}

function myValue2()
{
	document.getElementById('keyword').value = "";
}









//var $$=jQuery.noConflict();

/* *
* 清除购物车购买商品数量
*/
function delet(rec_id)
{
	var formBuy      = document.forms['formCart'];
	var domname='goods_number_'+rec_id;
	var attr = getSelectedAttributes(document.forms['formCart']);
	var qty = parseInt(document.getElementById(domname).innerHTML)==0;
	Ajax.call('flow.php', 'step=price&rec_id=' + rec_id + '&number=' + qty, changecartPriceResponse, 'GET', 'JSON');
}
/* *
* 增加购物车购买商品数量
*/
function addcartnum(rec_id)
{
	var attr = getSelectedAttributes(document.forms['formCart']);
	var domname='goods_number_'+rec_id;
	var qty = parseInt(document.getElementById(domname).innerHTML)+1;
	Ajax.call('flow.php', 'step=price&rec_id=' + rec_id + '&number=' + qty, changecartPriceResponse, 'GET', 'JSON');
}
/* *
* 减少购买商品数量
*/
function lesscartnum(rec_id)
{
	var formBuy      = document.forms['formCart'];
	var domname='goods_number_'+rec_id;
	var attr = getSelectedAttributes(document.forms['formCart']);
	var qty = parseInt(document.getElementById(domname).innerHTML)-1;
	Ajax.call('flow.php', 'step=price&rec_id=' + rec_id + '&number=' + qty, changecartPriceResponse, 'GET', 'JSON');
}
/**
* 接收返回的信息
*/
function changecartPriceResponse(res)
{
	if (res.err_msg.length > 0 )
	{
		alert(res.err_msg);
	}
	else
	{
		var domnum='goods_number_'+res.rec_id;
		if(res.qty <= 0){
			document.getElementById('CART_INFO').innerHTML = res.content1;
		}else{
			document.getElementById(domnum).innerHTML = res.qty;
		}
		document.getElementById('ECS_CARTINFO').innerHTML = res.result;
	}
}


function changallser(allser)
{
	document.getElementById(allser).className='item fore';
}


function closeLAD(){
	$("#cproIframe4holder").css("display","none");
}

$(document).ready(function(){
	$("#ta1").css("display","none");
	$("#ta2").css("display","block");
	$("#snActive-wrap").css("height","500px");
	
	$("#kfao").bind("mouseenter",function(event){
	  $("#kefu").animate({width:159},400,function(){});
	  event.stopPropagation();
	});

	$("#kefu").bind("mouseleave",function(event){
		$("#kefu").animate({width:24},400,function(){});
	});

	setTimeout(function(){
		var snActive = $("#snActive-wrap");
		var hideImg = snActive.find('a:hidden');
		var em = $("#ems");
		
		snActive.animate({height:40},600,function(){
			hideImg.siblings('a:visible').hide();
			hideImg.show();
			em.bind("click",function(){
				snActive.slideUp(300);
			});
		});
	},3000);
});


function goTop() {	//跳到顶部
	$('html,body').animate({scrollTop:0},200); 
}

$(function($){
	
	//$('#backtops').click(function(){
	//	goTop();
	//});	
	//setIE6Fixed(document.getElementById('backtop'),{right:10,bottom:20});
	$(window).bind('scroll',function(){
		if($(window).scrollTop()>0){
			$('#backtop').fadeIn(300);
		}else {
			$('#backtop').fadeOut(300);	
		}
	});
	
});


	
// ie6中fixed定位函数
function setIE6Fixed(element){
	var isIE6 = !!window.ActiveXObject && !window.XMLHttpRequest;
	if(!isIE6){return 0;}
	
	var o = arguments[1];
	var left=0,right=0,top=0,bottom=0;
	if(o){
		left = o.left;
		right = o.right;
		top = o.top;
		bottom = o.bottom;
	}
	function setFixed(){
		if(right||right===0){
			var width = element.offsetWidth;
			//x = document.documentElement.scrollLeft+document.documentElement.clientWidth-(width+right);
			element.style.setExpression("left","eval(document.documentElement.scrollLeft+document.documentElement.clientWidth-"+(width+right)+")+'px'");
		}else {
			//x = document.documentElement.scrollLeft+left;
			element.style.setExpression("left","eval(document.documentElement.scrollLeft+"+left+")+'px'");
		}
		
		if(bottom||bottom===0){
			var height = element.offsetHeight;
			//y = document.documentElement.scrollTop+document.documentElement.clientHeight-(height+bottom);
			element.style.setExpression("top","eval(document.documentElement.scrollTop+document.documentElement.clientHeight-"+(height+bottom)+")+'px'");
		}else {
			//y = top + document.documentElement.scrollTop;
			element.style.setExpression("top", "eval(document.documentElement.scrollTop+"+top+")+'px'");
		}
		//element.style.left = x;	//此方法会抖动
		//element.style.top = y;	    	
	}
	
	 window.attachEvent('onscroll',function(){
		setFixed();
	 });
	 window.attachEvent('onresize',function(){
		setFixed();
	 });
}
// ie6中fixed定位函数


var myul = document.getElementById('my_main_nav');
var myli = myul.getElementsByTagName('li');
for(var i = 0;i<myli.length;i++){
	
	if(myli[i].className !='cp'){
		myli[i].onmouseover = function(){ this.className = 'nav_hover';}
		myli[i].onmouseout  = function(){this.className ='';}
	}
}
