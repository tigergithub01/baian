

var loader = '<div class="loader">&nbsp;</div>', result = '<div class="result"></div>', status = '<div class="status"></div>', page = 'undefined', action = 'undefined';

function clearHistory() {
	document.cookie = 'ECS[history]='+escape('');
	$('#history').animate({height:'0',opacity:'0'}, 1000, '', function(){
		$('#history .tip').tipsy('hide');
		$('#history').hide();
	});
}

function orderQuery() {
	var order_sn = order_input.val(), reg = /^[\.0-9]+/;
	if (order_sn.length < 10 || ! reg.test(order_sn)) {
		order_input.focus().tipsy('show');
		return;
	}
	else {
		$.get(
			'user.php?act=order_query&order_sn=s',
			'order_sn=s' + order_sn,
			function(response){
				var order_result = $('#order_query .result');
				var res = $.evalJSON(response);
				if (res.message != '') {
					cAlert(res.message);
				}
				if (res.error == 0) {
					cAlert(res.content);
					$('form[name^=queryForm] a').click(function(){
						$(this).parents('form').submit();
						return false;
					});
				}
			},
			'text'
		);
	}
}

function submitVote()
{
	var type = $('#vote input[name="type"]').val(), vote_id = $('#vote input[name="id"]').val(), option = '';
	$('#vote input[name="option_id"]:checked').each(function() {
		var option_id = $(this).val();
		option += option_id + ',';
	});

	if (option == '') {
		$('#vote form').tipsy('show');
		return;
	} else {
		var vote_result = $('#vote .result');
		$('#vote .loader').css({visibility:'visible'}).fadeTo(0, 1000);
		$.post(
			'vote.php',
			{vote: vote_id, options: option, type: type},
			function(response){
				var res = $.evalJSON(response);
				if (res.message != '') {
					vote_result.css({display:'block',backgroundColor:'#ff5215'});
					vote_result.html(res.message);
				}
				if (res.error == 0) {
					$('#vote_inner').html(res.content);
				}
				vote_result.animate({backgroundColor:'#fff'}, 1000);
				$('#vote .loader').fadeTo(1000, 0);
			},
			'text'
		);
	}
}

function addEmailList() {
	var email = subscription_email.val();
	if (!isValidEmail(email)) {
		subscription_email.focus().tipsy('show');
		return;
	}
	else {
		$('#subscription .loader').css({visibility:'visible'}).fadeTo(0, 1000);
		$.get(
			'user.php?act=email_list&job=add',
			'email=' + email,
			function(response){
				$('#subscription .result').css({display:'block',backgroundColor:'#97cf4d'}).html(response).animate({backgroundColor:'#fff'}, 1000);
				$('#subscription .loader').fadeTo(1000, 0);
			},
			'text'
		);
	}
}
function cancelEmailList()
{
	var email = subscription_email.val();
	if (!isValidEmail(email)) {
		subscription_email.focus().tipsy('show');
		return;
	}
	else {
		var subscription_result = $('#subscription .result');
		var subscription_loader = $('#subscription .loader');
		subscription_loader.css({visibility:'visible'}).fadeTo(0, 1000);
		$.get(
			'user.php?act=email_list&job=del',
			'email=' + email,
			function(response){
				subscription_result.css({display:'block',backgroundColor:'#97cf4d'}).html(response).animate({backgroundColor:'#fff'}, 1000);
				subscription_loader.fadeTo(1000, 0);
			},
			'text'
		);
	}
}
function isValidEmail(email) {
	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return filter.test(email);
}

function getAttrSiy(area) {
	var attrList = new Array();
	area.find('input[name^="spec_"]:checked, select[name^="spec_"]').each(function(i) {
		attrList[i] = $(this).val();
	});
	return attrList;
}

(function($) {
$.fn.ChangePriceSiy = function() {
	var area = $(this);
	loadPrice(area);
	area.find('input[name^="spec_"], select[name^="spec_"]').change(function() {
		loadPrice(area);
	});
	area.find('input[name="number"]').keyup(function() {
		var number = area.find('input[name="number"]').val();
		if (number.length > 0) {
			loadPrice(area);
		}
	});
};
})(jQuery);

