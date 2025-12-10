/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

// Selector for block containing single delivery option
var ruch_selector_delivery = '.delivery-option';

// Selector for checkbox for active delivery option
var ruch_selector_for_service = '';
var ruch_was_init_call = false;

function testPkt17() {
	var val = $(ruch_selector_for_service).val();
	return testPkt(val);
}

// Test if seleced delivery option is Ruch option 
function testRuchServ17() {
	var val = $(ruch_selector_for_service).val();
	if(!val) return;
	var id = parseInt(val.substr(0, val.length - 1));
	var querySelector = testElementDeliveryOption();
	var ruch = false;
	var cod = 0;
    var cena = ruch_ceny[id];
	if(ruch_codserv.indexOf(id) != -1) {
		ruch = true;
		cod = 1;
	}
	else if(ruch_serv.indexOf(id) != -1) ruch = true;
	$('#ruchWidgetMapContainer').slideUp(100, function() {
		$(this).remove();
	});

	if(ruch) {
		start_widget(querySelector, cod, [cena, cena, cena, cena]);
		if (ruch_async_carrier_loaded) setSpecificValueOnPageIfNoPkt();
	}
}

function testRuchServ17_popup() {
	$('#ruchWidgetButtonContainer').remove();
	var val = $(ruch_selector_for_service).val();
	if(!val) return;

    var serv_id = $(ruch_selector_for_service).val();
    if(serv_id.endsWith(',')) serv_id = serv_id.slice(0, -1);
    if(ruch_debug) console.log('testRuchServ17_popup id="'+serv_id+'"');
	if (!ruch_serv.includes(parseInt(serv_id))) return;
	if(ruch_debug) console.log('testRuchServ17_popup after');
	$(ruch_selector_delivery + ':has(input:checked)').after('<div id="ruchWidgetButtonContainer"><button class="ruch_sel_point_button" type="button" onclick="ruchDisplayMap()">Wybierz punkt odbioru</button><div style="width: 100%; text-align: left;">' + ruch_html_info_popup + '</div></div>');
	if(ruch_debug) console.log($(ruch_selector_delivery + ':has(input:checked)'));
	set_selected_pkt_from_cache();
	if (ruch_async_carrier_loaded) setSpecificValueOnPageIfNoPkt();
}

function ruchDisplayMap() {
	var val = $(ruch_selector_for_service).val();
	if(!val) return;
	var ruch = false;
	if ($('.ruch-widget-map-wrapper-full-screen').length == 0) ruch = true;


	var cod = 0;
	var id = parseInt(val.substr(0, val.length - 1));
	var cena = ruch_ceny[id];
	if(ruch_codserv.indexOf(id) != -1) {
		cod = 1;
	}

	$(document.body).append('<div class="ruch-widget-map-container-full-screen"><div class="ruch-widget-map-wrapper-full-screen"><div class="ruch-widget-empty-container"></div></div></div>');
	$('.ruch-widget-map-container-full-screen').bind('click.ruch-remove-widget-map-container-full-screen',function() {
		$('.ruch-widget-map-container-full-screen').unbind('click.ruch-remove-widget-map-container-full-screen');
		$('.ruch-widget-map-container-full-screen').remove();
	});

	if(ruch)
	{
		start_widget('.ruch-widget-empty-container', cod, [cena, cena, cena, cena]);
	}

}

function testElementDeliveryOption() {
	if($('.form-fields:has(.delivery-options)').length == 1)
		return '.form-fields:has(.delivery-options)';

	return '.form-fields';
}

function ruchSelectWidgetMode() {
	if (ruch_display_map_as_popup)
		testRuchServ17_popup();
	else
		testRuchServ17();
}

