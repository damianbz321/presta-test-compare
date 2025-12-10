jQuery(document).ready(function($){
	// home xip blog block mod
	var selectSlider = $('.home_blog_post_area');
	if (!!$.prototype.slick)

	// blog post format carousel
	var selectSlider = $('.post_thumbnail');
	if (!!$.prototype.slick)
	selectSlider.find('.post_format_items.carousel').slick({
		infinite: true,
		dots: false,
		autoplay: true,
		slide: '.item',
		slidesToShow : 1,
		slidesToScroll : 1,
		arrows: true,
		nextArrow : '<i class="fa fa-angle-right"></i>',
		prevArrow : '<i class="fa fa-angle-left"></i>',
	});
}); // doc ready