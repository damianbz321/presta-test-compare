document.addEventListener("DOMContentLoaded", function(event) {

	$("#field_LabelType option[value='LP']").remove();
	$("#field_ShipperPreset_LabelType option[value='LP']").remove();

	function updateURLParameter(url, param, paramVal){
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}



$( document ).ready(function() {

	$( ".alert button.close" ).click(function() {
	  $( this ).parent().hide();
	});
	
	if ($("input[type='text'].datepicker").length > 0) {
					$("input[type='text'].datepicker").datepicker({
						prevText: '',
						nextText: '',
						dateFormat: 'yy-mm-dd'
					});
	}
	
	$("input[type='text'].timepicker").datetimepicker({
		dateFormat: '', 
		timeFormat: 'hh:mm',
		timeOnly: true,
		hourText: "Godzina",
		minuteText: "Minuta",
		timeText: "Czas",
		currentText: "Teraz",
		closeText: "Zrobione",
		timeOnlyTitle:'Wybierz godzinę'
	});

	$('.timepicker').click(function() {
		  $(this).focus();
		});
	
	$( ".timepicker" ).blur( function() {
			
			var patt = /^[0-9]{2}:[0-9]{2}$/;
			if(!patt.test($(this).val()))
			{
				$(this).val('00:00');
			}
			
		});
	
	$('.datepicker').click(function() {
		  $(this).focus();
		});
	
	$( ".datepicker" ).blur( function() {
			
			var patt = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/;
			if(!patt.test($(this).val()))
			{
				$(this).val('');
			}
			
		});

	$('[data-toggle="tooltip"]').tooltip();

	});
});


