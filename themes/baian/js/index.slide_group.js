function slide_group (obj){
    this.slideContents = obj.slide_contents;
    this.slideBtns = obj.slide_btns;
    this.prevBtns = obj.slide_prev;
    this.nextBtns = obj.slide_next;
    this.paseTime = obj.pase_time||2500;   /*每帧间隔时间*/
    this.bufferTime = obj.buffer_time||300;  /*淡入淡出时间*/
    this.btnHoverClass = obj.btn_hover||'hover'; /*按钮hover样式*/
    this.madeBtn = obj.made_btn||0;
    this.contBtns = obj.cont_btns;

    this.currFrame = -1; /*当前帧*/
    this.sumFrame = this.slideContents.length;  /*总帧数*/
    var _this = this;

    this.skip = function(n){    /*跳到第n帧*/
        if(n==this.currFrame){return 0;}
        this.delBtnsClick();
        this.delPrevClick();
        this.delNextClick();
        this.slideContents.fadeOut(this.bufferTime,'linear');
        $(this.slideContents[n]).fadeIn(this.bufferTime,'linear',function(){
            _this.addPrevClick();
            _this.addNextClick();
            _this.addBtnsClick(n);
        });
        this.currFrame = n;
        if(this.slideBtns){
            this.slideBtns.removeClass(this.btnHoverClass);
            $(this.slideBtns[n]).addClass(this.btnHoverClass);
        }
        if(this.contBtns){
            this.contBtns.removeClass(this.btnHoverClass);
            $(this.contBtns[n]).addClass(this.btnHoverClass);
        }
    };

    this.next = function(){ /*跳到下一帧*/
        var nextFrame = this.currFrame<this.sumFrame-1?this.currFrame+1:0;
        this.skip (nextFrame);
    };

    this.prev = function(){ /*跳到上一帧*/
        var prevFrame = this.currFrame>0?this.currFrame-1:this.sumFrame-1;
        this.skip (prevFrame);
    };

    this.stop = function(){
        clearInterval(this.interval);
    };

    this.start = function(){
        this.interval = setInterval(function(){
            _this.next();
        },this.paseTime+this.bufferTime);
    };

    this.addBtnsClick = function(n){
        if(this.slideBtns){
            var _this = this;
            this.slideBtns.bind('click',function(e){
                e.stopPropagation();
                var index = $(this).index();
                if(n!=index){   /*点击按钮不是在当前帧的时候才执行*/
                    _this.skip(index);
                }
            });
        }
    };

    this.delBtnsClick = function(){
        if(this.slideBtns){
            this.slideBtns.unbind('click');
        }
    };

    this.addPrevClick = function(){
        if(this.prevBtns){
            this.prevBtns.bind('click',function(e){
                e.stopPropagation();
                _this.prev();
            });
        }
    };

    this.delPrevClick = function(){
        if(this.prevBtns){
            this.prevBtns.unbind('click');
        }
    };

    this.addNextClick = function(){
        if(this.nextBtns){
            this.nextBtns.bind('click',function(e){
                e.stopPropagation();
                _this.next();
            });
        }
    };

    this.delNextClick = function(){
        if(this.nextBtns){
            this.nextBtns.unbind('click');
        }
    };

    this.btnsHover = function(){
        if(this.slideBtns){
            this.slideBtns.hover(function(){
                _this.stop();
            },function(){
                _this.start();
            });
        }
        if(this.prevBtns){
            this.prevBtns.hover(function(){
                _this.stop();_this.prevBtns.addClass(_this.btnHoverClass);
            },function(){
                _this.start(); _this.prevBtns.removeClass(_this.btnHoverClass);
            });
        }
        if(this.nextBtns){
            this.nextBtns.hover(function(){
                _this.stop();_this.nextBtns.addClass(_this.btnHoverClass);
            },function(){
                _this.start(); _this.nextBtns.removeClass(_this.btnHoverClass);
            });
        }
    };

    this.contBtnsHover = function(){
        if(this.contBtns){
            this.contBtns.hover(function(){
                var n = $(this).index();
                _this.stop();
                _this.contBtnTimeout = setTimeout(function(){
                   _this.skip(n);
                },300);
            },function(){
                clearTimeout(_this.contBtnTimeout);
                _this.start();
            });
        }
    }

    this.init = function(){
        if(this.sumFrame<=1){this.skip(0);return false;}
        if(this.madeBtn){  /*自动生成按钮*/
            var str = '';
            for(var i=0;i<this.sumFrame;i++){str+='<li></li>';}
            //alert(str);
            this.slideBtns = $(str).appendTo(this.slideBtns);
        }
        this.slideContents.hover(function(){_this.stop();},function(){_this.start();});
        this.contBtnsHover();
        this.btnsHover();
        this.skip(0);
        this.start();
    };
}


var slide_group = new slide_group({
	'slide_contents':$('#slide_group_contents li'),
	'slide_btns':$('#slide_group_btns'),
	//'cont_btns':$('#slide_group .text_cont a'),
	'btn_hover':'hover',
	'made_btn':1
});
slide_group.init();