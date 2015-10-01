
jQuery(document).ready(function($) {

    // 导航
    $('.menu-ul>li').hover(function() {
        $(this).toggleClass('on');
        $(this).find('.subBox').stop().toggle();
    });

    $('a.goTop').click(function(){ 
       $('html, body').animate({scrollTop:0}, 'slow'); 
    });
    
    $('a.s-f').click(function(){
        $('html, body').animate({
            scrollTop: $( $.attr(this, 'href') ).offset().top
        }, 500);
        return false;
    });

    $('#login-btn').click(function(){
        $('#login-pop').fadeIn();
    });
    $('#login-pop .close').click(function(){
        $('#login-pop').fadeOut();
    });

    $('#user-btn').click(function(){
        $('#user-info').fadeIn();
    });
    $('#user-info .close').click(function(){
        $('#user-info').fadeOut();
    });

    $('#float li.mb0').hover(function(){
        $(this).find('a').stop().animate({left:0} ,100);
        $(this).addClass('on')
    },function(){
        $(this).find('a').stop().animate({left: -20} ,100);
        $(this).removeClass('on')
    });

    $('#side-menu').hover(function(){
        $(this).find(".menu-ul-dn").slideDown();
    },function(){
        $(this).find(".menu-ul-dn").slideUp();
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

    // 等级条
    $('.degreeBox').each(function(){
        var _score = $(this).attr('score');
        var _total = $(this).attr('total');
        var _val = _score / _total *100;
        console.log(_val);
        $(this).append('<b></b><span>'+_score+'/'+_total+'</span>');
        $(this).find('b').css('width', _val+'%');
    });

    // 侧边栏二级菜单
    $('.s-nav-list .on').next('dl').stop().slideDown();
    $('.s-nav-list h3').click(function(event) {
        $(this).toggleClass('on');
        $('.s-nav-list h3').not($(this)).removeClass('on');
        $('.s-nav-list dl').stop().slideUp();
        if ($(this).hasClass('on')) {
            $(this).next('dl').stop().slideDown();
        }else{
            $(this).next('dl').stop().slideUp();
        };
    });

    $('.goTop').hide();  
    $(window).scroll(function() {     
        if($(window).scrollTop() >= 100){
            $('.goTop').show(); 
        }else{    
            $('.goTop').hide();    
        }  
    });

    function kefu(){
        var winW = $(window).width();
        if(winW < 1300){
            $('#float').css({
                "left":"auto",
                "right":"-2px",
                "margin-left":"0"
            });
        }else{
            $('#float').css({
                "right":"auto",
                "left":"50%",
                "margin-left":"605px"
            });
        }
    }
    kefu();
    $(window).resize(function(){
        kefu();
    })

});