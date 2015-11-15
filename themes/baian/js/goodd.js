 jQuery(document).ready(function($) {
            
           $("#mycarousel").jcarousel({
            	initCallback:mycarousel_initCallback
           	});
           	$(".jqzoom").jqueryzoom({
                xzoom:400,
                yzoom:400,
                offset:10,
                position:"right",
                preload:1,
                lens:1
            });

            function mycarousel_initCallback(carousel){
            $("#mycarousel li").mouseover(function(){
                var JQ_img = $("img", this);
                var image_url = JQ_img.attr("src");
                $("#_middleImage").attr("src", image_url).attr('longdesc',image_url);
                $('#mycarousel li img').removeClass("cur_on");
                JQ_img.addClass("cur_on");
            })
            }; 

            /*$('.ul-sel li').click(function(){
               $(this).siblings('li').removeClass('on');
               $(this).addClass('on');
            });*/

            jQuery(".zuBox").slide({mainCell:".bd",trigger:"click"});
            jQuery(".phBox").slide({mainCell:".bd",trigger:"click"});
            jQuery(".mainBox").slide({mainCell:".bd",trigger:"click"});
            jQuery("#huanBox").slide({mainCell:".bd ul",autoPlay:false,effect:"left",vis:5,scroll:5,autoPage:true,trigger:"click"});
            jQuery("#rushScroll").slide({mainCell:".bd ul",autoPlay:true,effect:"left",vis:5,scroll:5,autoPage:true,trigger:"click"});

            // 图片点击放大镜
            $('.ul-ping li').each(function(){
				$(this).find('.con').append('<div class="s-photo-viewer"><img src="" alt=""></div>');
            });
            $('.ul-ping .img img').click(function(){
            	var _url = $(this).attr('src');
            	$(this).siblings('img').removeClass('ok');
            	$(this).toggleClass('ok');
            	if( $(this).hasClass('ok') ){
	            	$(this).parents('.con').find('.s-photo-viewer img').attr('src',_url);
            	}else{
	            	$(this).parents('.con').find('.s-photo-viewer img').attr('src','');
            	}
            });
            $('.s-photo-viewer img').click(function(){
            	$(this).attr('src','');
            	$(this).parents('.con').find('.img img').removeClass('ok');
            });

        });