var Tday = new Array();
var daysms = 24 * 60 * 60 * 1000
var hoursms = 60 * 60 * 1000
var Secondms = 60 * 1000
var microsecond = 1000
var DifferHour = -1
var DifferMinute = -1
var DifferSecond = -1
function clock(key) {
	var time = new Date()
	var hour = time.getHours()
	var minute = time.getMinutes()
	var second = time.getSeconds()
	var timevalue = "" + ((hour > 12) ? hour - 12 : hour)
	timevalue += ((minute < 10) ? ":0" : ":") + minute
	timevalue += ((second < 10) ? ":0" : ":") + second
	timevalue += ((hour > 12) ? " PM" : " AM")
	var convertHour = DifferHour
	var convertMinute = DifferMinute
	var convertSecond = DifferSecond
	var Diffms = Tday[key].getTime() - time.getTime()
	DifferHour = Math.floor(Diffms / daysms)
	Diffms -= DifferHour * daysms
	DifferMinute = Math.floor(Diffms / hoursms)
	Diffms -= DifferMinute * hoursms
	DifferSecond = Math.floor(Diffms / Secondms)
	Diffms -= DifferSecond * Secondms
	var dSecs = Math.floor(Diffms / microsecond)

	if (convertHour != DifferHour) a = DifferHour + "<span>天</span>";
	if (convertMinute != DifferMinute) b = DifferMinute + "<span>时</span>";
	if (convertSecond != DifferSecond) c = DifferSecond + "<span>分</span>";
	d = dSecs + "<span>秒</span>";
	if (DifferHour > 0) { a = a }
	else { a = '' }
	document.getElementById("times" + key).innerHTML = a + b + c + d; //显示倒计时信息

}

var now = new Date();
Tday[5] = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
//Tday[5] = new Date();
window.setInterval(function()
{ clock(5); }, 1000);
