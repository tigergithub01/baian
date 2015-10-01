$(document).ready(function() {
	$('body').append('<div id="loading_box">' + lang.process_request + '</div>');
	$('#loading_box').ajaxStart(function(){
		var loadingbox = $(this);
		var left = -(loadingbox.outerWidth() / 2);
		loadingbox.css({'marginRight': left + 'px'});
		loadingbox.delay(3000).fadeIn(400);
	});
	$('#loading_box').ajaxSuccess(function(){
		$(this).stop().stop().fadeOut(400);
	});

	ieFixedInt();
	if ($('#page_index').length) indexInt();
	if ($('#page_goods').length) goodsInt();
});


function ieFixedInt() {
	if ($('html.ie6').length > 0 || $('html.ie7').length > 0 || $('html.ie8').length > 0) {
		$('#top_nav .has_sub, #header_nav .has_sub').append('<span class="after"></span>');
		$('#top_nav .inner').prepend('<span class="before"></span>');
		if (window.PIE) {
			$('#header_bar, #main .wrapper, .button, input[type=button], input[type=submit], input[type=reset], input[type=button].dim_button, input[type=submit].dim_button, input[type=reset], .pagination a, .pagination span, .tabs a').each(function() {
				PIE.attach(this);
			});
		}
	}
}

function indexInt() {
	var order_input = $('#order_query input[name="order_sn"]');
	var vote_form = $('#vote form');
	var subscription_email = $('#subscription input[name="email"]');
	$('#slider_hp').nivoSlider();
	$('#order_query').append(loader).append(result);
	order_input.tipsy({
		trigger:'manual',gravity:'s',fade: true
	}).focusout(function() {
		$(this).tipsy('hide');
	}).keypress(function() {
		$(this).tipsy('hide');
	});
	$('#order_query input[type="button"]').click(function() {
		orderQuery();
	});
	$('#vote').append(loader).append(result);
	vote_form.tipsy({
		trigger:'manual',gravity:'s',fade: true
	}).focusout(function() {
		$(this).tipsy('hide');
	});
	$('#vote input[name="option_id"]').change(function() {
		vote_form.tipsy('hide');
	});
	$('#subscription').append(loader).append(result);
	subscription_email.tipsy({
		trigger:'manual',gravity:'s',fade: true
	}).focusout(function() {
		$(this).tipsy('hide');
	}).keypress(function() {
		$(this).tipsy('hide');
	});
}

function goodsInt() {
	$('#goods_info').Tabiy();
	if ($('.properties').length) {
		$('.properties').Formiy();
		$('.properties dl').tipsy({gravity: 'e',fade: true,html:true});
		$('.properties label').tipsy({gravity: 's',fade: true,html:true});
	}
}
