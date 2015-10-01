$.fn.topFixed = function(top,show){
            var top = top||0;
            var _this = $(this);
            var offsetTop = $(this).offset().top-top;
            if(!!window.ActiveXObject){
                offsetTop = $(this).offset().top-top*2;
            }
            var offsetLeft = $(this).offset().left;
            $(window).scroll(function(){
                if($(this).scrollTop()>=offsetTop){
                    _this.css({'position':'fixed','top':top+'px','left':offsetLeft});
                    if(show){
                        _this.find(show).fadeIn(200);
                    }
                }else {
                    _this.css({'position':'','top':'0px','left':'0px'})
                    if(show){
                        _this.find(show).fadeOut(200);
                    }
                }
            });
        };

        $('#y_ads').topFixed();