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
function test()
{
	console.log('123');
}

function update_div_class(id)
{
    if(id == "promo_random_label")
    {
        document.getElementById("promo_random_label").className = "btn btn-info";
        document.getElementById("promo_prioritize_label").className = "btn btn-default";
    }
    if(id == "promo_prioritize_label")
    {
        document.getElementById("promo_prioritize_label").className = "btn btn-info";
        document.getElementById("promo_random_label").className = "btn btn-default";
    }
    if(id == "promo_custom_yes_label")
    {
        document.getElementById("promo_custom_yes_label").className = "btn btn-info";
        document.getElementById("promo_custom_no_label").className = "btn btn-default";
        $('#design_tab').css("display", "");
        $("#design_help").show();
    }
    if(id == "promo_custom_no_label")
    {
        document.getElementById("promo_custom_no_label").className = "btn btn-info";
        document.getElementById("promo_custom_yes_label").className = "btn btn-default";
        $('#design_tab').css("display", "none");
        $("#design_help").hide();
    }
}

function erasePromo(id_pushoncart, id_product)
{
	$.ajax({
		data: {
			action : 'ErasePromo',
			id_pushoncart: id_pushoncart,
		}
	  }).done(function() {
			$("#pushoncart_"+id_pushoncart).remove();
			$("#"+id_product).remove();
	  });
}

function showStatsOrders(id_pushoncart)
{
	$.ajax({
			data: { id_pushoncart: id_pushoncart,
					action: 'ShowStatsOrders' }
	  }).done(function(html) {
			$("#statsOrdersTable_area").show(300);
			$("#statsOrdersTable_body").html(html);
			$("#statsOrdersTable").show(300);
			$("#statsOrdersTable_body").show(300);
	  });
}

function search(lang, translation)
{
	$.ajax({
			data: {
					value: $("#liveSearch").val(),
					action: 'search',
					lang: lang,
				}
	  }).success(function(msg)
	  {
			json = jQuery.parseJSON(msg);
			if(json.found_results == 1)
			{
				$("#liveSearchResults").html(json.html);
				$("#liveSearchResults").show();
			}
	  });
}

function archivePromo(id_pushoncart)
{
	$.ajax({
			data: {id_pushoncart: id_pushoncart, action: 'DeleteProduct'}
	  }).done(function(msg) {
			$("#discountTable").hide();
			$("#"+id_pushoncart).remove();
	  });
}

function enable(id_pushoncart)
{
	$.ajax({
			data: { id_pushoncart: id_pushoncart,
					action: 'Enable' }
	  }).done(function() {
		  refreshTable();
	  });
}

function move(direction, id_product)
{
	$.ajax({
			data: { direction: direction,
					id_product: id_product,
					action: 'Move' }
	  }).done(function() {
		  var row = $("#id_product");
			if (direction == "up") {
				row.insertBefore(row.prev());
			} else {
				row.insertAfter(row.next());
			}
		   refreshTable();
	  });
}

function enableMultipleCodes(id_pushoncart)
{
	$.ajax({
			data: { action : 'EnableMultipleCodes',
					id_pushoncart: id_pushoncart}
	  }).done(function() {
		  refreshTable();
	  });
}

function refreshTable()
{
	$.ajax({
		data: {
			action : 'RefreshTable'
		}
	  }).done(function(html) {

		   $("#table_body").html(html);
	  });
}

function setVal(value, val)
{
	$.ajax({
		data: {
			action : 'SetVal',
			value : value,
			val: val,
		},
		success: function(msg)
		{
			$("#promo_alert_success").show();
			setTimeout('$("#promo_alert_success").hide(500)',3000);
		},
		error: function(jqXHR, textStatus, errorThrown){ console.log(jqXHR); console.log(textStatus); console.log(errorThrown); }
	});
}

function update_promo_count()
{
	var val = $("#promo_count").val();
	$.ajax({
		data: {
			action : 'SetPromoCount',
			val: val,
		},
		success: function(msg)
		{
			$("#promo_alert_success").show();
			setTimeout('$("#promo_alert_success").hide(500)',3000);
		},
		error: function(jqXHR, textStatus, errorThrown){ console.log(jqXHR); console.log(textStatus); console.log(errorThrown); }
	});
}

