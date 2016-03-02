
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
	        $(".topBtn").stop().fadeIn();
	    }else{
	        $(".topBtn").stop().fadeOut();
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
	
	
	
	


});

/**
 * 打开指定DIV
 * @param __element
 */
function nav_div(__element,overlay){
	if(overlay!=null && overlay){
		$('body').addClass('fixme').append('<div class="overlay"></div>');
	}
	__element.addClass('open');
	//e.stopPropagation();
}

function close_div(__element){
	__element.removeClass('open');
	$('.overlay').remove();
}