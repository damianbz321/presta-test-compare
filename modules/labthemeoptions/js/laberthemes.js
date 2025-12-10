$('.control').click(function() {
	if ($(this).hasClass('inactive')) {
	$(this).removeClass('inactive').addClass('active');
	$('.lab-wrap').animate({left: '0'}, 500);
}
	else{
	$(this).addClass('inactive').removeClass('active');
	$('.lab-wrap').animate({left:'-228px'}, 500);
}
});
	
	
	
