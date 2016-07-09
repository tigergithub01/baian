/* $Id : shopping_flow.js 4865 2007-01-31 14:04:10Z paulgao $ */



var selectedShipping = null;

var selectedPayment  = null;

var selectedPack     = null;

var selectedCard     = null;

var selectedSurplus  = '';

var selectedBonus    = 0;

var selectedIntegral = 0;

var selectedOOS      = null;

var alertedSurplus   = false;



var groupBuyShipping = null;

var groupBuyPayment  = null;



/* 添加 by pgge start */

var defaultAddress = null;



/* 

 * 选用其它收货方式信息

 */

function changeAddress(cid)

{

  var consigneeTable = document.getElementById('consigneeTable');

  var sendConginee = document.getElementById('sendConginee')

  var defaultAddress = document.getElementById('defaultAddress');

  document.getElementById('editConsigneeTable').innerHTML = "";

  

  if(cid == '0'){

	 if(defaultAddress){defaultAddress.value = 0; }

	 consigneeTable.style.display = 'block';

	 sendConginee.style.display = 'none';

  }else{

	 if(defaultAddress){defaultAddress.value = cid; }

	 consigneeTable.style.display = 'none'; 

	 sendConginee.style.display = 'block';

  }

 

}



/* *

 * 设置默认收货人信息

 */

function setDefaultAddress(obj)

{

  if (defaultAddress == obj)

  {

    return;

  }

  else

  {

    defaultAddress = obj;

  }

  

  var now = new Date();

  Ajax.call('/user.php?act=defaultAddress', 'address_id=' + defaultAddress, 'GET', 'JSON');

}





function showPlusDiv(id){

	var did = $("#"+id);

	var spandid = $("#span"+id);

	if(did.css("display") == "none"){

		did.css("display","block");

		spandid.html("-")

	}else{

		did.css("display","none");

		spandid.html("+");

	}

}



function closePlusDiv(id){

	showPlusDiv(id);

}



function bindBlur2(obj){

	var fid = $("#"+obj.id);

    if(fid.val() == fid.attr("placeholder")){

    	fid.val('');

    	fid.removeClass("placeholder");

    }

	if(fid.val()!=''){

    	if(fid.hasClass('number')){

    		if(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(fid.val())){

    			if(fid.parent().children().length == 2){

    				fid.siblings().remove();

    			}

    		}else{

    			if(fid.parent().children().length == 1){

    				var html = '<label for="'+fid.attr("name")+'" generated="true" class="error">'+fid.attr("title")+'</label>';

    			   $(html).insertAfter(fid);

    			   if($("#mobile").parent().children().length == 2){

    					 $("#mobile").siblings().remove();

    	  			 }

    			}

    		}

    	}else{

			if(fid.parent().children().length == 2){

				fid.siblings().remove();

			}

    	}

    }	

}





function checknumber(fid){

	var error = '';

	if(fid.hasClass('number')){

		if(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(fid.val()) && fid.val().length >= fid.attr("minlength") && fid.val().length <= fid.attr("maxlength")){

			if(fid.parent().children().length == 2){

				fid.siblings().remove();

			}

		}else{

			error = '1';

			if(fid.parent().children().length == 1){

			  var tips = "";

			  if(fid.attr("name") == "mobile"){

				  tips = "手机号码为11位数字";

			  }else if(fid.attr("name") == "zipcode"){

				  tips = "邮政编码为6位数字";

			  }

			   var html = '<label for="'+fid.attr("name")+'" generated="true" class="error">'+tips+'</label>';

			   $(html).insertAfter(fid);

			}

		}

	}else{

		if(fid.parent().children().length == 2){

			fid.siblings().remove();

		}

	}

	return error;

}



function bindBlur(obj){

	var fid = $("#"+obj.id);

    if(fid.val() == fid.attr("placeholder")){

    	fid.val('');

    	fid.removeClass("placeholder");

    }

	if(fid.val()==''){

		if(fid.parent().children().length == 1){

			var html = '<label for="'+fid.attr("name")+'" generated="true" class="error">'+fid.attr("title")+'</label>';

		   $(html).insertAfter(fid);

		}

    }else{

    	checknumber(fid);

    }	

}



