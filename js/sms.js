function sendSms() {
	var mobile = document.getElementById('mobile_phone').value;
	var captcha = document.getElementById('captcha').value;
	Ajax.call('user.php?act=send_sms', 'mobile=' + mobile+"&captcha="+captcha, sendSmsResponse,
			'POST', 'JSON');
}

function sendSmsResponse(result) {
	if (result.status == 1) {
		RemainTime();
		alert('手机验证码已经成功发送到您的手机');
	}else if (result.status == -2) {
		//发送失败
		RemainTime();
		if (result.message) {
			alert(result.message);
		}
		reload_captcha($('#yzm_img'));
	} else {
		//验证失败
		if (result.message) {
			alert(result.message);
		} else {
			alert('手机验证码发送失败');
		}
		reload_captcha($('#yzm_img'));
	}
}


function validateMobileCode() {
	var validate = false;
	var mobile = document.getElementById('mobile_phone').value;
	var mobile_code = document.getElementById("mobile_code").value;
	if (mobile_code != '') {
		$.ajax({     
		    url:'user.php?act=check_sms',     
		    type:'post',  
		    dataType:'json', 
		    data:{
		    	'mobile' : mobile,
		    	'mobile_code':mobile_code,
		    	},     
		    async :false, 
		    error:function(){ 
		    	
		    },     
		    success:function(data){ 
			    if(data.status==1){
			    	validate = true;
			    }else{
			    	alert(data.message);
			    }
		    }  
		}); 
	}
	return validate;
	/*
	var mobile = document.getElementById('mobile_phone').value;
	if (mobile != '') {
		var mobile_code = document.getElementById("mobile_code").value;
		var result = Ajax.call('user.php?act=check_sms', 'mobile=' + mobile
				+ '&mobile_code=' + mobile_code, null, 'POST', 'JSON', false);
		if (result.status == 1) {
			return true;
		} else {
			alert(result.message);
			return false;
		}
	}*/
}

var iTime = 119;
var Account;
function RemainTime() {
	document.getElementById('zphone').disabled = true;
	var iSecond, sSecond = "", sTime = "";
	if (iTime >= 0) {
		iSecond = parseInt(iTime % 120);
		if (iSecond >= 0) {
			sSecond = iSecond + "秒";
		}
		sTime = sSecond;
		if (iTime == 0) {
			clearTimeout(Account);
			sTime = '获取手机验证码';
			iTime = 119;
			document.getElementById('zphone').disabled = false;
		} else {
			Account = setTimeout("RemainTime()", 1000);
			iTime = iTime - 1;
		}
	} else {
		sTime = '没有倒计时';
	}
	document.getElementById('zphone').value = sTime;
}
