/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

var ruch_wid;
var ruch_cod;
var ruch_c;
var ruch_lpay;
function start_widget(el, cod, c) {
    ruch_cod = cod;
    ruch_c = c;
    $(el).after(ruch_html);
	$('#ruch_widget').on('click', function (event) {event.stopPropagation()});

    ruch_wid = new RuchWidget('ruch_widget',
        {
            readyCb: on_ready,
            selectCb: on_select,
            initialAddress: ruch_initial_address,
            baseLink: ruch_baseLink,
            sandbox: ruch_sandbox,
            showCodFilter: ruch_showCodFilter,
            showPointTypeFilter: ruch_showPointTypeFilter
        }
    );
    ruch_wid.init();
}

function testPkt(val) {
	var id = parseInt(val.substr(0, val.length - 1));
    if(ruch_serv.indexOf(id) == -1) return true;

	if (!ruch_selpkt) {
    	if (!!$.prototype.fancybox)
        	$.fancybox.open([
                {
                    type: 'inline',
                    autoScale: true,
                    minHeight: 30,
                    content: '<p class="fancybox-error">' + ruch_msg_nopkt + '</p>'
                }],
                {
                	padding: 0
                }
            );
        else
        {
            if (ruch_async_carrier_loaded)
                alert(ruch_msg_nopkt);
            else
            {
                $('#ruch_validation_message').html('<b style="color: red;">' + ruch_msg_nopkt + '</b>').show();
            }

        }
    	return false;
    }

	if ((ruch_codserv.indexOf(id) != -1) && !ruch_lpay && !ruch_async_carrier_loaded) {
    	if (!!$.prototype.fancybox)
        	$.fancybox.open([
                {
                    type: 'inline',
                    autoScale: true,
                    minHeight: 30,
                    content: '<p class="fancybox-error">' + ruch_msg_nopayment + '</p>'
                }],
                {
                	padding: 0
                }
            );
        else
        {
            if (ruch_async_carrier_loaded)
                alert(ruch_msg_nopayment);
            else
                $('#ruch_validation_message').html('<b style="color: red;">' + ruch_msg_nopayment + '</b>').show();
        }
    	return false;
	}

	if (ruch_tel == '' && !ruch_async_carrier_loaded) {
    	if (!!$.prototype.fancybox)
        	$.fancybox.open([
                {
                    type: 'inline',
                    autoScale: true,
                    minHeight: 30,
                    content: '<p class="fancybox-error">' + ruch_msg_notel + '</p>'
                }],
                {
                	padding: 0
                }
            );
        else
        {
            if (ruch_async_carrier_loaded)
                alert(ruch_msg_notel);
            else
                $('#ruch_validation_message').html('<b style="color: red;">' + ruch_msg_notel + '</b>').show();
        }
    	return false;
	}

	if (!/^(\+48|48|)\d{9}$/.test(ruch_tel) && !ruch_async_carrier_loaded) {
    	if (!!$.prototype.fancybox)
        	$.fancybox.open([
                {
                    type: 'inline',
                    autoScale: true,
                    minHeight: 30,
                    content: '<p class="fancybox-error">' + ruch_msg_btel + '</p>'
                }],
                {
                	padding: 0
                }
            );
        else
        {
            if (ruch_async_carrier_loaded)
                alert(ruch_msg_btel);
            else
                $('#ruch_validation_message').html('<b style="color: red;">' + ruch_msg_btel + '</b>').show();
        }
    	return false;
	}

	if (ruch_adr == 0 && !ruch_async_carrier_loaded) {
    	if (!!$.prototype.fancybox)
        	$.fancybox.open([
                {
                    type: 'inline',
                    autoScale: true,
                    minHeight: 30,
                    content: '<p class="fancybox-error">' + ruch_msg_noadr + '</p>'
                }],
                {
                	padding: 0
                }
            );
        else
        {
            if (ruch_async_carrier_loaded)
                alert(ruch_msg_noadr);
            else
                $('#ruch_validation_message').html('<b style="color: red;">' + ruch_msg_noadr + '</b>').show();
        }
    	return false;
	}

    $('#ruch_validation_message').empty();
	$('#ruch_widget').html();
	return true;
}

function on_ready() {
    ruch_wid.showWidget(
        ruch_cod,
        {
            'R': ruch_c[0],
            'P': ruch_c[1],
            'U': ruch_c[2],
            'A': ruch_c[3]
        },
        {
            'R': 'ruch_' + ruch_cod,
            'P': 'partner_' + ruch_cod,
            'U': 'partner_' + ruch_cod,
            'A': 'orlen_' + ruch_cod
        }
    );
    if (!ruch_display_map_as_popup)
        set_selected_pkt_from_cache();
}

function on_select(pkt) {
    var id, t;

    display_selected_pkt(pkt);
    ajax_selected_pkt(pkt);

    if(pkt != null && ruch_display_map_as_popup) {
        $('.ruch-widget-map-container-full-screen').unbind('click.ruch-remove-widget-map-container-full-screen');
        $('.ruch-widget-map-container-full-screen').remove();
    }
}

function ajax_selected_pkt(pkt) {
    if(pkt != null) {
        id = pkt.id;
        t = pkt.t;
        ruch_lpay = pkt.c;
        cache_selected_pkt(pkt);
    }
    else {
        id = 0;
        t = '';
        ruch_lpay = 0;
    }

    var d = {token: ruch_token, a: 's', pkt: id, typ: t};
    var s = $.ajax({
        url: ruch_ajax_uri,
        type: 'POST',
        dataType: "json",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(d),
        async: false
    }).responseText;
}

function cache_selected_pkt(pkt) {
    if(pkt != null) {
        switch (ruch_check_service(ruch_selector_for_service)) {
            case 'ruch_serv':
                sessionStorage.removeItem('ruchSelectedPktSession');
                sessionStorage.setItem('ruchSelectedPktSession', btoa(unescape(encodeURIComponent(JSON.stringify(pkt)))));
                break;
            case 'ruch_codserv':
                sessionStorage.removeItem('ruchSelectedPktCodSession');
                sessionStorage.setItem('ruchSelectedPktCodSession', btoa(unescape(encodeURIComponent(JSON.stringify(pkt)))));
                break;
        }
    }
}

function set_selected_pkt_from_cache() {
    var sessionValue;

    switch (ruch_check_service(ruch_selector_for_service)) {
        case 'ruch_serv':
            sessionValue = sessionStorage.getItem('ruchSelectedPktSession');
            break;
        case 'ruch_codserv':
            sessionValue = sessionStorage.getItem('ruchSelectedPktCodSession');
            break;
    }

    if (sessionValue == null) return;

    var selected_pkt = JSON.parse(decodeURIComponent(escape(atob(sessionValue))));
    if (selected_pkt != null)
    {
        ruch_lpay = selected_pkt.c;
        display_selected_pkt(selected_pkt);
        ajax_selected_pkt(selected_pkt);
    }
}

function display_selected_pkt(pkt) {

    if(pkt != null) {
        ruch_selpkt = pkt.id;
        $('#ruch_selpkt_desc').html('<b>Wybrany punkt: ' + pkt.a + '</b>');
        $('#ruch_selpkt_desc').show();
    }
}


function ruch_check_service(selector) {
    var val = $(selector).val();
    if(!val) return;
    var id = parseInt(val.substr(0, val.length - 1));
    if(ruch_codserv.indexOf(id) != -1) {
        return 'ruch_codserv'
    }
    else if(ruch_serv.indexOf(id) != -1) {
        return 'ruch_serv'
    }
}