function changSelectCal(id){

	if($("#"+id).val() != 0){

		if($("#selDistricts").parent().children().length == 4){

			$("#selDistricts").parent().find("label").remove();

		} 

	}

}



function submitHandler(){

	  $("input[placeholder]").each(function(){

        if($(this).val() == $(this).attr("placeholder")){

          $(this).val("");

          $(this).removeClass("placeholder");

        }

      });

	  

	  var msg = '';

	  if($("#address_id").val() != '' || $("#ff").val() == 1){

		  $(".cmxform2 input[type='text']").each(function(){

			  if($(this).hasClass("required")){

				bindBlur(this);

				if($(this).val() == '')

				   msg += '1';

			  }

		  });

	  }else{

		  $(".cmxform input[type='text']").each(function(){

			  if($(this).hasClass("required")){

				bindBlur(this);

				if($(this).val() == '')

				   msg += '1';

			  }

		  });

	  }

	 

	  if($("#mobile").val() == '' && $("#tel").val() == ''){

		  msg += '1';

		  bindBlur(document.getElementsByName("mobile")[0]);

	  }else{

		  if($("#tel").val() != ''){

			 if($("#mobile").parent().children().length == 2){

				 $("#mobile").siblings().remove();

  			 }

			 msg += checknumber($("#tel"));

		  }

		  

		  if($("#mobile").val() != ''){

			 if($("#tel").parent().children().length == 2){

				 $("#tel").siblings().remove();

  			 }

			 msg += checknumber($("#mobile"));

		  }

	  }

	  

	  if($("#selProvinces").val() == 0){

		  msg += '1';

		  if($("#selDistricts").parent().children().length == 3){

			 var html = '<label for="'+$(this).attr("name")+'" generated="true" class="error">请选择省份/直辖市</label>';

			 $(html).insertAfter("#selDistricts");

		  }

	  }else{

		  if($("#selDistricts").parent().children().length == 4){

			  $("#selDistricts").parent().find("label").remove();

		  } 

	  }

	  

	  if($("#selCities").val() == 0){

		  msg += '1';

		  if($("#selDistricts").parent().children().length == 3){

			 var html = '<label for="'+$(this).attr("name")+'" generated="true" class="error">请选择市</label>';

			 $(html).insertAfter("#selDistricts");

		  }

	  }else{

		  if($("#selDistricts").parent().children().length == 4){

			  $("#selDistricts").parent().find("label").remove();

		  } 

	  }

	  

	  if($("#selDistricts").val() == 0){

		  msg += '1';

		  if($("#selDistricts").parent().children().length == 3){

			 var html = '<label for="'+$(this).attr("name")+'" generated="true" class="error">请选择县/区</label>';

			 $(html).insertAfter("#selDistricts");

		  }

	  }else{

		  if($("#selDistricts").parent().children().length == 4){

			  $("#selDistricts").parent().find("label").remove();

		  } 

	  }


	  if($("#selTowns").val() == 0){

		  msg += '1';

		  if($("#selTowns").parent().children().length == 3){

			 var html = '<label for="'+$(this).attr("name")+'" generated="true" class="error">请选择街道</label>';

			 $(html).insertAfter("#selTowns");

		  }

	  }else{

		  if($("#selTowns").parent().children().length == 4){

			  $("#selTowns").parent().find("label").remove();

		  } 

	  }

	  

	  

	  if(msg==''){

		  var address_id = $("#address_id") == undefined ? 0 : $("#address_id").val();

	      var consignee = $("#consignee").val();

	      //var selCountries = $("#selCountries").val();

	      var selProvinces = $("#selProvinces").val();

	      var selCities = $("#selCities").val();

	      var selDistricts = $("#selDistricts").val();
		  
		  var selTowns = $("#selTowns").val();

	      var address = $("#address").val();

	      var zipcode = $("#zipcode").val();

	      var mobile = $("#mobile").val();

	      var tel = $("#tel").val();

	      var best_time = $("#best_time").val();

	      var isSaveDefault = $("input[type='checkbox']:checked").val();

	      var param = "address_id="+address_id+"&consignee="+consignee+"&province="+selProvinces+"&city="+selCities+"&district="+selDistricts+"&town="+selTowns+

	                  "&address="+address+"&zipcode="+zipcode+"&mobile="+mobile+"&tel="+tel+"&best_time="+best_time;

	      if(isSaveDefault == 1) param += "&isSaveDefault="+isSaveDefault;

	     $.post("/flow.php?step=consignee",param,function(json){

	      if(json != null){

	        if(json.error == "1"){

	          document.getElementById("addressDiv").innerHTML = json.content;

	          var html = '<a href="javascript:editAddress()" style="color:#004ea2;">[修改]</a> ';

	          $("#addressFont").html(html);

	          

		      //修正运费

	          conrectShipping();

	          //return ;

	          location.href = '/flow.php?step=checkout';

	        }else{

	         //$(form).find(":submit").attr("disabled", true).attr("value", "正在提交...");

	         //form.submit();

	        }

	      }

	     },"json");

	  }

}



