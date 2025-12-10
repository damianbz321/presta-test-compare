$(document).ready(function() {

    setTimeout(function() {
        $('.row-3, .row-4').each(function() {
            var image_height = $(this).find('.image-box img').height();
            var text_height = $(this).find('.description').height();
            // console.log(image_height+' | '+text_height)
            if (text_height < image_height) {
                $(this).find('.row-desc').css('min-height', image_height+'px');
            } else {
                $(this).find('.image-box').css('min-height', text_height+'px');
            }
        });
        $('.row-5').each(function() {
            var image_height = $(this).find('.row-image-1 img').height();
            var image_height2 = $(this).find('.row-image-2 img').height();
            if (image_height < image_height2) {
                $(this).find('.row-image-1').css('min-height', image_height2+'px');
            } else if (image_height > image_height2) {
                $(this).find('.row-image-2').css('min-height', image_height+'px');
            }
        });
    }, 500);

});
