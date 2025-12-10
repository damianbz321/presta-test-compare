/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */

	$(document).ready(function(){
	
	   if (typeof ruch_defaults === 'undefined') return;

		$('#ruch_form_div').slideUp();
		addPackRuch(false);
		ruchPutData();

		$("#ruch_paczki_add").click(function() {
			addPackRuch(true);
		});
	
		$('#ruch_edit_label').on('click', function() {
			$('#ruch_form_div').slideDown();
		});

		$('#ruch_create_label').on('click', function() {
			$('#ajax_running').slideDown();
			$('#ruch_msg_container').slideUp().html('');
		
			$.ajax({
				type: "POST",
				async: true,
				url: ruch_ajax_uri,
				dataType: "json",
				global: false,
	            data: JSON.stringify({
	            	"action": "newLabel",
	            	"token": ruch_token,
	            	"id_order": ruch_order_id
	            }),
				success: function(resp)
				{
					if (resp.error)
					{
						$('#ruch_msg_container').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
						$.scrollTo('#ruch', 400, { offset: { top: -100 }});
					}
					else
					{
						ruch_data = resp.data;
						ruchPutData();
						$('.shipping_number_show').html(ruch_data.api_parcel_id);
						ruchDoDownload(ruch_data.id_label);
					}

					$('#ajax_running').slideUp();
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					if(jqXHR.status == 0) alert("Nieprawidłowa domena");
					$('#ajax_running').slideUp();
				}
			});
		});

		$('#ruch_label').on('click', function() {
			if(!testRuchLpacz()) return;
			$('#ajax_running').slideDown();
			$('#ruch_msg_container').slideUp().html('');
			ruchGetData();
		
	        $.ajax({
	            type: "POST",
	            async: true,
	            url: ruch_ajax_uri,
	            dataType: "json",
	            global: false,
	            contentType: "application/json; charset=utf-8",
	            data: JSON.stringify({
	            	"action": "createLabel",
	            	"token": ruch_token,
	            	"data": ruch_data 
	            }),
	            success: function(resp)
	            {
					if (resp.error)
					{
						$('#ruch_msg_container').hide().html('<p class="error alert alert-danger">'+resp.error+'</p>').slideDown();
						scrollTo('ruch');
					}
					else
					{
						ruch_data = resp.data;
						ruchPutData();
						$('.shipping_number_show').html(ruch_data.api_parcel_id);
						ruchDoDownload(ruch_data.id_label);
					}

	                $('#ajax_running').slideUp();
	            },
	            error: function(jqXHR, textStatus, errorThrown)
	            {
	            	if(jqXHR.status == 0) alert("Nieprawidłowa domena");
	                $('#ajax_running').slideUp();
	            }
	        });
		});		

		$('#ruch_print_label').on('click', function() {
			$('#ruch_msg_container').slideUp().html('');
			ruchDoDownload(ruch_data.id_label);
		});

	});

function ruchDoDownload(id_label) {
	link = ruch_pdf_uri + '?printLabel=true&id_label=' + id_label + '&token=' + encodeURIComponent(ruch_token);
	ifr = window.document.getElementById('ruch_down');
	ifr.src = link;
	return true;
}

var paczki_ruch = new Array();
var ruch_limitp = 10;
function addPackRuch(slide) {
	var lpacz = 0;
	for(var i = 0; i < paczki_ruch.length; i++) if(paczki_ruch[i]) lpacz++;
	if(lpacz >= 10) {
		alert("Limit wynosi 10 paczek");
		return;
	}
	var num = paczki_ruch.length + 1;
	paczki_ruch.push(1);
    var td1 = '<td><input type="text" id="ruch_form_waga_' + num + '" name="ruch_form_waga" value="' + ruch_defaults.waga + '" maxlength="5" size="5"></td>';
    var td2 = '<td class="form_dim"><input type="text" id="ruch_form_wys_' + num + '" name="ruch_form_wys" value="' + ruch_defaults.wys + '" maxlength="6" size="6"></td>';
    var td3 = '<td class="form_dim"><input type="text" id="ruch_form_dlug_' + num + '" name="ruch_form_dlug" value="' + ruch_defaults.dlug + '" maxlength="6" size="6"></td>';
    var td4 = '<td class="form_dim"><input type="text" id="ruch_form_szer_' + num + '" name="ruch_form_szer" value="' + ruch_defaults.szer + '" maxlength="6" size="6"></td>';
    var td5 = '<td class="form_tpl"><select id="ruch_form_tpl_' + num + '" name="ruch_form_tpl"><option value="S">Gabaryt S</option><option value="M">Gabaryt M</option><option value="L">Gabaryt L</option></select></td>';
    var td6 = '<td><input type="checkbox" id="ruch_form_nst_' + num + '" name="ruch_form_nst" ' + ruch_defaults.nst + '></td>';
    var td7 = '<td><img src="' + ruch_img_uri + '/delete_16.png" class="ruch_paczki_del" style="cursor: pointer;"/><div class="ruch_paczki_num" style="display: none;">' + num + '</div></td>';

	$('#ruch_paczki tbody').append('<tr id="ruch_paczki_tr_' + num + '" style="display: none;">' + td1 + td2 + td3 + td4 + td5 + td6 + td7 + '</tr>');
	$("#ruch_form_tpl_" + num + " option[value='" + ruch_defaults.tpl + "']").attr('selected', 'selected');
	
	if(slide) {
		$("#ruch_paczki_tr_" + num).slideDown();
	}
	else {
		$("#ruch_paczki_tr_" + num).show();
	}
	$(".ruch_paczki_del").click(function() {
		var lpacz = 0;
		for(var i = 0; i < paczki_ruch.length; i++) if(paczki_ruch[i]) lpacz++;
		if(lpacz <= 1) return;
		var num = $(".ruch_paczki_num", $(this).parent()).html();
		paczki_ruch[num - 1] = 0;
		lpacz--;
		$("#ruch_paczki_tr_" + num).slideUp();
	});
}

