
jQuery(document).ready(function($) {

    // 头部
    $('.weixBox').hover(function() {
        $('.weixBox .pop-con').stop().slideToggle();
    });

    $('.loginBox,.cartBox').hover(function(){
        $(this).find('.pop-con').stop().slideToggle();
    });

    // 导航
    $('.menu-ul>li').hover(function() {
        $(this).toggleClass('on');
        $(this).find('.subBox').stop().toggle();
    });

    // 选项卡 鼠标经过切换
    $(".TAB li").mousemove(function(){
        var $vv=$(this).parent(".TAB").attr("id");
        $($vv).hide();
        $(this).parent(".TAB").find("li").removeClass("on");
        var xx=$(this).parent(".TAB").find("li").index(this);
        $($vv).eq(xx).show();
        $(this).addClass("on");
    });


    
});