function ruchRegisterCarrierEvent() {
		ruchSelectWidgetMode();

		$(ruch_selector_delivery + ' input').on('click', function() {
			ruchSelectWidgetMode();
		});

		//in specific presta versions there is a bug regarding this event. You have to delete and register again
		$("[name='confirmDeliveryOption']").click(function(event){
			var testPkt17Result = testPkt17();
			if (!testPkt17Result)
			{
				event.preventDefault();
				$("[name='confirmDeliveryOption']").removeClass('disabled');
				$('#js-delivery').unbind('submit');

			}
			else {
				$('#js-delivery').submit(function(){
					return true;
				});
			}
		});
		//end

		$('#checkout-delivery-step').on('click', function() {
			$('#checkout-delivery-step').removeClass('-complete');
			$('#checkout-payment-step').removeClass('-reachable').addClass('-unreachable');
			$('#checkout-delivery-step').off('click');
			$('#checkout-payment-step .content').remove();
			// ruchSelectWidgetMode();
		});
}

function ruchDetectionCarrierStop(ruch_detection_carrier_interval_id) {
    if(ruch_debug) console.log('ruchDetectionCarrierStop');
	clearInterval(ruch_detection_carrier_interval_id);
}

function ruchDetectionCarrierAsync() {
	var ruch_detection_carrier_interval_id = setInterval(function () {
		if($(ruch_selector_delivery + ' input').length > 0)
		{
			ruchRegisterCarrierEvent();
			registerValidationForSpecificOpc();
			if(ruch_chk_type != 'wk_opc') ruchDetectionCarrierStop(ruch_detection_carrier_interval_id);
		}
	}, ruch_detection_carrier_interval);

	setTimeout(function () {
		ruchDetectionCarrierStop(ruch_detection_carrier_interval_id);
	}.bind(ruch_detection_carrier_interval_id), ruch_detection_carrier_timeout);
}

// Initialize my events on delivery options and submits - called after page is loaded
function ruchDetectionCarrierStart() {
	if (ruch_was_init_call) return;
	ruch_was_init_call = true;

    if(ruch_chk_type == 'supercheckout') ruch_selector_delivery = '.highlight'; // For Supercheckout OPC 
    if(ruch_chk_type == 'wk_opc') ruch_selector_delivery = '.wk-shipping-list'; // For WK OPC
    ruch_selector_for_service = ruch_selector_delivery + ' input:checked';

	if (ruch_async_carrier_loaded)
		ruchDetectionCarrierAsync();
	else
		ruchRegisterCarrierEvent();
}


function registerValidationForSpecificOpc() {
	// modulosy.pl
	if ($("input[name='psgdpr']").length == 1)
	{
		$("input[name='psgdpr']").unbind('click');
		$("input[name='psgdpr']").click(function (){
			if (!$("input[name='psgdpr']")[0].checked)
			{
				return;
			}
			var testPkt17Result = testPkt17();
			if (!testPkt17Result)
			{
				$("input[name='psgdpr']").prop('checked', false);
			}
		});
	}

	// zoo24.pl
	if ($("input[name='conditions_to_approve[terms-and-conditions]']").length == 1)
	{
		$("input[name='conditions_to_approve[terms-and-conditions]']").unbind('click');
		$("input[name='conditions_to_approve[terms-and-conditions]']").click(function (){
			if (!$("input[name='conditions_to_approve[terms-and-conditions]']")[0].checked)
			{
				return;
			}
			var testPkt17Result = testPkt17();
			if (!testPkt17Result)
			{
				$("input[name='conditions_to_approve[terms-and-conditions]']").prop('checked', false);
			}
		});
	}
}

function setSpecificValueOnPageIfNoPkt() {
	// modulosy.pl
	if ($("input[name='psgdpr']").length == 1)
	{
		if (!ruch_selpkt)
		{
			$("input[name='psgdpr']").prop('checked', false);
		}
	}

	// zoo24.pl
	if ($("input[name='conditions_to_approve[terms-and-conditions]']").length == 1)
	{
		if (!ruch_selpkt)
		{
			$("input[name='conditions_to_approve[terms-and-conditions]']").prop('checked', false);
		}
	}
}
