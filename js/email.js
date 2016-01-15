function register_by_email() {
	//add by tiger.guo 20151012
	var email = document.getElementById('email').value;
	if (email != '') {
		if(!validateEmailCode()){
			return false;
		}
	}else{
		alert('邮箱地址不能为空');
		return false;
	}
	return true;
}

function validateEmailCode() {
	var validate = false;
	var email = document.getElementById('email').value;
	var email_code = document.getElementById('email_code').value;
	if (email_code != '') {
		$.ajax({     
		    url:'user.php?act=validate_email_code',     
		    type:'post',  
		    dataType:'json', 
		    data:{
		    	'email' : email,
		    	'email_code':email_code,
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
}

var iTime_email = 119;
var Account_email;
function RemainTime_email() {
	document.getElementById('verify_mail').disabled = true;
	var iSecond, sSecond = "", sTime = "";
	if (iTime_email >= 0) {
		iSecond = parseInt(iTime_email % 120);
		if (iSecond >= 0) {
			sSecond = iSecond + "秒";
		}
		sTime = sSecond;
		if (iTime_email == 0) {
			clearTimeout(Account_email);
			sTime = '获取邮箱验证码';
			iTime_email = 119;
			document.getElementById('verify_mail').disabled = false;
		} else {
			Account = setTimeout("RemainTime_email()", 1000);
			iTime_email = iTime_email - 1;
		}
	} else {
		sTime = '没有倒计时';
	}
	document.getElementById('verify_mail').value = sTime;
}

/**
 * 发送邮箱验证码
 */
function sendEmail(){
	var email = document.getElementById('email').value;
	var captcha = document.getElementById('captcha_email').value;
	$.ajax({     
	    url:'user.php?act=send_verify_email',     
	    type:'post',  
	    dataType:'json', 
	    data:{
	    	'email':email,
	    	'captcha':captcha
	    	},     
	    async :true, 
	    error:function(){ 
	    },     
	    success:function(data){ 
		    if(data.status==1){
		    	alert('验证码已经成功发送到您的邮箱');
		    	RemainTime_email();
		    }else{
		    	alert(data.message);
		    	reload_captcha($('#yzm_img_email'));
		    }
	    }  
	}); 
}


