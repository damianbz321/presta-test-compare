document.addEventListener("DOMContentLoaded", function(event) {
	MapPointSelectListener = function(msg)
	{
		var point = JSON.parse(msg.data);
		$('#dhlassistant_parcel_ident_value').val(point.sap);
		$('#dhlassistant_parcel_postal_code_value').val(point.zip);
	}

	SendToParcelShopChange = function()
	{

		$('#dhlassistant_ps_checkbox').parent().parent().removeClass('disabled');
		var val = $('#dhlassistant_ps_checkbox').prop('checked');
		if (val)
		{
			if (dhlassistant_require_postalcode_for_ps)
			{
				$('#dhlassistant_parcel_postal_code .label').html('Kod pocztowy Parcelshop:');
				$('#dhlassistant_parcel_postal_code').show();
			}
			else
			{
				$('#dhlassistant_parcel_postal_code').hide();
			}
			$('#dhlassistant_pl_checkbox').prop('checked', false);
			$('#dhlassistant_pl_checkbox').prop('disabled', true);
			SendToParcelLockerChange();
			$('#dhlassistant_parcel_ident').show();
			if (true)
			{
				$('#dhlassistant_map iframe').attr('src', dhlassistant_map_for_ps_url);
				$('#dhlassistant_map').show();
			}
		}
		else
		{
			if (!$('#dhlassistant_pl_checkbox').prop('checked'))
			{
				$('#dhlassistant_map').hide();
				$('#dhlassistant_parcel_ident').hide();
				$('#dhlassistant_parcel_postal_code').hide();
			}
			$('#dhlassistant_pl_checkbox').prop('disabled', false);
			$('#dhlassistant_pl_checkbox').parent().parent().removeClass('disabled');
		}
	}
	SendToParcelLockerChange = function()
	{
		$('#dhlassistant_pl_checkbox').parent().parent().removeClass('disabled');
		var val = $('#dhlassistant_pl_checkbox').prop('checked');
		if (val)
		{
			if (dhlassistant_require_postnummer_for_pl)
			{
				$('#dhlassistant_postnummer').show();
			}
			else
			{
				$('#dhlassistant_postnummer').hide();
			}
			if (dhlassistant_require_postalcode_for_pl)
			{
				$('#dhlassistant_parcel_postal_code .label').html('Kod pocztowy Parcelstation:');
				$('#dhlassistant_parcel_postal_code').show();
			}
			else
			{
				$('#dhlassistant_parcel_postal_code').hide();
			}
			$('#dhlassistant_ps_checkbox').prop('checked', false);
			$('#dhlassistant_ps_checkbox').prop('disabled', true);
			SendToParcelShopChange();
			//$('#dhlassistant_ps_checkbox').parent().parent().addClass('disabled');
			$('#dhlassistant_parcel_ident').show();
			if (true)
			{
				$('#dhlassistant_map iframe').attr('src', dhlassistant_map_for_pl_url);
				$('#dhlassistant_map').show();
			}
		}
		else
		{
			if (!$('#dhlassistant_ps_checkbox').prop('checked'))
			{
				$('#dhlassistant_map').hide();
				$('#dhlassistant_parcel_ident').hide();
				$('#dhlassistant_parcel_postal_code').hide();
			}
			$('#dhlassistant_ps_checkbox').prop('disabled', false);
			$('#dhlassistant_ps_checkbox').parent().parent().removeClass('disabled');
			$('#dhlassistant_postnummer').hide();
		}
	}
	SaveDhlCarrierOptions = function()
	{

		$('#dhlassistant_messages .success').hide();
		$('#dhlassistant_messages .error').hide();
		$('#HOOK_PAYMENT').html('');

		var ajax_data = {};
		var val_ps = $('#dhlassistant_ps_checkbox').prop('checked');
		var val_pl = $('#dhlassistant_pl_checkbox').prop('checked');
		var val_parcel_ident = $('#dhlassistant_parcel_ident_value').val();
		var val_postnummer = $('#dhlassistant_postnummer_value').val();
		var val_parcel_postal_code = $('#dhlassistant_parcel_postal_code_value').val();

		if (val_ps)
			ajax_data.ParcelShop = val_ps;
		if (val_pl)
			ajax_data.ParcelLocker = val_pl;
		if (val_parcel_ident)
			ajax_data.ParcelIdent = val_parcel_ident;
		if (val_postnummer)
			ajax_data.Postnummer = val_postnummer;
		if (val_parcel_postal_code)
			ajax_data.ParcelPostalCode = val_parcel_postal_code;

		$.ajax({
			type: "POST",
			headers: { "cache-control": "no-cache" },
			url: dhlassistant_ajax_catcher_url,
			data: ajax_data,
			context: document.body,
			dataType : "json",
			success: function(message)
			{
				if (message == 'Success')
					$('#dhlassistant_messages .success').slideDown('slow');
				else
					$('#dhlassistant_messages .error').slideDown('slow');
				if (typeof(updatePaymentMethodsDisplay) !== "undefined")
				{
					$('#uniform-cgv').parent().show();
					if ($('#cgv').prop('checked'))
						updatePaymentMethodsDisplay();
				}
			},
			error: function()
			{
				$('#dhlassistant_messages .error').slideDown('slow');
			}
		});
	}
	DhlOptionsInit = function(){
		dhlassistant_ajax_catcher_url = $("input[name='dhlassistant_ajax_catcher_url']").val();
		dhlassistant_carrier_id = $("input[name='dhlassistant_carrier_id']").val();
		dhlassistant_is_ps_available = !!+$("input[name='dhlassistant_is_ps_available']").val();
		dhlassistant_is_ps_only_service = !!+$("input[name='dhlassistant_is_ps_only_service']").val();
		dhlassistant_is_pl_available = !!+$("input[name='dhlassistant_is_pl_available']").val();
		dhlassistant_is_map_for_parcel_available = !!+$("input[name='dhlassistant_is_map_for_parcel_available']").val();
		dhlassistant_map_for_ps_url = $("input[name='dhlassistant_map_for_ps_url']").val();
		dhlassistant_map_for_pl_url = $("input[name='dhlassistant_map_for_pl_url']").val();
		dhlassistant_require_postnummer_for_pl = !!+$("input[name='dhlassistant_require_postnummer_for_pl']").val();
		dhlassistant_require_postalcode_for_ps = !!+$("input[name='dhlassistant_require_postalcode_for_ps']").val();
		dhlassistant_require_postalcode_for_pl = !!+$("input[name='dhlassistant_require_postalcode_for_pl']").val();

		dhlassistant_ok = ($('#delivery_option_' + dhlassistant_carrier_id).val() == (dhlassistant_carrier_id+','));
		if (dhlassistant_ok)
		{
			if (dhlassistant_is_ps_only_service) /* jeśli musi być podany nr. PS aby przejść dalej*/
			{
				$('#uniform-cgv').parent().hide();
				$('#HOOK_PAYMENT').html('');
			}
			if ((dhlassistant_is_ps_available || dhlassistant_is_pl_available) && dhlassistant_is_map_for_parcel_available)
			{
				if (window.addEventListener)
				{
					window.addEventListener("message", MapPointSelectListener, false);
				}
				else
				{
					window.attachEvent("onmessage", MapPointSelectListener);
				}
			}
			SendToParcelShopChange();
			SendToParcelLockerChange();
			$('#dhlassistant_ps_checkbox').bind('change', SendToParcelShopChange);
			$('#dhlassistant_pl_checkbox').bind('change', SendToParcelLockerChange);
			$('#dhlassistant').show();
		}
	};
	/* init */
	if (document.readyState !== 'complete')
		$(DhlOptionsInit);
	else
		DhlOptionsInit();

// function $() {
// 	var results = [], element;
// 	for (var i = 0; i < arguments.length; i++) {
// 		element = arguments[i];
// 		if (typeof element == 'string')
// 			element = document.getElementById(Element.extend(element));
// 		results.push(element);
// 	}
//
// 	return results.length < 2 ? results[0] : results;
// }
});