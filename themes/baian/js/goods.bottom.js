onload = function(){
  changePrice();
  fixpng();
  
  //alert('here!');
  //$.get("/admin/check_admin_online.php",function(data,status){if(data=='yes')alert("Data: " + data + "\nStatus: " + status);});
  //Ajax.call('/admin/check_admin_online.php', '', checkAdminStatus, 'GET', 'JSON');
  
  try { onload_leftTime(); }
  catch (e) {}
}

function checkAdminStatus(res){
	alert(res);
}


function changeAtt(t,goods_id) {
t.lastChild.checked='checked';
for (var i = 0; i<t.parentNode.childNodes.length;i++) {
	if (t.parentNode.childNodes[i].className == 'cattsel') {
		t.parentNode.childNodes[i].className = '';
	}
}

t.className = "cattsel";
var formBuy = document.forms['ECS_FORMBUY'];
spec_arr = getSelectedAttributes(formBuy);
Ajax.call('goods.php?act=get_products_qq800007396', 'id=' + spec_arr+ '&goods_id=' + goods_id, shows_number, 'GET', 'JSON');
changePrice();
}
function shows_number(result)
{
if(result.product_number !=undefined)
{
	document.getElementById('shows_number').innerHTML = result.product_number+g_goods_measure_unit;
}
else
{
	document.getElementById('shows_number').innerHTML = '0'+g_goods_measure_unit;
}
}


/**
 * 点选可选属性或改变数量时修改商品价格的函数
 */
function changePrice()
{
  var attr = getSelectedAttributes(document.forms['ECS_FORMBUY']);
  var qty = document.forms['ECS_FORMBUY'].elements['number'].value;

  /* 代码增加_start By www.ecshop120.com 多城市多仓库*/
  //var store_province_id = document.forms['ECS_FORMBUY'].elements['store_province_id'].value;
  var store_province_id = document.getElementById('store_province_id').value;
   if (store_province_id)
   {
	check_storeroom(store_province_id);
   }
   /* 代码增加_end By www.ecshop120.com */

  Ajax.call('goods.php', 'act=price&id=' + goodsId + '&attr=' + attr + '&number=' + qty, changePriceResponse, 'GET', 'JSON');
}

/**
 * 接收返回的信息
 */
function changePriceResponse(res)
{
  if (res.err_msg.length > 0)
  {
    alert(res.err_msg);
  }
  else
  {
    document.forms['ECS_FORMBUY'].elements['number'].value = res.qty;

    if (document.getElementById('ECS_GOODS_AMOUNT'))
      document.getElementById('ECS_GOODS_AMOUNT').innerHTML = res.result;
  }
}



function changeAtt(t) {
t.lastChild.checked='checked';
for (var i = 0; i<t.parentNode.childNodes.length;i++) {
        if (t.parentNode.childNodes[i].className == 'cattsel') {
            t.parentNode.childNodes[i].className = '';
        }
    }
t.className = "cattsel";
changePrice();
}



//$(document).ready(function(){  
//    $("#goodsPic").jqzoom(); 
//}); 


function check_storeroom(province_id)
     {
          var goods_id = g_goods_id;

	  var attr = getSelectedAttributes(document.forms['ECS_FORMBUY']);
	  //alert(attr);

	  Ajax.call('goods.php', 'act=check_storeroom&goods_id=' + goods_id + '&attr=' + attr + '&province_id=' + province_id , checkStoreroomResponse, 'GET', 'JSON');
     }
     function checkStoreroomResponse(res)
     {
          document.getElementById('province').innerHTML = res.result;
	  document.getElementById('store_province_id').value = res.store_province_id;
	  change_cart(res.error);
     }
     
	 
	 
function change_cart(error){
		if(error){

		document.getElementById('choose-btn-append').innerHTML = ''
					+'<a style="margin-top: 5px;" class="btn-append " id="InitCartUrl" '
					+" href='javascript:addToCart("+g_goods_id+")'>加入购物车<b></b></a> ";
			document.getElementById('choose-btn-append').setAttribute('class','btn');
			
		document.getElementById('flowChart').setAttribute('style','float: right;margin-right: 13px;margin-top: 0px;height: 29px;width: 136px;background: url(themes/baian/images/flowCart.png) no-repeat;');
		}else{
		document.getElementById('choose-btn-append').innerHTML = ''
					+'<a style="margin-top: 5px;background: url(/themes/baian/images/gray_cart.png);cursor: default; " class="btn-append " id="InitCartUrl" '
					+" href='#none' title='商品已无货'></a> ";
		document.getElementById('choose-btn-append').setAttribute('class','');
		document.getElementById('flowChart').style.display="none";
		//document.getElementById('flowChart').setAttribute('style','display:none');
		}
	}


