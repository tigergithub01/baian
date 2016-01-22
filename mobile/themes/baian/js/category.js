$('.more-ce').toggle(function(){
	$(this).addClass("pz-up");
	$(this).find('em').text('收起');
	$('#select-brand .content').removeClass('pz-hide').addClass("pz-show");
},function(){
	$(this).find('em').text('更多');
	$(this).removeClass("pz-up");
	$('#select-brand .content').removeClass('pz-show').addClass('pz-hide');
});