function selectProduct(id_product, product_name, translation, lang)
{
	$.ajax({
			data: {
				id_product: id_product,
				lang: lang,
				action : 'SelectProduct'
			}
	}).done(function(html) {
			$("#selectedProduct").html(html);
	});

	var content = $("#selectedProduct").html();
	$("#selectedProduct").append(content+'<br/>' + translation + product_name + '</p></center></div>');
	$('#liveSearch').val(product_name);
	$("#product").val(id_product);
}

function showDiscountInfo(id_pushoncart)
{
	$.ajax({
			data: { action : 'ShowDiscountInfo',
					id_pushoncart: id_pushoncart
				}
	}).done(function(html) {
			$("#discounts_table_body").html(html);
			$("#discounts_table_body").show(300);
			$("#discountTable").show(300);
	});
}

// Main Function
var Main = function () {
	// function to custom select
	var runCustomElement = function () {
				// check submit
				var is_submit = $("#modulecontent").attr('role');
				if (is_submit == 1) {
					$(".list-group-item").each(function() {
						if ($(this).hasClass('active')) {
							$(this).removeClass("active");
						}
						else if ($(this).attr('href') == "#config") {
							$(this).addClass("active");
						}
					});
					$('#config').addClass("active");
					$('#documentation').removeClass("active");
				}
				if (is_submit == 2) {
					$(".list-group-item").each(function() {
						if ($(this).hasClass('active')) {
							$(this).removeClass("active");
						}
						else if ($(this).attr('href') == "#poc_statistics") {
							$(this).addClass("active");
						}
					});
					$('#poc_statistics').addClass("active");
					$('#documentation').removeClass("active");
				}

				$('.module_confirmation').delay(5000).hide(100);

		// toggle panel
		$(".list-group-item").on('click', function() {
			var $el = $(this).parent().closest(".list-group").children(".active");
			if ($el.hasClass("active")) {
				$el.removeClass("active");
				$(this).addClass("active");
			}
		});

		// Hide ugly toolbar
		$('table[class="table"]').each(function(){
			$(this).hide();
			$(this).next('div.clear').hide();
		});

		// Hide ugly multishop select
		if (typeof(_PS_VERSION_) !== 'undefined') {
			var version = _PS_VERSION_.substr(0,3);
			if(version === '1.5') {
				$('.multishop_toolbar').addClass("panel panel-default");
				$('.shopList').removeClass("chzn-done").removeAttr("id").css("display", "block").next().remove();
				cloneMulti = $(".multishop_toolbar").clone(true, true);
				$(".multishop_toolbar").first().remove();
				cloneMulti.find('.shopList').addClass('selectpicker show-menu-arrow').attr('data-live-search', 'true');
				cloneMulti.insertBefore("#modulecontent");
				// Copy checkbox for multishop
				cloneActiveShop = $.trim($('table[class="table"] tr:nth-child(2) th').first().html());
				$(cloneActiveShop).insertAfter("#tab_translation");
			}
		}

		// Custom Select
		$('.selectpicker').selectpicker();
		$('.datepicker').datepicker({
			format: 'yyyy-mm-dd',
			startDate: '-3d'
		}).on('changeDate', function(e){
			$(this).datepicker('hide');
		});

		// Fix bug form builder + bootstrap select
		var z = 1;
		$('.selectpicker').each(function(){
			var select = $(this);
			select.on('click', function() {
				$(this).parents('.bootstrap-select').addClass('open');
				$(this).parents('.bootstrap-select').toggleClass('open');
			});
		});

				setTimeout('$("#success_alert").hide(500)',10000);
				$("#ranges_yes").hide(); // Hides the ranges on the page load.
				$("#ranges_no").show();
				$("#range_1").hide();
				$("#range_3").hide();
				$("#range_4").hide();
				$("#range_2").show();
				$("#cart_max_min_div").hide();
				$("#cart_minimum").hide();
				$("#discountTable").hide();

				var content = $.trim($('#table_body').html());

				if(content.length == 0)
				{
					$("#discount_table_area").hide();
				}
				else
				{
					$("#discount_table_area").show();
				}

		// Custom Textarea
		$('.textarea-animated').autosize({append: "\n"});
	};
	return {
		//main function to initiate template pages
		init: function () {
			runCustomElement();
		}
	};
}();