function ruchPutData() {
	if(!ruch_data) {
		$('#ruch_status').html('Nie');
		return;
	}

    var tmp = ruch_data.target_point.split(':');
    ruch_typ = tmp[0];
    ruch_point = tmp[1];
    ruch_serv = tmp[2];
    if(!ruch_point) {
        $('#ruch_edit_label').hide();
        $('#ruch_create_label').hide();
        return;
    }

    if(ruch_typ != 'A') {
        $('#ruch_forms1').show();
        $("#ruch_form_serv option[value='" + ruch_serv + "']").attr('selected', 'selected');
        $('.form_dim').show();
        $('.form_tpl').hide();
    }
    else {
        $('.form_tpl').show();
        $('.form_dim').hide();
    }

	if(ruch_data.api_parcel_id) {
		var track = ruch_track_url.replace('@', ruch_data.api_parcel_id);
		$('#ruch_print_label').show();
		$('#ruch_status').html('Tak');
		$('#ruch_track_url').html('<a href="' + track + '">' + ruch_data.api_parcel_id + '</a>');
	}

	$('#ruch_form_nazwa').val(ruch_data.odb_naz);
	$('#ruch_form_adres1').val(ruch_data.odb_adr1);
	$('#ruch_form_adres2').val(ruch_data.odb_adr2);
	$('#ruch_form_kod').val(ruch_data.odb_kod);
	$('#ruch_form_miasto').val(ruch_data.odb_miasto);
	$('#ruch_form_kraj').val(ruch_data.odb_kraj);
	$('#ruch_form_imie').val(ruch_data.odb_osoba_imie);
	$('#ruch_form_nazwisko').val(ruch_data.odb_osoba_nazw);
	$('#ruch_form_tel').val(ruch_data.odb_tel);
	$('#ruch_form_email').val(ruch_data.odb_mail);
	
	$('#ruch_form_kwota').val(ruch_data.cod);
	$('#ruch_form_ubezp').val(ruch_data.ins);
	if($.inArray("email", ruch_data.additional) != -1) $('#ruch_form_mail').prop('checked', 1);
	if($.inArray("sms", ruch_data.additional) != -1) $('#ruch_form_sms').prop('checked', 1);
	
	paczki_ruch = new Array();
	if(!ruch_data.parcels) return;
	for(var i = 0; i < ruch_data.parcels.length; i++) {
		addPackRuch(false);
		var num = i + 1;
		$('#ruch_form_waga_' + num).val(ruch_data.parcels[i].waga);
		$('#ruch_form_wys_' + num).val(ruch_data.parcels[i].wys);
		$('#ruch_form_dlug_' + num).val(ruch_data.parcels[i].dlug);
		$('#ruch_form_szer_' + num).val(ruch_data.parcels[i].szer);
		$('#ruch_form_tpl_' + num + " option[value='" + ruch_data.parcels[i].tpl + "']").attr('selected', 'selected');
		$('#ruch_form_nst_' + num).prop('checked', parseInt(ruch_data.parcels[i].nst));
	}	
}

function ruchGetData() {
	if(ruch_data.pstat == 30) return;
	ruch_data.odb_naz = $('#ruch_form_nazwa').val();
	ruch_data.odb_adr1 = $('#ruch_form_adres1').val();
	ruch_data.odb_adr2 = $('#ruch_form_adres2').val();
	ruch_data.odb_kod = $('#ruch_form_kod').val();
	ruch_data.odb_miasto = $('#ruch_form_miasto').val();
	ruch_data.odb_kraj = $('#ruch_form_kraj').val();
	ruch_data.odb_osoba_imie = $('#ruch_form_imie').val();
	ruch_data.odb_osoba_nazw = $('#ruch_form_nazwisko').val();
	ruch_data.odb_tel = $('#ruch_form_tel').val();
	ruch_data.odb_mail = $('#ruch_form_email').val();

	ruch_data.cod = $('#ruch_form_kwota').val();
	ruch_data.ins = $('#ruch_form_ubezp').val();
	ruch_data.additional = new Array();
	if($('#ruch_form_mail').prop('checked')) ruch_data.additional.push('email');
	if($('#ruch_form_sms').prop('checked')) ruch_data.additional.push('sms');
	
	ruch_data.parcels = new Array();
	for(var i = 0; i < paczki_ruch.length; i++) {
		if(paczki_ruch[i] == 1) {
			var num = i + 1;
			var pak = {
				"waga": $('#ruch_form_waga_' + num).val(),
				"dlug": $('#ruch_form_dlug_' + num).val(),
				"szer": $('#ruch_form_szer_' + num).val(),
				"wys": $('#ruch_form_wys_' + num).val(),
				"nst": ($('#ruch_form_nst_' + num).prop("checked") ? '1' : '0'),
				"tpl": $('#ruch_form_tpl_' + num).val(),
			}
			ruch_data.parcels.push(pak);
		}
	}
	ruch_serv = $("#ruch_form_serv").val();
	ruch_data.target_point = ruch_typ + ':' + ruch_point + ':' + ruch_serv;
}

function testRuchLpacz() {
	var lpacz = getRuchLpacz();
	if(lpacz > ruch_limitp) {
		alert("Maksymalna liczba paczek dla wybranej usługi wynosi " + ruch_limitp);
		return false;
	}
	return true;
}

function getRuchLpacz() {
	var lpacz = 0;
	for(var i = 0; i < paczki_ruch.length; i++) if(paczki_ruch[i]) lpacz++;
	return lpacz;
}