function showPostInput(){

	var did = $("#postspan");

	var pdid = $("#posttips");

	if(did.css("display") == "none"){

		did.css("display","block");

		pdid.css("display","block");

	}else{

		did.css("display","none");

		pdid.css("display","none");

	}

}



function closeAddress(){

	$.post("/flow.php","step=getConsignee",function(json){

        if(json != null){

          if(json.error == "1"){

            $("#addressDiv").html(json.content);

            var html = '<a href="javascript:editAddress()" style="color:#004ea2;">[修改]</a> ';

            $("#addressFont").html(html);

          }else{

           //$(form).find(":submit").attr("disabled", true).attr("value", "正在提交...");

           //form.submit();

          }

        }

    },"json");

}



function editAddress(){

	$.post("/flow.php","step=getAllConsignee",function(json){

        if(json != null){

          if(json.error == "1"){

            $("#addressDiv").html(json.content);

            var html = '<a href="javascript:closeAddress()" style="color:#004ea2;">[请保存并关闭]</a> ';

            $("#addressFont").html(html);

          }else{

           //$(form).find(":submit").attr("disabled", true).attr("value", "正在提交...");

           //form.submit();

          }

        }

    },"json");

}



function editConginee(addressId){

	$('#sendConginee').hide();//alert($("#conaddress_"+addressId).attr("checked"));



	$.post("/flow.php?step=getConsignee","addressId="+addressId+"&flag=1",function(json){

        if(json != null){

          if(json.error == "1"){

        	$("#consigneeTable").hide();

            $("#editConsigneeTable").html(json.content).show();

          }else{

           //$(form).find(":submit").attr("disabled", true).attr("value", "正在提交...");

           //form.submit();

          }

        }

    },"json");

	$("#conaddress_"+addressId).attr("checked","checked");

	//$("#conaddress_"+addressId).attr("checked",true);	

}



function confirmConginee(){

	var addressId = 0 ;

	$("#consigneeList input[type='radio']").each(function(){

	   if($(this).attr("checked")){

		   addressId = $(this).attr("value");

	   }	

	});

	

	$.post("/flow.php?step=getConsignee","addressId="+addressId,function(json){

        if(json != null){

          if(json.error == "1"){

            $("#addressDiv").html(json.content);

            var html = '<a href="javascript:editAddress()" style="color:#004ea2;">[修改]</a> ';

	        $("#addressFont").html(html);

	        

	        //修正运费

	        conrectShipping();

	        location.href = '/flow.php?step=checkout';

          }else{

           //$(form).find(":submit").attr("disabled", true).attr("value", "正在提交...");

           //form.submit();

          }

        }

       },"json");

}





function conrectShipping(){

	//修正运费

	var objs = document.getElementsByName("shipping");

	var obj = null;

	for(var i=0;i<objs.length;i++){

		if(objs[i].checked){

			obj = objs[i];

		}

	}

	selectShipping(obj,"");

}



function comfirmDropConginee(addressId){

     if(confirm("您要删除该地址吗？")){

    	 $.post("/flow.php?step=drop_consignee","id="+addressId,function(json){

    	        if(json != null){

    	          if(json.error == "1"){

    	        	  editAddress();

    	          }else{

    	        	alert(json.content);

    	          }

    	        }

    	  },"json"); 

     }

}