function clearProductBanner()
{
	var val1 = document.getElementById("liveSearch");
	val1.value='';
	$("#productBanner").hide();
}

function prevent(e){
	if(e.keyCode == 13)
		e.preventDefault();
}

/* design tab */
function setTopPromoName()
{
	var top_promo_name_checkbox = $("#top_promo_name_checkbox").attr('checked');
	var flag;
	if(top_promo_name_checkbox == "checked")
	{
		$('#top_banner_offer').hide();
		$('#top_banner_promo_name').show();
		flag = 1;
	}
	else
	{
		$('#top_banner_offer').show();
		$('#top_banner_promo_name').hide();
		flag = 0;
	}
	setVal('PUSHONCART_DESIGN_PROMO_TEXT', flag);
}

function showColorPickerDesignTab(id_design)
{
	$('#'+id_design+'_div').show(300);
}

function hideColorPickerDesignTab(id_design)
{
	$('#'+id_design+'_div').hide(300);
}

function setPromoColor(id_design)
{
	if(id_design == 'banner_bg_color')
	{
		banner_bg_color = $("#banner_bg_color").val();
		$('.poc-promo-header').css('background-color', banner_bg_color);
		$('.poc-promo-table').css('border', '1px solid '+banner_bg_color);
		setVal('PUSHONCART_DESIGN_TOP_BANNER_BG', banner_bg_color);
	}
	else if(id_design == 'banner_text_color')
	{
		banner_text_color = $("#banner_text_color").val();
		$('.poc-promo-header').css('color', banner_text_color);
		setVal('POC_DESIGN_TOP_BANNER_TEXT', banner_text_color);
	}
	else if(id_design == 'default' || id_design == 'success' || id_design == 'info' || id_design == 'warning' || id_design == 'danger')
	{
		$('#pushoncart_add_to_cart').removeClass();
		$('#pushoncart_add_to_cart').addClass('btn btn-'+id_design+' reduction');
		setVal('PUSHONCART_DESIGN_BUTTON', id_design);
	}
}

$(function() {

	$.ajaxSetup({
		type: "POST",
		url: pushoncart_admin_module_ajax_url,
		data: {
			ajax : true,
			id_tab : pushoncart_id_tab,
			controller : pushoncart_admin_module_controller,
		}
	});

	$("input").keypress( function(event){ prevent(event); });
	$("input").keydown( function(event){ prevent(event); });
	$("input").keyup( function(event){ prevent(event); });

	$("#promo_random_label").click(function(){
		setVal('PUSHONCART_ORDER_METHOD', 1);
	});
	$("#promo_prioritize_label").click(function(){
		setVal('PUSHONCART_ORDER_METHOD', 0);
	});
	$("#promo_custom_yes_label").click(function(){
		setVal('PUSHONCART_CUSTOM', 1);
	});
	$("#promo_custom_no_label").click(function(){
		setVal('PUSHONCART_CUSTOM', 0);
	});

	$("#liveSearch").keyup(function (e) {
		search_val = $("#liveSearch").val();
		if(e.keyCode != 13)
		{
			if (search_val.length >= 3) {

			lang = $("#lang_search").val();
			translation = $("#trans_search").val();
				search(lang, translation);
			}

			if (search_val.length < 3) {
				$("#liveSearchResults").hide();
			}
		}
		else
			e.preventDefault();
	});

	// Load functions
	Main.init();
	$("#promo_alert_success").hide();
	$("#promo_alert_fail").hide();
	//$('.dataTables_paginate').child().each().attr("style", "list-style-type: none;");

});
