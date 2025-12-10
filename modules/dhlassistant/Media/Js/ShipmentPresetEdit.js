document.addEventListener("DOMContentLoaded", function(event) {

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
	}
	else
	{
		$('#field_div_SpecialServices_UBEZP_Value').hide();
	}
}
S_CODOptionChange = function()
{
	var val = $('#field_SpecialServices_S_COD').prop('checked');
	if (val)
	{
		$('#field_div_SpecialServices_COD_Value').show();
	}
	else
	{
		$('#field_div_SpecialServices_COD_Value').hide();
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
		$('#field_SpecialServices_S_ROD').prop('checked', false);
		$('#field_SpecialServices_S_ROD').prop('disabled', true);
		S_RODOptionChange();
	}
	else
	{
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
		if (val == 'PACKAGE')
		{
			$('#field_div_Package_NonStandard').show();
		}
		else
		{
			$('#field_div_Package_NonStandard').show();
		}
		if (val == 'PALLET')
		{
			$('#field_div_Package_EuroReturn').show();
			$('#field_div_Package_NonStandard').show();
		}
		else
		{
			$('#field_div_Package_EuroReturn').hide();
			$('#field_Package_EuroReturn').prop('checked', false);
		}
	}
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
	S_1722OptionChange();
	S_SOBOTAOptionChange();
	S_UBEZPOptionChange();
	S_CODOptionChange();
	S_RODOptionChange();
	S_SASOptionChange();
	S_ODBOptionChange();
	S_UTILOptionChange();
}
/*READY*/
$(function(){
	BindAndFireAllSpecialServices();
	$('#field_Package_Type').bind('change', PackageTypeChange);
	PackageTypeChange();
	});
});