function loadPrice(area) {
	var attr = getAttrSiy(area);
	var number = area.find('input[name="number"]').val();
	if (number < 1) {
		var qty = '1';
	}
	else {
		var qty = number;
	};
	$.get(
		'goods.php',
		'act=price&id=' + goodsId + '&attr=' + attr + '&number=' + qty,
		function(response){
			var res = $.evalJSON(response);
			if (res.err_msg.length > 0) {
				$.fn.colorbox({html:'<div class="message_box">' + res.err_msg + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
			}
			else {
				area.find('[name="number"]').val(res.qty);
				area.find('.amount').html(res.result);
			}
		},
		'text'
	);
};


function buy(id, num, parent) {
	var goods = new Object();
	var spec_arr = new Array();
	var fittings_arr = new Array();
	var number = 1;
	var form = $('#purchase_form');
	var quick = 0;

	if (form.length > 0) {
		spec_arr = getAttrSiy(form);
		var formNumber = form.find('[name="number"]').val();
		if (formNumber) {
			number = formNumber;
		}
		quick = 1;
	}
	if (num > 0) {
		number = num;
	}

	goods.quick    = quick;
	goods.spec     = spec_arr;
	goods.goods_id = id;
	goods.number   = number;
	goods.parent   = (typeof(parent) == 'undefined') ? 0 : parseInt(parent);

	$.post(
		'flow.php?step=add_to_cart',
		{goods: $.toJSON(goods)},
		function(response){
			var res = $.evalJSON(response);
			if (res.error > 0) {
				if (res.error == 2) {
					$.fn.colorbox({html:'<div class="message_box mb_question">' + '对不起，该商品已经库存不足暂停销售。' + '<p class="action"><a style="margin-left: 70px;" href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.continue_browsing_products + '</span></a><a style="display:none;" href="user.php?act=add_booking&id=' + res.goods_id + '&spec=' + res.product_spec + '" class="tc_button brighter_button"><span>' + lang.booking + '</span></a></p></div>'});
				}
				else if (res.error == 6) {
					openSpeSiy(res.message, res.goods_id, number, res.parent);
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="tc_button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
				}
			}
			else {
				$('#cart').html(res.content);
				//loadCart();
				if (res.one_step_buy == '1') {
					location.href = 'flow.php?step=checkout';
				}
				else {
					if ($('#page_flow').length > 0) {
						location.href = 'flow.php?step=cart';
					} else {
						$.fn.colorbox({html:'<div class="cart-pop" style="position: absolute; z-index: 9999; left: -100px; bottom: -92px; -webkit-transform-origin-x: 0px; -webkit-transform-origin-y: 0px; opacity: 1; -webkit-transform: scale(1, 1); "> <span class="close" onclick="$.fn.colorbox.close(); return false;" style="float:right;margin-right:8px;cursor:pointer;">x</span><div class="ico-succ"><span class="att-succ">添加成功！</span><span class="cart-succ">商品已成功加入购物车</span></div> <div class="cp-lnk"><a href="./" >再逛逛</a><a href="flow.php?step=cart"><span class="bar"></span>去购物车</a></div></div>'});
					}
				}
			}
		},
		'text'
	);
}

function openSpeSiy(message, goods_id, num, parent) 
{
	var html = '<div class="message_box" id="properties_box"><div class="properties_wrapper">';
	for (var spec = 0; spec < message.length; spec++) {
		var tips = '';
		if (message[spec]['attr_type'] == 2) {
			var tips = 'title="' + lang.multi_choice + '"';
		};
		html += '<dl class="properties clearfix" ' + tips + '><dt>' +  message[spec]['name'] + '：</dt>';
		if (message[spec]['attr_type'] == 1) {
			html += '<dd class="radio">';
			for (var val_arr = 0; val_arr < message[spec]['values'].length; val_arr++) {
				var check = '';
				var title = '';
				if (val_arr == 0) {
					var check = 'checked="checked"';
				}
				if (parseInt(message[spec]["values"][val_arr]["price"]) > 0) {
					var title = 'title="' + lang.increase + message[spec]["values"][val_arr]["format_price"] + '"';
				} else if (parseInt(message[spec]["values"][val_arr]["price"]) < 0) {
					var title = 'title="' + lang.reduce + message[spec]["values"][val_arr]["format_price"] + '"';
				}
				html += '<label for="spec_value_'+ message[spec]["values"][val_arr]["id"] +'" ' + title + '><input type="radio" name="spec_' + message[spec]["attr_id"] + '" value="' + message[spec]["values"][val_arr]["id"] + '" id="spec_value_' + message[spec]["values"][val_arr]["id"] + '" ' + check + ' />' + message[spec]["values"][val_arr]["label"] + '</label>';
			} 
			html += '<input type="hidden" name="spec_list" value="' + val_arr + '" /></dd>';
		}
		else {
			html += '<dd class="checkbox">';
			for (var val_arr = 0; val_arr < message[spec]["values"].length; val_arr++) {
				var title = '';
				if (parseInt(message[spec]["values"][val_arr]["price"]) > 0) {
					var title = 'title="' + lang.increase + message[spec]["values"][val_arr]["format_price"] + '"';
				} else if (parseInt(message[spec]["values"][val_arr]["price"]) < 0) {
					var title = 'title="' + lang.reduce + message[spec]["values"][val_arr]["format_price"] + '"';
				}
				html += '<label for="spec_value_' + message[spec]["values"][val_arr]["id"] + '" ' + title + '><input type="checkbox" name="spec_' + message[spec]["attr_id"] + '" value="' + message[spec]["values"][val_arr]["id"] + '" id="spec_value_' + message[spec]["values"][val_arr]["id"] + '" />' + message[spec]["values"][val_arr]["label"] + '</label>';
			}
			html += '<input type="hidden" name="spec_list" value="' + val_arr + '" /></dd>';
		}
		html += "</dl>";
	}
	html += '</div><p class="action"><a href="javascript:submitSpeSiy(' + goods_id + ',' + num + ',' + parent + ')" class="buy brighter_button">' + lang.buy + '</a><a href="javascript:void(0);" class="brighter_button" onclick="$.fn.colorbox.close(); return false;">' + lang.cancel + '</a></p></div>';
	
	$.fn.colorbox({scrolling:false,html: html});
	$('.properties').Formiy();
	$('.properties dl').tipsy({gravity: 'e',fade: true,html:true});
	$('.properties label').tipsy({gravity: 's',fade: true,html:true});
}

function submitSpeSiy(goods_id, num, parent) 
{
	var goods = new Object();
	var spec_arr = new Array();
	var fittings_arr = new Array();
	var number = 1;
	var area = $('#properties_box');
	var quick = 1;

	if (num > 0) {
		number = num;
	}

	var spec_arr = getAttrSiy(area);

	goods.quick    = quick;
	goods.spec     = spec_arr;
	goods.goods_id = goods_id;
	goods.number   = number;
	goods.parent   = (typeof(parent) == "undefined") ? 0 : parseInt(parent);

	$.post(
		'flow.php?step=add_to_cart',
		{goods: $.toJSON(goods)},
		function(response){
			var res = $.evalJSON(response);
			if (res.error > 0) {
				if (res.error == 2) {
					$.fn.colorbox({html:'<div class="message_box mb_question">' + res.message + '<p class="action"><a href="user.php?act=add_booking&id=' + res.goods_id + '&spec=' + res.product_spec + '" class="button brighter_button"><span>' + lang.booking + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
				}
				else if (res.error == 6) {
					openSpeSiy(res.message, res.goods_id, number, res.parent);
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
				}
			}
			else {
				$('#cart').html(res.content);
				//loadCart();
				if (res.one_step_buy == '1') {
					location.href = 'flow.php?step=checkout';
				}
				else {
					if ($('#page_flow').length > 0) {
						location.href = 'flow.php?step=cart';
						//window.location.reload();
					} else {
						$.fn.colorbox({html:'<div class="message_box mb_info">' + lang.add_to_cart_success + '<p class="action"><a href="flow.php?step=cart" class="button brighter_button"><span>' + lang.checkout_now + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
					};
				}
			}
		},
		'text'
	);
}

function collect(id)
{
	$.get(
		'user.php?act=collect',
		'id=' + id,
		function(response){
			var res = $.evalJSON(response);
		
				$.fn.colorbox({html:'<div class="cart-pop" style="position: absolute; z-index: 9999; left:-200px; bottom: -92px; -webkit-transform-origin-x: 0px; -webkit-transform-origin-y: 0px; opacity: 1; -webkit-transform: scale(1, 1); "><div class="ico-succ"><span class="cart-succ">' + res.message + '</span></div> <div class="cp-lnk"><a onclick="$.fn.colorbox.close(); return false;" >' + lang.confirm + '</a></div></div>'});
		},
		'text'
	);
}


function addPackageToCart(id) {
	var package_info = new Object();
	var number       = 1;

	package_info.package_id = id
	package_info.number     = number;

	$.post(
		'flow.php?step=add_package_to_cart',
		{package_info: $.toJSON(package_info)},
		function(response){
			var res = $.evalJSON(response);
			if (res.error > 0) {
				if (res.error == 2) {
					$.fn.colorbox({html:'<div class="message_box mb_question">' + res.message + '<p class="action"><a href="user.php?act=add_booking&id=' + res.goods_id + '" class="button brighter_button"><span>' + lang.booking + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.cancel + '</a></p></div>'});
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
				}
			}
			else {
				$('#cart').html(res.content);
				//loadCart();
				if (res.one_step_buy == '1') {
					location.href = 'flow.php?step=checkout';
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + lang.add_to_cart_success + '<p class="action"><a href="flow.php?step=cart" class="button brighter_button"><span>' + lang.checkout_now + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
				}
			}
		},
		'text'
	);
}

function fittings_to_flow(goodsId,parentId)
{
	var goods        = new Object();
	var spec_arr     = new Array();
	var number       = 1;
	goods.spec     = spec_arr;
	goods.goods_id = goodsId;
	goods.number   = number;
	goods.parent   = parentId;

	$.post(
		'flow.php?step=add_to_cart',
		{goods: $.toJSON(goods)},
		function(response){
			var res = $.evalJSON(response);
			if (res.error > 0) {
				if (res.error == 2) {
					$.fn.colorbox({html:'<div class="message_box mb_question">' + res.message + '<p class="action"><a href="user.php?act=add_booking&id=' + res.goods_id + '&spec=' + res.product_spec + '" class="button brighter_button"><span>' + lang.booking + '</span></a><a href="javascript:void(0);" class="tool_link" onclick="$.fn.colorbox.close(); return false;">' + lang.continue_browsing_products + '</a></p></div>'});
				}
				else if (res.error == 6) {
					openSpeSiy(res.message, res.goods_id, number, res.parent);
				}
				else {
					$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
				}
			} else {
				location.href = 'flow.php?step=cart';
			}
		},
		'text'
	);
}


function validAndTip(obj){if(obj.val().length > 0){obj.tipsy('hide');}return false;}
function validAndTipNext(obj){if(obj.val().length > 0){obj.next().tipsy('hide');}else{obj.next().tipsy('show');}return false;}


/* =user */
function sendHashMail() {
	$.get(
		'user.php?act=send_hash_mail',
		'',
		function(response){
			var res = $.evalJSON(response);
			$.fn.colorbox({html:'<div class="message_box mb_info">' + res.message + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
			$('#comment_wrapper').html(res.content);
		},
		'text'
	);
}


/* =snatch */
function bid()
{
	var form = $('#snatch_form');
	var id = form.find('input[name="snatch_id"]').val();
	var priceInput = form.find('input[name="price"]');
	var price = priceInput.val();
	priceInput.tipsy({gravity: 'w', fade: true, trigger: 'manual'}).focusout(function() {
		$(this).tipsy('hide');
	}).keypress(function() {
		$(this).tipsy('hide');
	});;
	if (price == '') {
		priceInput.attr('original-title', lang.error_price_required).tipsy('show');
		return;
	} else {
		var reg = /^[\.0-9]+/;
		if ( ! reg.test(price)) {
			priceInput.attr('original-title', lang.error_price).tipsy('show');
			return;
		} else {
			$.post(
				'snatch.php?act=bid',
				{id: id, price: price},
				function(response){
					var res = $.evalJSON(response);
					if (res.error == 0) {
						$('#snatch_wrapper').html(res.content);
					} else {
						$.fn.colorbox({html:'<div class="message_box mb_info">' + res.content + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
					}
				},
				'text'
			);
		}
	}
}

function newPrice(id) {
	$.get(
		'snatch.php?act=new_price_list',
		'id=' + id,
		function(response){
			$('#price_list').html(response);
			$('#price_list').find('.bd').css({backgroundColor:'#ffc'}).animate({backgroundColor:'#fff'}, 1000);
		},
		'text'
	);
}

function regionChanged(obj, type, selName) {
	var parent = obj.options[obj.selectedIndex].value;
	loadRegions(parent, type, selName);
}
function loadRegions(parent, type, target) {
	var target = $('#'+target+'');
	target.after(loader).next('.loader').css({visibility:'visible'}).fadeTo(0, 1000);
	target.nextAll('select').css('display','none');
	$.get(
		'region.php',
		'type=' + type + '&target=' + target + "&parent=" + parent,
		function(response){
			var res = $.evalJSON(response);
			target.next('.loader').fadeTo(500, 0, function(){
				$(this).remove();
			});
			target.find('option[value!="0"]').remove();
			if (res.regions.length == 0) {
				target.css('display','none');
				target.nextAll('select').css('display','none');
			} else {
				target.css('display','');
				for (i = 0; i < res.regions.length; i ++ ) {
					target.append('<option value="' + res.regions[i].region_id + '">' + res.regions[i].region_name + '</option>');
				}
			};
		},
		'text'
	);
}

function loadCart(show) {
	$.post(
		'flow.php?step=cart&ajax=1',
		'',
		function(response){
			$('#cart .loader').fadeTo(1000, 0);
			$("#cart").html(response);
			if (show == 1) {
				$('#cart .list_wrapper').css({display:'block',backgroundColor:'#ffc'}).animate({backgroundColor:'#fff'}, 1000);
				//$('#cart .list_wrapper .close').css({display:'block'});
			}
		},
		'text'
	);
}

function cAlert(content) {
	$.fn.colorbox({transition:'none',html:'<div class="message_box mb_info">' + content + '<p class="action"><a href="javascript:void(0);" class="button brighter_button" onclick="$.fn.colorbox.close(); return false;"><span>' + lang.confirm + '</span></a></p></div>'});
}


function submitTag() {
	var tag = $('#tag_form input[name="tag"]').val();
	var goods_id = $('#tag_form input[name="goods_id"]').val();
	if (tag.length > 0 && parseInt(goods_id) > 0) {
		$.post(
			'user.php?act=add_tag',
			{id: goods_id, tag: tag},
			function(response){
				var res = $.evalJSON(response);
				if (res.error > 0) {
					cAlert(res.message);
				} else {
					var tags = res.content;
					var html = '';
					for (i = 0; i < tags.length; i++) {
						html += '<a href="search.php?keywords='+tags[i].word+'" class="item">' +tags[i].word + '<em>' + tags[i].count + '</em></a>';
					}
					$('#tags').html(html);
				}
			},
			'text'
		);
	}
}

/*切换产品图库*/
function sw_goodsimg(obj)
{
	imgs = obj.getElementsByTagName("IMG");
	document.getElementById("goods_bimg").src = imgs[0].src;
	var glist_divs = document.getElementById("goods_gallery").getElementsByTagName("DIV");
	for(var i=0;i<glist_divs.length;i++)
	{
		if(obj.parentNode == glist_divs[i])
		{
			glist_divs[i].className = "selectImg";
		}
		else
		{
			glist_divs[i].className = "";	
		}
	}
	
	
}