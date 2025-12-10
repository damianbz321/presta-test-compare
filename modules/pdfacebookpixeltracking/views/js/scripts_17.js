$(document).ready(function() {

	$(document).on('change', 'input[name="payment-option"]', function () {
		execAddPaymentInfo();
	});

	prestashop.on('updateCart', function(params) {

		if (typeof(params) !== 'undefined' && typeof(prestashop.cart) !== 'undefined') {
			var iso_code = prestashop.currency.iso_code,
			 	product_id = params.reason.idProduct,
				product_id_product_attribute = params.reason.idProductAttribute;


			if (typeof(product_id) !== 'undefined' && typeof(product_id_product_attribute) !== 'undefined') {

				$.ajax({
					type: "POST",
					url: pdfacebookpixeltracking_ajax_link,
					data: {'action': 'updateCart', 'product_id': product_id, 'product_id_product_attribute' : product_id_product_attribute, 'secure_key':  pdfacebookpixeltracking_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event: AddToCart');
						fbq('track', 'AddToCart', {
							content_name: data.content_name,
							content_category: data.content_category,
							content_ids: [data.content_ids],
							content_type: 'product',
							value: data.content_value,
							currency: iso_code
						});
					}
				});
			}
		}
	});

	function PixelSetDelay(ms) {
        var cur_d = new Date();
        var cur_ticks = cur_d.getTime();
        var ms_passed = 0;
        while(ms_passed < ms) {
            var d = new Date();
            var ticks = d.getTime();
            ms_passed = ticks - cur_ticks;
        }
    }

	prestashop.on('updateProduct', function(params) {

		if (typeof(params) !== 'undefined') {
			var iso_code = prestashop.currency.iso_code,
				product_id = parseInt(document.getElementsByName('id_product')[0].value),
				groups = [],
				select_groups = document.getElementsByClassName('form-control-select'),
				input_color_group = document.getElementsByClassName('input-color'),
				input_radio_group = document.querySelector('.input-radio:checked');


			if (typeof(select_groups) != 'undefined' && select_groups != null) {
				for (select_count = 0; select_count < select_groups.length; select_count++) {
				  groups.push(select_groups[select_count].value);
				}
			}

			if (typeof(input_color_group) != 'undefined' && input_color_group != null) {
				for (color_count = 0; color_count < input_color_group.length; color_count++) {
					if (input_color_group[color_count].checked) {
						groups.push(input_color_group[color_count].value);
					}
				}
			}

			if (typeof(input_radio_group) != 'undefined' && input_radio_group != null) {
				for (radio_count = 0; radio_count < input_radio_group.length; radio_count++) {
					if (input_radio_group[radio_count].checked) {
						groups.push(input_radio_group[radio_count].value);
					}
				}
			}
			
			if (typeof groups !== 'undefined' && groups.length > 0 && typeof product_id !== 'undefined') {
				$.ajax({
					type: "POST",
					url: pdfacebookpixeltracking_ajax_link,
					data: {'action': 'updateProduct', 'product_id': product_id, 'attributes_groups': groups,'secure_key':  pdfacebookpixeltracking_secure_key, ajax : true},
					dataType: "json",
					success: function(data) {
						console.log('Fired up event: ViewContent on combination change');
						//console.log(data);
						PixelSetDelay(250);
						fbq('track', 'ViewContent', {
							content_name: data.content_name,
							content_category: data.content_category,
							content_ids: [data.content_ids],
							content_type: 'product',
							value: data.content_value,
							currency: iso_code
						});
					}
				});
			}
		}
	});
});