/*倒计时*/
window.onload = function () { clock() }
//var starttime = document.getElementById("hidStarttime").value;
//var Tday = new Date(starttime)
var endtime = document.getElementById("hidEndtime").value;
var Tday = new Date(endtime)

var daysms = 24 * 60 * 60 * 1000;
var hoursms = 60 * 60 * 1000;
var Secondms = 60 * 1000;
var microsecond = 1000 ;
function clock()
{
//    var endtime = document.getElementById("hidEndtime").value;
    //    var time = new Date(endtime)
    var time = new Date()
	var Diffms = Tday.getTime() - time.getTime()
	DifferenceHour = Math.floor(Diffms / daysms)
	Diffms -= DifferenceHour * daysms
	DifferenceMinute = Math.floor(Diffms / hoursms)
	Diffms -= DifferenceMinute * hoursms
	DifferenceSecond = Math.floor(Diffms / Secondms)
	Diffms -= DifferenceSecond * Secondms
	var dSecs = Math.floor(Diffms / microsecond)
	var time1 = new Date()
	var hm=(time1.getMilliseconds()%100);
	
	//document.getElementById("dd").innerHTML=((DifferenceHour<10) ? "0" : "")+DifferenceHour
	document.getElementById("hh").innerHTML=((DifferenceMinute<10) ? "0" : "")+DifferenceMinute
	document.getElementById("mm").innerHTML=((DifferenceSecond<10) ? "0" : "")+DifferenceSecond
	document.getElementById("ss").innerHTML=((dSecs<10) ? "0" : "")+dSecs
	setTimeout("clock()",100)
}


function slider(content, ul, lia, promise, numbers) {
    /*新品热卖*/
    var contentLeft = $(content).offset().left;
    var liw = $(lia).outerWidth(true);
    var len = $(lia).length;
    var leftwidth = -liw * len + numbers;
    $(ul).width(liw * len);
    var $gotouch = $(ul);
    var gogo = document.getElementById($gotouch[0].id);
    var left = -1;
    var right = 1;
    var direction = left, startLeft = 0, startX = 0, overX = 0; startY = 0, overY = 0, line = 0, count = 0, metion, speed = 0, time = 0;
    function timeadd() { time++ }
    gogo.ontouchstart = function (event) {
        clearInterval(metion);
        startLeft = $gotouch.offset().left;
        touch = event.touches[0];
        startX = touch.clientX;
        startY = touch.clientY;
        overX = 0;
    };
    gogo.ontouchmove = function (event) {
        moveX = startLeft - contentLeft;
        overX = event.changedTouches[0].clientX;
        overY = event.changedTouches[0].clientY;
        if (Math.abs(overY - startY) > Math.abs(overX - startX)) {
            return;
        };
        event.preventDefault();
        endX = overX - startX;
        if (endX > 0) {
            direction = right;
        }
        else if (endX < 0) {
            direction = left;
        };
        $gotouch.css("left", moveX + endX + 10);
    };
    if (promise != '') {
        for (var linun = 0; linun < len; linun++) {
            $(promise).find("ul").append("<li></li>");
        };
        $(promise).find("ul li:eq(0)").addClass("cur").width(55);
        gogo.ontouchend = function (event) {
            if (overX == 0) { direction = 0 }
            else {
                autoslider('fast');
                direction = left;
                metion = setInterval(function () { return autoslider(500) }, 5000);
            }
        };
        var metion = setInterval(function () { return autoslider(500) }, 5000);
    }
    else {
        gogo.ontouchend = function (event) {
            if (overX == 0) { direction = 0; };
            if (direction == left) {
                if (parseInt($gotouch.css("left")) <= leftwidth) {
                    $gotouch.animate({ left: leftwidth + 'px' }, "slow");
                }
            }
            else if (direction == right) {
                if (parseInt($gotouch.css("left")) >= 0) {
                    $gotouch.animate({ left: "0" }, "slow");
                }
            };
        };
    };
    function autoslider(speed) {
        if (direction == left) {
            count++;
            if (count >= len) { count = 0; $gotouch.animate({ left: '0px' }, 100) }
            else { $gotouch.animate({ left: liw * count * -1 }, speed) }
        }
        else if (direction == right) {
            count--;
            if (count <= -1) { count = len - 1; $gotouch.animate({ left: -liw * (len - 1) }, 100) }
            else { $gotouch.animate({ left: liw * count * -1 }, speed) }
        }
        $(promise).find("ul li").removeClass("cur");
        $(promise).find("ul li").eq(count).addClass("cur");
    };
};

$(function () {
    /*首页焦点图*/
    var roll1 = new slider(".content", ".banner .bar_list ul", ".banner .bar_list ul li", ".bar_btn", 0);
    /*新品热卖*/
    var roll2 = new slider(".content", ".hotShop .hotImg ul li", ".hotShop .hotImg ul li a", '', 318);
});