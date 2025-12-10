$(document).ready(function () {
	if ($.cookie("labpopupnewsletter") != "true" && $('body').outerWidth() > 767) {
$("#laberPopupnewsletter").modal({show: true});
		$(".send-reqest").click(function(){
			var email = $("#labnewsletter-input").val();
			$.ajax({
				type: "POST",
				headers: { "cache-control": "no-cache" },
				async: false,
				url: field_path,
				data: "name=marek&email="+email,
				success: function(data) {
					if (data)
						$(".labAlert").text(data);
				}
			});
		});
		$('#labnewsletter-input').keypress(function(event){
		  var keycode = (event.keyCode ? event.keyCode : event.which);
		  if (keycode == '13') {
					var email = $("#labnewsletter-input").val();
					$.ajax({
						type: "POST",
						headers: { "cache-control": "no-cache" },
						async: false,
						url: field_path,
						data: "name=marek&email="+email,
						success: function(data) {
							if (data)
								$(".labAlert").text(data);
						}
					});
					event.preventDefault();
		  }
		});
                $("#laber_newsletter_dont_show_again").prop("checked") == false;
	}

	
	
	$('#laber_newsletter_dont_show_again').change(function(){
	    if($(this).is(':checked')){
		$.cookie("labpopupnewsletter", "true");
	    }else{
		$.cookie("labpopupnewsletter", "false");
	    }
	});

});