function shippingReview(sid,sname){

	document.getElementById("spReviewName").innerHTML = sname;

	var increview = document.getElementById("increview");

	if(sname == "申通快递"){

		increview.href = "http://www.sto.cn/sites.asp";

	}else if(sname == "圆通速递"){

		increview.href = "http://www.yto.net.cn/cn/service/network1.aspx";

	}else if(sname == "顺丰速运"){

		increview.href = "http://www.sf-express.com/cn/sc/";

	}

	var shippingDiv = document.getElementById("shippingDiv");

	var relobj = document.getElementById("shipping_"+sid);

    var relleft = getAbsoluteLeft(relobj);

    var reltop  = getAbsoluteTop(relobj);

    var layerArrow = document.getElementById("layerArrow");

    layerArrow.style.left    = "327px";

    layerArrow.style.top     = "-2px";

    shippingDiv.style.left    = (relleft - 300) + "px";

    shippingDiv.style.top     = (reltop + 21) + "px";

    shippingDiv.style.display = "";

}



function closeDiv(){

	document.getElementById("shippingDiv").style.display = "none";

}



function getAbsoluteLeft(obj)

{

    var s_el = 0;

    var el   = obj;

    while(el)

    {

        s_el = s_el + el.offsetLeft;

        el   = el.offsetParent;

    }

    return s_el;

}



function getAbsoluteTop(obj)

{

    var s_el = 0;

    var el   = obj;

    while(el)

    {

        s_el = s_el + el.offsetTop;

        el   = el.offsetParent;

    }

    return s_el;

}

/* 添加 by pgge end */



/* *

 * 改变配送方式

 */

function selectShipping(obj)

{

  if (selectedShipping == obj)

  {

    return;

  }

  else

  {

    selectedShipping = obj;

  }
  
  



  var supportCod = obj.attributes['supportCod'].value + 0;

  var theForm = obj.form;



  for (i = 0; i < theForm.elements.length; i ++ )

  {

    if (theForm.elements[i].name == 'payment' && theForm.elements[i].attributes['isCod'].value == '1')

    {

      if (supportCod == 0)

      {

        theForm.elements[i].checked = false;

        theForm.elements[i].disabled = true;

      }

      else

      {

        theForm.elements[i].disabled = false;

      }

    }

  }



  if (obj.attributes['insure'].value + 0 == 0)

  {

    document.getElementById('ECS_NEEDINSURE').checked = false;

    document.getElementById('ECS_NEEDINSURE').disabled = true;

  }

  else

  {

    document.getElementById('ECS_NEEDINSURE').checked = false;

    document.getElementById('ECS_NEEDINSURE').disabled = false;

  }

  /*手机版单选框样式适应*/
  if($(obj).is(':checked')) { 
	$("input[type='radio'][name='"+$(obj).prop('name')+"']").parent().removeClass("checked");  
	$(obj).parent().addClass("checked");
  }else{
	$("input[type='radio'][name='"+$(obj).prop('name')+"']").parent().removeClass("checked");    
	$(obj).parent().removeClass("checked");
  }
  
  //付款方式为门店自提的时候，显示自提点选择
  if($(obj).attr('shipping_code')=='cac'){
	  $(".CAC_POINT_LIST").show();
  }else{
	  $(".CAC_POINT_LIST").hide();
  }

  var now = new Date();  

  Ajax.call('flow.php?step=select_shipping', 'shipping=' + obj.value, orderShippingSelectedResponse, 'GET', 'JSON');

}



/**

 *

 */

function orderShippingSelectedResponse(result)

{

  if (result.need_insure)

  {

    try

    {

      document.getElementById('ECS_NEEDINSURE').checked = true;

    }

    catch (ex)

    {

      alert(ex.message);

    }

  }



  try

  {

    if (document.getElementById('ECS_CODFEE') != undefined)

    {

      document.getElementById('ECS_CODFEE').innerHTML = result.cod_fee;

    }

  }

  catch (ex)

  {

    alert(ex.message);

  }


  result.operate = 'select_shipping';
  orderSelectedResponse(result);

}



/* *

 * 改变支付方式

 */

function selectPayment(obj)

