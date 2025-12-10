/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2018 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

function reloadImageAndPrice(id_product, id_att_group, color_group_ids, id_pushoncart)
{

	var id_attribute = $('select[name="pushoncart_product_attribute['+id_att_group+']"]').val();
	var id_attributes = [];
	$(".pushoncart-selected-product-attribute_"+id_product).each(function( index ) {
		id_attributes.push($(this).val());
	});
	var is_color = color_group_ids.indexOf(parseInt(id_att_group));

	$.ajax({
			type : 'POST',
			url : poc_ajax_url,
			data : {
				id_pushoncart : id_pushoncart,
				id_product : id_product,
				id_attribute : id_attribute, /* Attribute that has been changed */
				id_attributes : id_attributes, /* All current selected attributes */
				id_att_group : parseInt(id_att_group),
				color_group_ids : color_group_ids,
				action : 'ReloadImageAndPrice',
				ajax : true,
			}
	}).done(function(data) {
		info = jQuery.parseJSON(data);

		$('#pushoncart_sale_price_'+id_pushoncart).text(info.sale_price);
		$('#pushoncart_price_'+id_pushoncart).text(info.price);

		if(is_color == 1)
			$("#pushoncart_product_img_"+id_product).attr("src", info['image_link']);

		if (parseInt(info.stock_check) == 0)
		{
			$('#out_of_stock_alert_pushoncart_'+id_pushoncart).css("display", "block");
			$('#pushoncart_add_to_cart_'+id_pushoncart).addClass("disabled");
		}
		else if (parseInt(info.stock_check) == 1)
		{
			$('#pushoncart_add_to_cart_'+id_pushoncart).removeClass("disabled");
			$('#out_of_stock_alert_pushoncart_'+id_pushoncart).css("display", "none");
		}
	});
}
