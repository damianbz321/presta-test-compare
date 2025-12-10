/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
$(function(){
    $('.laberProd-cate .laber-Tab1 .nav-item').click(function(){
        $('.laberProd-cate .laber-Tab1 .nav-item').removeClass('laber-active');
        $(this).addClass('laber-active');
    });
});

$(document).ready(function ()
{	
    
        $('.cart-overview').prepend('<div class="cart-loader-custom"><img src="https://hifood.pl/themes/laber_wooka_v17_home1/assets/img/loader-cart-custom.gif"></div>');
    
	loadding();
        $('body').on('change', '.js-cart-line-product-quantity', function(){
            $(this).parent('.input-group').hide();
            $('.cart-loader-custom').show();
            setTimeout(function(){
                window.location.reload(1);
            }, 2000);
        });
        
        $('body').on('click', '.remove-from-cart', function(){
            $('.cart-loader-custom').show();
            setTimeout(function(){
                window.location.reload(1);
            }, 2000);
        });
        
});
$(document).ready(function () {
	$("#grid a").click(function(e) {
		$("#products").removeClass("active_list");
        $("#products").addClass("active_grid");
		setCookie('status_list_product','active_grid',1);
    });
	$("#list a").click(function(e) {
        $("#products").removeClass("active_grid");
		$("#products").addClass("active_list");
		setCookie('status_list_product','active_list',1);
    });
		if(getCookie('status_list_product')!=="" && getCookie('status_list_product')!=="active_grid"){
			$("#products").removeClass("active_grid");
			$("#products").addClass("active_list");
		}
});
function loadding() {
	$(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});
}
	
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}