{

  if (selectedPayment == obj)

  {

    return;

  }

  else

  {

    selectedPayment = obj;

  }
  
  /*手机版单选框样式适应*/
  if($(obj).is(':checked')) { 
	$("input[type='radio'][name='"+$(obj).prop('name')+"']").parent().removeClass("checked");  
	$(obj).parent().addClass("checked");
  }else{
	  $("input[type='radio'][name='"+$(obj).prop('name')+"']").parent().removeClass("checked");    
	$(obj).parent().removeClass("checked");
  }



  Ajax.call('flow.php?step=select_payment', 'payment=' + obj.value, orderSelectedResponse, 'GET', 'JSON');

}

/* *

 * 团购购物流程 --> 改变配送方式

 */

function handleGroupBuyShipping(obj)

{

  if (groupBuyShipping == obj)

  {

    return;

  }

  else

  {

    groupBuyShipping = obj;

  }



  var supportCod = obj.attributes['supportCod'].value + 0;

  var theForm = obj.form;



  for (i = 0; i < theForm.elements.length; i ++ )

  {

    if (theForm.elements[i].name == 'payment' && theForm.elements[i].attributes['isCod'].value == '1')

    {

      if (supportCod == 0)

      {

        theForm.elements[i].checked = false;

        theForm.elements[i].disabled = true;

      }

      else

      {

        theForm.elements[i].disabled = false;

      }

    }

  }



  if (obj.attributes['insure'].value + 0 == 0)

  {

    document.getElementById('ECS_NEEDINSURE').checked = false;

    document.getElementById('ECS_NEEDINSURE').disabled = true;

  }

  else

  {

    document.getElementById('ECS_NEEDINSURE').checked = false;

    document.getElementById('ECS_NEEDINSURE').disabled = false;

  }



  Ajax.call('group_buy.php?act=select_shipping', 'shipping=' + obj.value, orderSelectedResponse, 'GET');

}



/* *

 * 团购购物流程 --> 改变支付方式

 */

function handleGroupBuyPayment(obj)

{

  if (groupBuyPayment == obj)

  {

    return;

  }

  else

  {

    groupBuyPayment = obj;

  }



  Ajax.call('group_buy.php?act=select_payment', 'payment=' + obj.value, orderSelectedResponse, 'GET');

}



/* *

 * 改变商品包装

 */

function selectPack(obj)

{

  if (selectedPack == obj)

  {

    return;

  }

  else

  {

    selectedPack = obj;

  }



  Ajax.call('flow.php?step=select_pack', 'pack=' + obj.value, orderSelectedResponse, 'GET', 'JSON');

}



/* *

 * 改变祝福贺卡

 */

function selectCard(obj)

{

  if (selectedCard == obj)

  {

    return;

  }

  else

  {

    selectedCard = obj;

  }



  Ajax.call('flow.php?step=select_card', 'card=' + obj.value, orderSelectedResponse, 'GET', 'JSON');

}



/* *

 * 选定了配送保价

 */

function selectInsure(needInsure)

{

  needInsure = needInsure ? 1 : 0;



  Ajax.call('flow.php?step=select_insure', 'insure=' + needInsure, orderSelectedResponse, 'GET', 'JSON');

}



/* *

 * 团购购物流程 --> 选定了配送保价

 */

function handleGroupBuyInsure(needInsure)

{

  needInsure = needInsure ? 1 : 0;



  Ajax.call('group_buy.php?act=select_insure', 'insure=' + needInsure, orderSelectedResponse, 'GET', 'JSON');

}



/* *

 * 回调函数

 */

function orderSelectedResponse(result)

