document.addEventListener("DOMContentLoaded", function(event) {

	var selectedCountry = $('#field_Receiver_Address_Country').val();

	if(selectedCountry !== 'IE') {
		$('#field_Receiver_Address_PostalCode').attr('required', 'required');
		$( "#field_div_Receiver_Address_PostalCode" ).children().addClass( "required");

	} else {
		$('#field_Receiver_Address_PostalCode').removeAttr('required', 'required');
		$( "#field_div_Receiver_Address_PostalCode" ).children().removeClass( "required");
	}

	$('#field_Receiver_Address_Country').change(function(){
		if($(this).val() == 'IE'){
			$('#field_Receiver_Address_PostalCode').removeAttr('required', 'required');
			$( "#field_div_Receiver_Address_PostalCode" ).children().removeClass( "required");
		} else {
			$('#field_Receiver_Address_PostalCode').attr('required', 'required');
			$( "#field_div_Receiver_Address_PostalCode" ).children().addClass( "required");
		}
	});

	var postcodeValidMT = $('#field_Receiver_Address_PostalCode').val().toUpperCase().replace(/ /g,'');
	$('#field_Receiver_Address_PostalCode').val(postcodeValidMT);
	$('#field_Receiver_Address_PostalCode').keyup(function(){
		$(this).val($(this).val().toUpperCase());
	});

	GetDhlUserId = function()
{
	return 1;
}
GetCurrentCountryCode = function()
{
	return $('#field_Receiver_Address_Country').val();
}
GetCurrentServiceCode = function()
{
	return $('#field_ServiceType').val();
}
GetCurrentDCS = function()
{
	return DhlUserCountries[GetDhlUserId()][GetCurrentCountryCode()]['AvailableServices'][GetCurrentServiceCode()];
}
PrepareSelectValueFromEnums = function (Values, Enum)
{
	var result = {};
	$.each(Values, function(key, value) {
		if (typeof Enum[value] != 'undefined')
			result[value] = Enum[value];
	});
	return result;
}
SetSelectValues = function(FieldJqObj, Values, Order)
{
	var curr_value = FieldJqObj.val();
	FieldJqObj.empty();
	if (typeof Order !== 'undefined')
	{
		$.each(Order, function(index, key) {
			if (typeof Values[key] !== 'undefined')
			{
				var option = $("<option></option>").attr("value", key).text(Values[key]);
				if (key == curr_value){
					option.prop('selected', true);
				}
				FieldJqObj.append(option);
			}
		});
	}
	else
	{
		$.each(Values, function(value, description) {
			var option = $("<option></option>").attr("value", value).text(description);
			if (value == curr_value){
				option.prop('selected', true);
			}
			FieldJqObj.append(option);
		});
	}
}
PrepareShipmentPresets = function()
{
	var shipment_presets = {};
	var is_presets = false;
	$.each(ShipmentPresets, function(id, shipment_preset) {
		var dc = DhlUserCountries[GetDhlUserId()][GetCurrentCountryCode()];
		if (typeof dc['AvailableServices'][shipment_preset['ServiceType']] != 'undefined')
		{
			var dcs = dc['AvailableServices'][shipment_preset['ServiceType']];
			var ss_ok = true;
			$.each(DhlEnums['SpecialService'], function(service_code, service_description) {
				var service_name = 'S_'+service_code;
				if (shipment_preset['SpecialServices'][service_name] && !dcs['AvailableSpecialServices'][service_name])
					ss_ok = false;
			});
			if (ss_ok)
			{
				var label_ok = false;
				$.each(dcs['AvailableLabelTypes'], function(index, label_type) {
					if (label_type == shipment_preset['LabelType']){
						label_ok = true;
					}
				});
				if (label_ok)
				{
					var package_ok = false;
					$.each(dcs['AvailablePackageTypes'], function(index, package_type) {
						if (package_type == shipment_preset['Package']['Type'])
							package_ok = true;
					});
					if (package_ok && shipment_preset['Package']['NonStandard'] && !dcs['AllowNst'])
						package_ok = false;
					if (package_ok && shipment_preset['Package']['NonStandard'] && !dcs['AllowNstForParcel'] && ($('#field_SendToParcelShop').prop('checked') || $('#field_SendToParcelLocker').prop('checked')))
						package_ok = false;
					if (package_ok && shipment_preset['Package']['EuroReturn'] && !dcs['AllowEuroReturn'])
						package_ok = false;
					if (package_ok)
					{
						shipment_presets[id] = shipment_preset['Name'];
						is_presets = true;
					}
				}
			}
		}
	});
	if (is_presets)
	{
		SetSelectValues($('#field_ShipmentPreset_ShipmentPresetId'), shipment_presets);
		$('#section_div_ShipmentPreset_Start').show();
	}
	else
	{
		$('#section_div_ShipmentPreset_Start').hide();
	}
}
SetAddressParsingAlert = function()
{
	var bg_color = '#eab3b7';
	$('#field_Receiver_Address_Street').css('backgroundColor', bg_color);
	$('#field_Receiver_Address_HouseNumber').css('backgroundColor', bg_color);
	$('#field_Receiver_Address_ApartmentNumber').css('backgroundColor', bg_color);

	$('#field_Receiver_Address_Street, #field_Receiver_Address_HouseNumber, #field_Receiver_Address_ApartmentNumber').bind('change', function(){
		if ($('#field_Receiver_Address_Street').val() && $('#field_Receiver_Address_HouseNumber').val())
			ClearAddressParsingAlert();
	});
}
ClearAddressParsingAlert = function()
{
	$('#field_Receiver_Address_Street').css('backgroundColor', '');
	$('#field_Receiver_Address_HouseNumber').css('backgroundColor', '');
	$('#field_Receiver_Address_ApartmentNumber').css('backgroundColor', '');
}
SetCodCurrencyAlert = function()
{
	var bg_color = '#eab3b7';
	$('#field_SpecialServices_COD_Value').css('backgroundColor', bg_color);
	$('.currency_code.country').css('backgroundColor', bg_color);
	$('#field_SpecialServices_COD_Value').bind('change', function(){
		if ($('#field_SpecialServices_COD_Value').val())
			ClearCodCurrencyAlert();
	});
}
ClearCodCurrencyAlert = function()
{
	$('#field_SpecialServices_COD_Value').css('backgroundColor', '');
	$('.currency_code.country').css('backgroundColor', '');

}
S_1722OptionChange = function()
{
	var val = $('#field_SpecialServices_S_1722').prop('checked');
	if (val)
	{
		$('#field_SpecialServices_S_SOBOTA').prop('checked', false);
		$('#field_SpecialServices_S_SOBOTA').prop('disabled', true);
	}
	else
	{
		$('#field_SpecialServices_S_SOBOTA').prop('disabled', false);
	}
}
S_SOBOTAOptionChange = function()
{
	var val = $('#field_SpecialServices_S_SOBOTA').prop('checked');
	if (val)
	{
		$('#field_SpecialServices_S_1722').prop('checked', false);
		$('#field_SpecialServices_S_1722').prop('disabled', true);
	}
	else
	{
		$('#field_SpecialServices_S_1722').prop('disabled', false);
	}
}
S_UBEZPOptionChange = function()
{


	var val = $('#field_SpecialServices_S_UBEZP').prop('checked');
	if (val)
	{
		$('#field_div_SpecialServices_UBEZP_Value').show();
		if ($('#field_SpecialServices_OriginalUBEZPValue').text())
			$('#field_div_SpecialServices_OriginalUBEZPValue').show();
		else
			$('#field_div_SpecialServices_OriginalUBEZPValue').hide();
	}
	else
	{
		$('#field_div_SpecialServices_UBEZP_Value').hide();
		if (GetCurrentDCS()['CodRequireUbezp'])
		{
			$('#field_SpecialServices_S_COD').prop('checked', false);
			S_CODOptionChange();
		}
		$('#field_div_SpecialServices_OriginalUBEZPValue').hide();
	}
}
S_CODOptionChange = function()
{
	var val = $('#field_SpecialServices_S_COD').prop('checked');
	if (val)
	{
		$('#field_div_SpecialServices_COD_Value').show();
		if (GetCurrentDCS()['CodRequireUbezp'])
		{
			$('#field_SpecialServices_S_UBEZP').prop('checked', true);
			S_UBEZPOptionChange();
		}
		if ($('#field_SpecialServices_OriginalCODValue').text())
			$('#field_div_SpecialServices_OriginalCODValue').show();
		else
			$('#field_div_SpecialServices_OriginalCODValue').hide();
	}
	else
	{
		$('#field_div_SpecialServices_COD_Value').hide();
		$('#field_div_SpecialServices_OriginalCODValue').hide();
	}
}
S_RODOptionChange = function()
{
	var val = $('#field_SpecialServices_S_ROD').prop('checked');
	if (val)
	{
		$('#field_div_SpecialServices_ROD_Instruction').show();
		$('#field_SpecialServices_S_SAS').prop('checked', false);
		$('#field_SpecialServices_S_SAS').prop('disabled', true);
		S_SASOptionChange();
	}
	else
	{
		$('#field_div_SpecialServices_ROD_Instruction').hide();
		$('#field_SpecialServices_S_SAS').prop('disabled', false);
	}
}
S_SASOptionChange = function()
{
	var val = $('#field_SpecialServices_S_SAS').prop('checked');
	if (val)
	{
		$('#section_div_Neighbour_Start').show();
		$('#field_SpecialServices_S_ROD').prop('checked', false);
		$('#field_SpecialServices_S_ROD').prop('disabled', true);
		S_RODOptionChange();
	}
	else
	{
		$('#section_div_Neighbour_Start').hide();
		$('#field_SpecialServices_S_ROD').prop('disabled', false);
	}
}
S_ODBOptionChange = function()
{
	var val = $('#field_SpecialServices_S_ODB').prop('checked');
	if (val)
	{
		$('#field_SpecialServices_S_UTIL').prop('checked', false);
		$('#field_SpecialServices_S_UTIL').prop('disabled', true);
		S_UTILOptionChange();
	}
	else
	{
		$('#field_SpecialServices_S_UTIL').prop('disabled', false);
	}
}
S_UTILOptionChange = function()
{
	var val = $('#field_SpecialServices_S_UTIL').prop('checked');
	if (val)
	{
		$('#field_SpecialServices_S_ODB').prop('checked', false);
		$('#field_SpecialServices_S_ODB').prop('disabled', true);
		S_ODBOptionChange();
	}
	else
	{
		$('#field_SpecialServices_S_ODB').prop('disabled', false);
	}
}
SendToParcelShopChange = function()
{
	var val = $('#field_SendToParcelShop').prop('checked');
	if (val)
	{
		$('#field_div_ParcelIdent .field_name').text('Nr. Parcelshop:');
		if (GetCurrentDCS()['RequirePostalCodeForParcel'])
		{
			$('#field_div_ParcelPostalCode .field_name').text('Kod pocztowy Parcelshop:');
			$('#field_div_ParcelPostalCode').show();
		}
		else
		{
			$('#field_div_ParcelPostalCode').hide();
		}
		$('#field_SendToParcelLocker').prop('checked', false);
		$('#field_SendToParcelLocker').prop('disabled', true);
		SendToParcelLockerChange();
		if (!GetCurrentDCS()['AllowCodForParcelShop'])
		{
			$('#field_SpecialServices_S_COD').prop('checked', false);
			$('#field_div_SpecialServices_S_COD').hide();
			S_CODOptionChange();
		}
		$('#field_div_ParcelIdent').show();
	}
	else
	{
		if (!$('#field_SendToParcelLocker').prop('checked'))
		{
			$('#field_div_ParcelIdent').hide();
			if (GetCurrentDCS()['AvailableSpecialServices']['S_COD'])
			{
				$('#field_div_SpecialServices_S_COD').show();
			}
			$('#field_div_ParcelPostalCode').hide();
		}
		$('#field_SendToParcelLocker').prop('disabled', false);
	}
	PackageTypeChange();
	PrepareShipmentPresets();
}
SendToParcelLockerChange = function()
{
	var val = $('#field_SendToParcelLocker').prop('checked');
	if (val)
	{
		$('#field_div_ParcelIdent .field_name').text('Nr. Parcelstation:');
		if (GetCurrentDCS()['RequirePostnummerForParcelLocker'])
		{
			$('#field_div_Postnummer').show();
		}
		else
		{
			$('#field_div_Postnummer').hide();
		}
		if (GetCurrentDCS()['RequirePostalCodeForParcel'])
		{
			$('#field_div_ParcelPostalCode .field_name').text('Kod pocztowy Parcelstation:');
			$('#field_div_ParcelPostalCode').show();
		}
		else
		{
			$('#field_div_ParcelPostalCode').hide();
		}
		$('#field_SendToParcelShop').prop('checked', false);
		$('#field_SendToParcelShop').prop('disabled', true);
		SendToParcelShopChange();
		if (!GetCurrentDCS()['AllowCodForParcelLocker'])
		{
			$('#field_SpecialServices_S_COD').prop('checked', false);
			$('#field_div_SpecialServices_S_COD').hide();
			S_CODOptionChange();
		}
		$('#field_div_ParcelIdent').show();
	}
	else
	{
		if (!$('#field_SendToParcelShop').prop('checked'))
		{
			$('#field_div_ParcelIdent').hide();
			if (GetCurrentDCS()['AvailableSpecialServices']['S_COD'])
			{
				$('#field_div_SpecialServices_S_COD').show();
			}
			$('#field_div_ParcelPostalCode').hide();
		}
		$('#field_SendToParcelShop').prop('disabled', false);
		$('#field_div_Postnummer').hide();
	}
	PackageTypeChange();
	PrepareShipmentPresets();
}
DropOffTypeChange = function()
{
	if ($('#field_DropOffType').val() == 'REGULAR_PICKUP')
	{
		$('#field_div_ShipmentStartHour').hide();
		$('#field_div_ShipmentEndHour').hide();
	}
	else
	{
		$('#field_div_ShipmentStartHour').show();
		$('#field_div_ShipmentEndHour').show();
	}
}

PackageTypeChange = function()
{
	var val = $('#field_Package_Type').val()
	if (val == 'ENVELOPE')
	{
		$('#field_div_Package_Weight').hide();
		$('#field_div_Package_Width').hide();
		$('#field_div_Package_Height').hide();
		$('#field_div_Package_Length').hide();
		$('#field_div_Package_NonStandard').hide();
		$('#field_div_Package_EuroReturn').hide();
		$('#field_Package_NonStandard').prop('checked', false);
		$('#field_Package_EuroReturn').prop('checked', false);
	}
	else
	{
		$('#field_div_Package_Weight').show();
		$('#field_div_Package_Width').show();
		$('#field_div_Package_Height').show();
		$('#field_div_Package_Length').show();
		if ((val == 'PACKAGE' && GetCurrentDCS()['AllowNst']) && (GetCurrentDCS()['AllowNstForParcel'] || !($('#field_SendToParcelShop').prop('checked') || $('#field_SendToParcelLocker').prop('checked'))))
			$('#field_div_Package_NonStandard').show();
		else
		{
			$('#field_div_Package_NonStandard').hide();
		}
		if (val == 'PALLET')
		{
			if (GetCurrentDCS()['AllowEuroReturn']) {
				$('#field_div_Package_EuroReturn').show();
			} else {
				$('#field_div_Package_EuroReturn').hide();
				$('#field_Package_EuroReturn').prop('checked', false);
			}

			$('#field_div_Package_NonStandard').show();
		}
		else
		{
			$('#field_div_Package_NonStandard').show();
			$('#field_div_Package_EuroReturn').hide();
			$('#field_Package_EuroReturn').prop('checked', false);
		}
	}
}
ShippingPaymentTypeChange = function()
{
	var val = $('#field_ShippingPaymentType').val();
	SetSelectValues($('#field_PaymentType'), PrepareSelectValueFromEnums(GetCurrentDCS()['AvailableShippingPaymentTypes'][val], DhlEnums['PaymentType']));
	if (val == 'USER')
	{
		$('#field_div_SenderName').show();
		$('#field_div_SenderCompany').show();
		$('#field_div_SenderStreet').show();
		$('#field_div_SenderHouseNumber').show();
		$('#field_div_SenderApartmentNumber').show();
		$('#field_div_SenderPostalCode').show();
		$('#field_div_SenderCity').show();
		$('#field_div_SenderPhone').show();
		$('#field_div_SenderEmail').show();
	}
	else
	{
		$('#field_div_SenderName').hide();
		$('#field_div_SenderCompany').hide();
		$('#field_div_SenderStreet').hide();
		$('#field_div_SenderHouseNumber').hide();
		$('#field_div_SenderApartmentNumber').hide();
		$('#field_div_SenderPostalCode').hide();
		$('#field_div_SenderCity').hide();
		$('#field_div_SenderPhone').hide();
		$('#field_div_SenderEmail').hide();
	}
}
ServiceTypeChange = function()
{
	$('#field_DropOffType').unbind('change');
	$('#field_Package_Type').unbind('change');
	$('#field_ShippingPaymentType').unbind('change');

	SetSelectValues($('#field_LabelType'), PrepareSelectValueFromEnums(GetCurrentDCS()['AvailableLabelTypes'], DhlEnums['LabelType']));

	shipping_payment_types = [];
	$.each(GetCurrentDCS()['AvailableShippingPaymentTypes'], function(shipping_payment_type, payment_types) {
		shipping_payment_types.push(shipping_payment_type);
	});
	SetSelectValues($('#field_ShippingPaymentType'), PrepareSelectValueFromEnums(shipping_payment_types, DhlEnums['ShippingPaymentType']));
	ShippingPaymentTypeChange();

	DropOffTypeChange();

	SetSelectValues($('#field_Package_Type'), PrepareSelectValueFromEnums(GetCurrentDCS()['AvailablePackageTypes'], DhlEnums['PackageType']));
	PackageTypeChange();

	var special_services = GetCurrentDCS()['AvailableSpecialServices'];
	if (special_services['S_1722'])
		$('#field_div_SpecialServices_S_1722').show();
	else
	{
		$('#field_SpecialServices_S_1722').prop('checked', false);
		$('#field_div_SpecialServices_S_1722').hide();
	}
	S_1722OptionChange();
	if (special_services['S_SOBOTA'])
		$('#field_div_SpecialServices_S_SOBOTA').show();
	else
	{
		$('#field_SpecialServices_S_SOBOTA').prop('checked', false);
		$('#field_div_SpecialServices_S_SOBOTA').hide();
	}
	S_SOBOTAOptionChange();
	if (special_services['S_NAD_SOBOTA'])
		$('#field_div_SpecialServices_S_NAD_SOBOTA').show();
	else
	{
		$('#field_SpecialServices_S_NAD_SOBOTA').prop('checked', false);
		$('#field_div_SpecialServices_S_NAD_SOBOTA').hide();
	}
	if (special_services['S_UBEZP'])
		$('#field_div_SpecialServices_S_UBEZP').show();
	else
	{
		$('#field_SpecialServices_S_UBEZP').prop('checked', false);
		$('#field_div_SpecialServices_S_UBEZP').hide();
	}
	S_UBEZPOptionChange();
	if (special_services['S_COD'])
		$('#field_div_SpecialServices_S_COD').show();
	else
	{
		$('#field_SpecialServices_S_COD').prop('checked', false);
		$('#field_div_SpecialServices_S_COD').hide();
	}
	S_CODOptionChange();
	if (special_services['S_PDI'])
		$('#field_div_SpecialServices_S_PDI').show();
	else
	{
		$('#field_SpecialServices_S_PDI').prop('checked', false);
		$('#field_div_SpecialServices_S_PDI').hide();
	}
	if (special_services['S_POD'])
		$('#field_div_SpecialServices_S_POD').show();
	else
	{
		$('#field_SpecialServices_S_POD').prop('checked', false);
		$('#field_div_SpecialServices_S_POD').hide();
	}
	if (special_services['S_ROD'])
		$('#field_div_SpecialServices_S_ROD').show();
	else
	{
		$('#field_SpecialServices_S_ROD').prop('checked', false);
		$('#field_div_SpecialServices_S_ROD').hide();
	}
	S_RODOptionChange();
	if (special_services['S_SAS'])
		$('#field_div_SpecialServices_S_SAS').show();
	else
	{
		$('#field_SpecialServices_S_SAS').prop('checked', false);
		$('#field_div_SpecialServices_S_SAS').hide();
	}
	S_SASOptionChange();
	if (special_services['S_ODB'])
		$('#field_div_SpecialServices_S_ODB').show();
	else
	{
		$('#field_SpecialServices_S_ODB').prop('checked', false);
		$('#field_div_SpecialServices_S_ODB').hide();
	}
	S_ODBOptionChange();
	if (special_services['S_UTIL'])
		$('#field_div_SpecialServices_S_UTIL').show();
	else
	{
		$('#field_SpecialServices_S_UTIL').prop('checked', false);
		$('#field_div_SpecialServices_S_UTIL').hide();
	}
	S_UTILOptionChange();
	if (GetCurrentDCS()['AllowParcelShop'])
	{
		if (GetCurrentDCS()['ParcelShopOnlyService'])
		{
			$('#field_div_SendToParcelShop').hide();
			$('#field_SendToParcelShop').prop('checked', true);
		}
		else
		{
			$('#field_div_SendToParcelShop').show();
		}
	}
	else
	{
		$('#field_SendToParcelShop').prop('checked', false);
		$('#field_div_SendToParcelShop').hide();
	}
	SendToParcelShopChange();
	if (GetCurrentDCS()['AllowParcelLocker'])
	{
		$('#field_div_SendToParcelLocker').show();
	}
	else
	{
		$('#field_SendToParcelLocker').prop('checked', false);
		$('#field_div_SendToParcelLocker').hide();
	}
	SendToParcelLockerChange();

	$('#field_DropOffType').bind('change', DropOffTypeChange);
	$('#field_Package_Type').bind('change', PackageTypeChange);
	$('#field_ShippingPaymentType').bind('change', ShippingPaymentTypeChange);

	PrepareShipmentPresets();

}
ReceiverAddressCountryChange = function()
{
	$('#field_ServiceType').unbind('change');
	var old_currency = $('.currency_code.country').text();
	var new_currency = DhlUserCountries[GetDhlUserId()][GetCurrentCountryCode()]['Currency'];
	$('.currency_code.country').text(new_currency);
	var service_codes = [];
	$.each(DhlUserCountries[GetDhlUserId()][GetCurrentCountryCode()]['AvailableServices'], function(service_code, service) {
		service_codes.push(service_code);
	});
	SetSelectValues($('#field_ServiceType'), PrepareSelectValueFromEnums(service_codes, DhlEnums['ServiceType']), DhlServiceOrder);
	ServiceTypeChange();
	$('#field_ServiceType').bind('change', ServiceTypeChange);

}

BindAndFireAllSpecialServices = function()
{
	$('#field_SpecialServices_S_1722').bind('change', S_1722OptionChange);
	$('#field_SpecialServices_S_SOBOTA').bind('change', S_SOBOTAOptionChange);
	$('#field_SpecialServices_S_UBEZP').bind('change', S_UBEZPOptionChange);
	$('#field_SpecialServices_S_COD').bind('change', S_CODOptionChange);
	$('#field_SpecialServices_S_ROD').bind('change', S_RODOptionChange);
	$('#field_SpecialServices_S_SAS').bind('change', S_SASOptionChange);
	$('#field_SpecialServices_S_ODB').bind('change', S_ODBOptionChange);
	$('#field_SpecialServices_S_UTIL').bind('change', S_UTILOptionChange);
	$('#field_SendToParcelShop').bind('change', SendToParcelShopChange);
	$('#field_SendToParcelLocker').bind('change', SendToParcelLockerChange);
	S_1722OptionChange();
	S_SOBOTAOptionChange();
	S_UBEZPOptionChange();
	S_CODOptionChange();
	S_RODOptionChange();
	S_SASOptionChange();
	S_ODBOptionChange();
	S_UTILOptionChange();
	SendToParcelShopChange();
	SendToParcelLockerChange();
}
HandleAlerts = function()
{
	if ($('#field_Receiver_Address_ParseAlert').val())
	{
		$('#field_Receiver_Address_Street').val($('#field_Receiver_Address_OriginalAddressString').val());
		SetAddressParsingAlert();
	}
}
ShipmentEditViewMode = function()
{
	$('#field_Action_Save, #field_Action_Send, #section_div_ShipmentPreset_Start').hide();
	$('#section_div_ViewMode, #field_Action_CancelShipment, #field_Action_DeleteShipment').show();
	$('input:not([type="text"]), select').prop('disabled', true);
	$('input[type="text"]').prop('readonly', true);
	$.each(url_labels, function(key, value) {
		$('#field_Action_GetLabel_'+key).attr('href', value).show();
	});
	$('#field_Action_CancelShipment').attr('href', url_cancel_shipment);

}

	UpdateCodUbezp = function()
	{
		var selectedCountry = $('#field_Receiver_Address_Country').val();
		$('#currency_disclaimer').remove();
		if(selectedCountry == 'PL') {
			$('#field_div_SpecialServices_UBEZP_Value').children().addClass( "required");
		} else {
			$('#field_div_SpecialServices_UBEZP_Value').children().removeClass( "required");
			$('#field_div_SpecialServices_OriginalCODValue').append( "<p id='currency_disclaimer'>UWAGA: Wartość pobrania musi zostać zadeklarowana zgodnie z walutą używaną w kraju odbiorcy.</p>" );
		}
	}
	UpdateCodUbezp();
	$('#field_Receiver_Address_Country').on("click", UpdateCodUbezp);


/*READY*/
$(function(){
	$('.currency_code.original').text($('#field_SpecialServices_OriginalCurrencyUnit').val());
	ReceiverAddressCountryChange();
	$('#field_Receiver_Address_Country').bind('change', ReceiverAddressCountryChange);
	BindAndFireAllSpecialServices();
	$('#field_Action_DeleteShipment').attr('href', url_delete_shipment);
	HandleAlerts();
	if (shipment_view_mode)
	{
		ShipmentEditViewMode();
	}
	});
});
