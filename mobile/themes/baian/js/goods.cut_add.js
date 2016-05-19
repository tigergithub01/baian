/*详情页减少商品数量*/
function goods_cut(goods_id) {
	/*var num_val = document.getElementById('number');
	var new_num = num_val.value;
	var Num = parseInt(new_num);
	if (Num > 1)
		Num = Num - 1;
	num_val.value = Num;*/
	
	var $original_num = $('#original_number');
	var $num = $('#number');
	var new_num=$num.val();
	if(isNaN(new_num)){alert('请输入数字');return false}
	var Num = parseInt(new_num);
	if(Num>1){
		Num=Num-1;
		jude_goods_number(goods_id,Num,$num,$original_num);
	}else{
		$num.val(Num);
	}
	
}

/*详情页增加商品数量*/
function goods_add(goods_id) {
	/*var num_val = document.getElementById('number');
	var new_num = num_val.value;
	var Num = parseInt(new_num);
	Num = Num + 1;
	num_val.value = Num;*/
	
	var $original_num = $('#original_number');
	var $num = $('#number');
	var new_num=$num.val();
	if(isNaN(new_num)){alert('请输入数字');return false}
	var Num = parseInt(new_num);
	Num=Num+1;
	
	jude_goods_number(goods_id,Num,$num,$original_num);
	
}


/*详情页直接改变商品数量*/
function change_goods_number(goods_id){
	var $original_num = $('#original_number');
	var $num = $('#number');
	var goods_number = $num.val();
	
	if(isNaN(goods_number)){
		//alert('请输入数字');
		$num.val("1");
	}
	
	if($num.val()<=1){
		$num.val("1");
	}
	
	jude_goods_number(goods_id,$num.val(),$num,$original_num);
}




/*增加减少库存时检查库存情况**/
function jude_goods_number(goods_id,goods_number,$num,$original_num){
	
	//设置选择的地址
	var addr_province_name= $("#addr_sel").find("li[data-index='0']").attr('title');
	var addr_city_name=     $("#addr_sel").find("li[data-index='1']").attr('title');
	var addr_district_name= $("#addr_sel").find("li[data-index='2']").attr('title');
	var addr_town_name=     $("#addr_sel").find("li[data-index='3']").attr('title');
	
	var address_name = addr_province_name+"&nbsp;"+addr_city_name+"&nbsp;"+addr_district_name+"&nbsp;"+addr_town_name;
	$("#address_name").html(address_name).attr('title',address_name);
	
	$.ajax({     
	    url:'flow.php?step=jude_goods_number',     
	    type:'post',  
	    dataType:'json', 
	    data:{
	    	"address[0]":$("#addr_country").val(),
	    	"address[1]":$("#addr_province").val(),
	    	"address[2]":$("#addr_city").val(),
	    	"address[3]":$("#addr_district").val(),
	    	"address[4]":$("#addr_town").val(),
	    	"goods_id"  :goods_id,
	    	"goods_number":goods_number,
	    },     
	    async :true, 
	    error:function(){ 
	    	alert('更新出错！');
	    },     
	    success:function(data){     	    	
	    	if(data.status==1){
	    		//change value
	    		$original_num.val(goods_number);
	    		$num.val(goods_number);
	    	}else{
	    		alert(data.message);
	    		$num.val($original_num.val());
	    	}
	    }  
	});	
}