{

	//alert(result);return ;

  if (result.error)

  {

    alert(result.error);

    //location.href = './';

  }



  try

  {

    var layer = document.getElementById("ECS_ORDERTOTAL");



    layer.innerHTML = (typeof result == "object") ? result.content : result;



    if (result.payment != undefined)

    {

      var surplusObj = document.forms['theForm'].elements['surplus'];

      if (surplusObj != undefined)

      {

        surplusObj.disabled = result.pay_code == 'balance';

      }

    }

  }

  catch (ex) { }
  
  if(result.operate!=null && result.operate=='select_shipping'){
	//选择配送方式时如果应付款金额为零时，自动选择支付方式，电脑版选择“支付宝”，微信选择“微信支付”  
	  try{
		  var total_fee_amt = parseFloat($("#total_fee_amt").val());
		  //console.debug(result.operate);
		  //console.debug(total_fee_amt);
		  var checked_payments = $("#ECS_SHIPPING_PARMENT").find("input[type='radio'][name='payment']:checked"); //是否已经选择了支付方式
		  if(total_fee_amt==0 && (checked_payments.length==0)){
			var default_pay_type = 'alipay';
			var ua = window.navigator.userAgent.toLowerCase();
		    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
		    	//微信
		    	default_pay_type = 'wxpay';
		  }
		  var $pay_type = $("#ECS_SHIPPING_PARMENT").find("input[pay_code='"+default_pay_type+"']");
		  //console.debug($pay_type.val());
		  $pay_type.click();
		  $pay_type.parent().addClass("checked");//针对手机版起作用
		  }	  
	  }catch (ex) { 
		  
	  }
  }
  

}



/* *

 * 改变余额

 */

function changeSurplus(val)

{

  if (selectedSurplus == val)

  {

    return;

  }

  else

  {

    selectedSurplus = val;

  }



  Ajax.call('flow.php?step=change_surplus', 'surplus=' + val, changeSurplusResponse, 'GET', 'JSON');

}



/* *

 * 改变余额回调函数

 */

function changeSurplusResponse(obj)

{

  if (obj.error)

  {

    try

    {

      document.getElementById("ECS_SURPLUS_NOTICE").innerHTML = obj.error;

      document.getElementById('ECS_SURPLUS').value = '0';

      document.getElementById('ECS_SURPLUS').focus();

    }

    catch (ex) { }

  }

  else

  {

    try

    {

      document.getElementById("ECS_SURPLUS_NOTICE").innerHTML = '';

    }

    catch (ex) { }

    orderSelectedResponse(obj.content);

  }

}



/* *

 * 改变积分

 */

function changeIntegral(val)

{

  if (selectedIntegral == val)

  {

    return;

  }

  else

  {

    selectedIntegral = val;

  }



  Ajax.call('flow.php?step=change_integral', 'points=' + val, changeIntegralResponse, 'GET', 'JSON');

}



/* *

 * 改变积分回调函数

 */

function changeIntegralResponse(obj)

{

  if (obj.error)

  {

    try

    {

      document.getElementById('ECS_INTEGRAL_NOTICE').innerHTML = obj.error;

      document.getElementById('ECS_INTEGRAL').value = '0';

      document.getElementById('ECS_INTEGRAL').focus();

    }

    catch (ex) { }

  }

  else

  {

    try

    {

      document.getElementById('ECS_INTEGRAL_NOTICE').innerHTML = '';

    }

    catch (ex) { }

    orderSelectedResponse(obj.content);

  }

}



/* *

 * 改变红包

 */

function changeBonus(val)

{

  if (selectedBonus == val)

  {

    return;

  }

  else

  {

    selectedBonus = val;

  }



  Ajax.call('flow.php?step=change_bonus', 'bonus=' + val, changeBonusResponse, 'GET', 'JSON');

}



/* *

 * 改变红包的回调函数

 */

function changeBonusResponse(obj)

{

  if (obj.error)

  {

    alert(obj.error);



    try

    {

      document.getElementById('ECS_BONUS').value = '0';

    }

    catch (ex) { }

  }

  else

  {

    orderSelectedResponse(obj.content);

  }

}



/**

 * 验证红包序列号

 * @param string bonusSn 红包序列号

 */

function validateBonus(bonusSn)

{

  Ajax.call('flow.php?step=validate_bonus', 'bonus_sn=' + bonusSn, validateBonusResponse, 'GET', 'JSON');

}



function validateBonusResponse(obj)

{



if (obj.error)

  {

    alert(obj.error);

    orderSelectedResponse(obj.content);

    try

    {

      document.getElementById('ECS_BONUSN').value = '0';

    }

    catch (ex) { }

  }

  else

  {

    orderSelectedResponse(obj.content);

  }

}



/* *

 * 改变发票的方式

 */

function changeNeedInv()

