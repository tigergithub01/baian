
/*$(window).load(function(){

});*/

$(document).ready(function($) {
	/*// 筛选
	$('#a-screnn').click(function(e) {
		$('body').addClass('fixme').append('<div class="overlay"></div>');
		$('.m-screen').addClass('open');
		e.stopPropagation();
	});
	$('.m-screen').click(function(e) {
		e.stopPropagation();
	});

	$('body,html').on('click','.fixme',function(){
		$('.m-screen').removeClass('open');
		$('body').removeClass('fixme');
		$('.overlay').remove();
	});*/

	// 搜索
	$('.soBox').click(function(e) {
		$('.box-so').addClass('open');
		$('body').addClass('fixme')
		$('#keywords').focus();
		e.stopPropagation();
	});
	$('.box-so').click(function(e) {
		e.stopPropagation();
	});
	

	$('.a-close').click(function(e) {
		$('.box-so').removeClass('open');
		$('body').removeClass('fixme')
	});

	// 侧边栏菜单
	$('.side-menu').click(function(e){
		$('#pop-sidemunu').stop().animate({
			left: 0
		},300);
		$('#pop-sidemunu dl').hide();
		e.stopPropagation();
	});
	$('#pop-sidemunu a.v1').click(function(){
		if( $(this).next('dl').length > 0 ){
			$(this).parents('li').siblings('li').find('dl').stop().slideUp(400);
			$(this).next('dl').stop().slideToggle(400);
			return false;
		}
	});

	
	// 返回顶部
	$(".topBtn").hide();
	$(".topBtn").click(function(){
	    $('html,body').animate({scrollTop:0}, 'slow');
	});
	$(window).scroll(function(){
	    if( $(window).scrollTop()>100 ){
	    	if($("#disable_topBtn").val()=='1'){
	    		
	    	}else{
	    		$(".topBtn").stop().fadeIn();
	    	}
	        
	    }else{
	    	if($("#disable_topBtn").val()=='1'){
	    		
	    	}else{
	    		$(".topBtn").stop().fadeOut();
	    	}
	        
	    }
	});

	//分类导航
	$('.dl-guide-nav dt').click(function() {
		$(this).siblings('dt').removeClass('ok');
		$('.dl-guide-nav dd').hide();
		$(this).toggleClass('ok');
		if( $(this).hasClass('ok') ){
			$(this).next('dd').show();
		}
	});
	
	//详情页图片延迟加载
	$(".may_like_goods img").lazyload({ 
    	placeholder : "images/ico_preload.jpg",
        effect: "fadeIn",
        load:function(elements_left, settings) {
            //console.log('load');
            //console.log(elements_left);
            //console.log(this, elements_left, settings);
            //加载完成后，自动修改tab的高度
            //var bd = document.getElementById("tabBox2-bd");
			//bd.parentNode.style.height = bd.children[0].children[0].offsetHeight+40+"px";
        },
    });
	
	//输入时，自动隐藏底部固定导航栏；否则固定导航栏会挡住输入框
	$("input[type='text']").focus(function() {
		$(".ul-fmenu").hide();
	}).blur(function(){
		$(".ul-fmenu").show();
	});
	
	$("input[type='password']").focus(function() {
		$(".ul-fmenu").hide();
	}).blur(function(){
		$(".ul-fmenu").show();
	});
	
	$("textarea").focus(function() {
		$(".ul-fmenu").hide();
	}).blur(function(){
		$(".ul-fmenu").show();
	});
	
	
	
	


});

/**
 * 打开指定DIV
 * @param __element
 */
function nav_div(__element,overlay){
	/*if(overlay!=null && overlay){
		$('body').addClass('fixme').append('<div class="overlay"></div>');
	}*/
	$('body').addClass('fixme');
	if(overlay!=null && overlay){
		$('body').append('<div class="overlay"></div>');
	}
	__element.addClass('open');
	//e.stopPropagation();
}

function close_div(__element){
	__element.removeClass('open');
	$('body').removeClass('fixme');
	$('.overlay').remove();
}

/**
 * 显示通知消息
 * @param msg
 */
function showToastMessage(msg){
	$().toastmessage('showToast', {
	    text     : msg,
	    stayTime : 2000,
	    sticky   : false,
	    position : 'middle-center',
	    type     : 'notice',
	    closeText: '',
	    close    : function () {}
	});
}
