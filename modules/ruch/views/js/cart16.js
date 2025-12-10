/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

var ruch_selector_for_service = 'input.delivery_option_radio:checked';
var ruch_was_init_call = false;

function testPkt16() {
	var val = $(ruch_selector_for_service).val();
	return testPkt(val);
}

function testRuchServ16() {
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
	if(ruch) start_widget(querySelector, cod, [cena, cena, cena, cena]);
}

function testRuchServ16_popup() {
	// in specific opc this section is reload
	$('#ruchWidgetButtonContainer').remove();
	$('#btn_place_order').prop( "disabled", false );
	var val = $(ruch_selector_for_service).val();
	if(!val) return;
	if (!ruch_serv.includes(parseInt($(ruch_selector_for_service).val()))) return;
	var querySelector = testElementDeliveryOption();
	// $(querySelector).after('<div id="ruchWidgetButtonContainer"><button class="ruch_sel_point_button" type="button" onclick="ruchDisplayMap()">Wybierz punkt odbioru</button><div style="width: 100%; text-align: left;">' + ruch_html_info_popup + '</div></div>');
	$(querySelector).after('<div id="ruchWidgetButtonContainer"><button class="ruch_sel_point_button" type="button" onclick="ruchDisplayMap()">Wybierz punkt odbioru</button><div style="width: 100%; text-align: left;">' + ruch_html_info_popup + '</div></div>');
	set_selected_pkt_from_cache();
	$('#btn_place_order').prop( "disabled", true );
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

	if(ruch) start_widget('.ruch-widget-empty-container', cod, [cena, cena, cena, cena]);
}

function testElementDeliveryOption() {
    if(document.getElementById('onepagecheckoutps'))
        return '#onepagecheckoutps_step_two';

	if(document.getElementById('order-opc'))
		return '#carrier_area';

	return '.delivery_options_address';
}

function ruchSelectWidgetMode() {
	if (ruch_display_map_as_popup)
		testRuchServ16_popup();
	else
		testRuchServ16();
}

function ruchRegisterCarrierEvent() {

	ruchSelectWidgetMode();

	$('input.delivery_option_radio').live('click', function() {
		ruchSelectWidgetMode();
	});

	$('form[name=carrier_area]').submit(function(){
        return testPkt16();
	});

    $('#opc_payment_methods-content').live('click', function() {
        return testPkt16();
    });
    
    $('#buttons_footer_review').live('click', function() {
        return testPkt16();
    });
    
    $( document ).ajaxComplete(function() {
        $('#btn_place_order').prop( "disabled", false );
        var val = $(ruch_selector_for_service).val();
        if(!val) return;
        if (!ruch_serv.includes(parseInt($(ruch_selector_for_service).val()))) return;
        if (!ruch_selpkt) $('#btn_place_order').prop( "disabled", true );
    });

}

function ruchDetectionCarrierStop(ruch_detection_carrier_interval_id) {
	clearInterval(ruch_detection_carrier_interval_id);
}

function ruchDetectionCarrierAsync() {
	var ruch_detection_carrier_interval_id = setInterval(function () {
		if($('input.delivery_option_radio').length > 0)
		{
			ruchRegisterCarrierEvent();
			ruchDetectionCarrierStop(ruch_detection_carrier_interval_id);
		}
	}, ruch_detection_carrier_interval);

	setTimeout(function () {
		ruchDetectionCarrierStop(ruch_detection_carrier_interval_id);
	}.bind(ruch_detection_carrier_interval_id), ruch_detection_carrier_timeout);
}

function ruchDetectionCarrierStart() {
	if (ruch_was_init_call) return;
	ruch_was_init_call = true;
	if (ruch_async_carrier_loaded)
		ruchDetectionCarrierAsync();
	else
		ruchRegisterCarrierEvent();
}