{

  var obj        = document.getElementById('ECS_NEEDINV');

  var objType    = document.getElementById('ECS_INVTYPE');

  var objPayee   = document.getElementById('ECS_INVPAYEE');

  var objContent = document.getElementById('ECS_INVCONTENT');

  var needInv    = obj.checked ? 1 : 0;

  var invType    = obj.checked ? (objType != undefined ? objType.value : '') : '';

  var invPayee   = obj.checked ? objPayee.value : '';

  var invContent = obj.checked ? objContent.value : '';

  objType.disabled = objPayee.disabled = objContent.disabled = ! obj.checked;

  if(objType != null)

  {

    objType.disabled = ! obj.checked;

  }



  Ajax.call('flow.php?step=change_needinv', 'need_inv=' + needInv + '&inv_type=' + encodeURIComponent(invType) + '&inv_payee=' + encodeURIComponent(invPayee) + '&inv_content=' + encodeURIComponent(invContent), '', 'GET','JSON');

}



/* *

 * 改变发票的方式

 */

function groupBuyChangeNeedInv()

{

  var obj        = document.getElementById('ECS_NEEDINV');

  var objPayee   = document.getElementById('ECS_INVPAYEE');

  var objContent = document.getElementById('ECS_INVCONTENT');

  var needInv    = obj.checked ? 1 : 0;

  var invPayee   = obj.checked ? objPayee.value : '';

  var invContent = obj.checked ? objContent.value : '';

  objPayee.disabled = objContent.disabled = ! obj.checked;



  Ajax.call('group_buy.php?act=change_needinv', 'need_idv=' + needInv + '&amp;payee=' + invPayee + '&amp;content=' + invContent, null, 'GET');

}



/* *

 * 改变缺货处理时的处理方式

 */

function changeOOS(obj)

{

  if (selectedOOS == obj)

  {

    return;

  }

  else

  {

    selectedOOS = obj;

  }



  Ajax.call('flow.php?step=change_oos', 'oos=' + obj.value, null, 'GET');

}



/* *

 * 检查提交的订单表单

 */

function checkOrderForm(frm)

{

  var paymentSelected = false;

  var shippingSelected = false;



  var mes = document.getElementById("mes").value;

  if (mes == "1") {

      if (confirm("亲，还未达免运费标准，是否继续添加商品？")) {

          window.location.href = "http://" + window.location.host;

          return false;

      }

  }



  // 检查是否选择了支付配送方式

  for (i = 0; i < frm.elements.length; i ++ )

  {

    if (frm.elements[i].name == 'shipping' && frm.elements[i].checked)

    {

      shippingSelected = true;

    }



    if (frm.elements[i].name == 'payment' && frm.elements[i].checked)

    {

      paymentSelected = true;

    }

  }



  if ( ! shippingSelected)

  {

    alert(flow_no_shipping);

    return false;

  }



  if ( ! paymentSelected)

  {

    alert(flow_no_payment);

    return false;

  }



  // 检查用户输入的余额

  if (document.getElementById("ECS_SURPLUS"))

  {

    var surplus = document.getElementById("ECS_SURPLUS").value;

    var error   = Utils.trim(Ajax.call('flow.php?step=check_surplus', 'surplus=' + surplus, null, 'GET', 'TEXT', false));



    if (error)

    {

      try

      {

        document.getElementById("ECS_SURPLUS_NOTICE").innerHTML = error;

      }

      catch (ex)

      {

      }

      return false;

    }

  }



  // 检查用户输入的积分

  if (document.getElementById("ECS_INTEGRAL"))

  {

    var integral = document.getElementById("ECS_INTEGRAL").value;

    var error    = Utils.trim(Ajax.call('flow.php?step=check_integral', 'integral=' + integral, null, 'GET', 'TEXT', false));



    if (error)

    {

      return false;

      try

      {

        document.getElementById("ECS_INTEGRAL_NOTICE").innerHTML = error;

      }

      catch (ex)

      {

      }

    }

  }

  frm.action = frm.action + '?step=done';

  return true;

}



/* *

 * 检查收货地址信息表单中填写的内容

 */

function checkConsignee(frm)

{

  var msg = new Array();

  var err = false;



  



  if (Utils.isEmpty(frm.elements['consignee'].value))

  {

    err = true;

    msg.push(consignee_not_null);

  }



  if ( frm.elements['email'] && !Utils.isEmpty(frm.elements['email'].value) && !Utils.isEmail(frm.elements['email'].value))

  {

    err = true;

    msg.push(invalid_email);

  }



  



  if (frm.elements['zipcode'] && frm.elements['zipcode'].value.length > 0 && (!Utils.isNumber(frm.elements['zipcode'].value)))

  {

    //err = true;

    //msg.push(zip_not_num);

  }



  /*if (Utils.isEmpty(frm.elements['tel'].value))

  {

    err = true;

    msg.push(tele_not_null);

  }

  else

  {*/

  if (!Utils.isEmpty(frm.elements['tel'].value) && !Utils.isTel(frm.elements['tel'].value))

    {

      err = true;

      msg.push(tele_invaild);

    }

  /*}*/



  if (frm.elements['mobile'] && frm.elements['mobile'].value.length > 0 && (!Utils.isMobile(frm.elements['mobile'].value)))

  {

    err = true;

    msg.push(mobile_invaild);

  }
  
  if (Utils.isEmpty(frm.elements['tel'].value) && Utils.isEmpty(frm.elements['mobile'].value)){
	  err = true;

	  msg.push("手机和电话必须填写一项！");
  }
  
  if (frm.elements['country'] && frm.elements['country'].value == 0)

  {

    msg.push(country_not_null);

    err = true;

  }



  if (frm.elements['province'] && frm.elements['province'].value == 0 && frm.elements['province'].length > 1)

  {

    err = true;

    msg.push(province_not_null);

  }



  if (frm.elements['city'] && frm.elements['city'].value == 0 && frm.elements['city'].length > 1)

  {

    err = true;

    msg.push(city_not_null);

  }



  if (frm.elements['district'] && frm.elements['district'].length > 1)

  {

    if (frm.elements['district'].value == 0)

    {

      err = true;

      msg.push(district_not_null);

    }

  }
  
  if (frm.elements['address'] && Utils.isEmpty(frm.elements['address'].value))

  {

    err = true;

    msg.push(address_not_null);

  }
  



  if (err)

  {

    message = msg.join("\n");

    alert(message);

  }

  return ! err;

}









/* JS代码增加_start By www.ecshop120.com */

function get_shipping_list(frm, goodsId) {

    data = frm.elements['country'].value + ',' + frm.elements['province'].value + ',' + frm.elements['city'].value + ',' + frm.elements['district'].value;

    string = '';

    if (frm.elements['country']) {

        if (frm.elements['country'].value) {

            string = string + frm.elements['country'].value + ',';

        } else {

            string = string + 0 + ',';

        }

    }

    if (frm.elements['province']) {

        if (frm.elements['province'].value) {

            string = string + frm.elements['province'].value + ',';

        } else {

            string = string + 0 + ',';

        }

    }

    if (frm.elements['city']) {

        if (frm.elements['city'].value) {

            string = string + frm.elements['city'].value + ',';

        } else {

            string = string + 0 + ',';

        }

    }

    if (frm.elements['district']) {

        if (frm.elements['district'].value) {

            string = string + frm.elements['district'].value + ',';

        } else {

            string = string + 0 + ',';

        }

    }



    if (/0,0,0,\d+/.test(string)) {//判断是否重新选择，而有最后一个



        document.getElementById('show_ship').innerHTML = '<br>请选择配送区域。';

        document.getElementById('show_ship').style.display = '';



    } else {



        var attr = getSelectedAttributes(document.forms['ECS_FORMBUY']);

        var qty = document.forms['ECS_FORMBUY'].elements['number'].value;



        Ajax.call('flow.php', 'step=show_shipping1&string=' + string + '&goods_id=' + goodsId + '&attr=' + attr + '&number=' + qty, shipping_list_response, "GET", "JSON");



    }



}



function shipping_list_response(res) {



    string = res.content;

    document.getElementById('show_ship').innerHTML = string;

    if (string != '') {

        document.getElementById('show_ship').style.display = '';

    }



}

/* JS代码增加_end By www.ecshop120.com */