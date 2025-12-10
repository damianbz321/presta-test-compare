/**
* 2012-2015 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - PD Pop-Ups Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2015 Patryk Marek - PrestaDev.pl
* @link		 http://prestadev.pl
* @package	 PD Pop-Ups Pro - PrestaShop 1.5.x and 1.6.x Module
* @version	 1.1.2
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @date		 10-06-2015
*/

document.addEventListener("DOMContentLoaded", function(){
	PdPopupsPro.pdInit();
	PdPopupsPro.pdBindEvents();
});

let PdPopupsPro = {
	pdInit() {
		if (pdpopupspro_time_open > 0 && !PdPopupsPro.pdGetCookie(pdpopupspro_cookie_name)) {
			setTimeout(PdPopupsPro.pdShowPopup, pdpopupspro_time_open*1000);
		} else if (pdpopupspro_time_open == 0 && !PdPopupsPro.pdGetCookie(pdpopupspro_cookie_name)) {
			PdPopupsPro.pdShowPopup();
		}

		if (pdpopupspro_time_close > 0 && pdpopupspro_time_open == 0) {
			setTimeout(PdPopupsPro.pdHidePopup, pdpopupspro_time_close*1000);
		} else if (pdpopupspro_time_close > 0 && pdpopupspro_time_open > 0) {
			setTimeout(PdPopupsPro.pdHidePopup, (parseInt(pdpopupspro_time_close)+parseInt(pdpopupspro_time_open))*1000);
		}
	},
	pdBindEvents() {
		// Colose on ESC
		$(document).on('keyup',function(evt) {
		    if (evt.keyCode == 27) {
		       PdPopupsPro.pdHidePopup();
		       if($("#pdpopupspro-checkbox").is(':checked')) {
					PdPopupsPro.pdSetCookie(pdpopupspro_cookie_name);
				}
		    }
		});

		// Close on click overlay
		$(document).on('click', '#pdpopupspro-overlay', function(e) {
			PdPopupsPro.pdHidePopup();
			if($("#pdpopupspro-checkbox").is(':checked')) {
				PdPopupsPro.pdSetCookie(pdpopupspro_cookie_name);

				if($("#pdpopupspro-checkbox").is(':checked')) {
					PdPopupsPro.pdSetCookie(pdpopupspro_cookie_name);
				}
			}
		});

		// Submit close by user
		$(document).on('click', '#pdpopupspro .close', function(e) {
			PdPopupsPro.pdHidePopup();
			if($("#pdpopupspro-checkbox").is(':checked'))
				PdPopupsPro.pdSetCookie(pdpopupspro_cookie_name);
		});

		$(document).on('click', 'input#ncp', function() {
			if ($(this).is(':checked')) {
				$('#pdpopupspro .form_message').text('');
			}
		});

		// Ajax submit newsletter subscription by user
		//	1 = registered in block
		//	2 = registered in customer
		//	3 = not a customer and not registered
		//	4 = customer not registered

		$(document).on('click', ' #pdpopupspro .btn-newsletter', function(e) {				
			var email = $('#submited_email').val();
			var id_pdpopupspro = $('#id_pdpopupspro').val();
			if (pdpopupspro_npc_enabled != 0) {
				if ($("input#ncp").is(':checked') == false) {
					$('#pdpopupspro .form_message').text(pdpopupspro_npc_msg);
					return;
				} 
			}

			if (email) {
				$.ajax({
					url: pdpopupspro_ajax_link,
					type: "POST",
					dataType: "json",
					data: {
						'action': 'subscribeEmail', 
						'secure_key': pdpopupspro_secure_key,
						'email': email,
						'id_pdpopupspro': id_pdpopupspro,
						'ajax' : true
					},
					success: function(result) {
						if (result === 0) {
							PdPopupsPro.pdMessageForm(pdpopupspro_msg_error_0);
						} else if (result === 1) {
							PdPopupsPro.pdMessageForm(pdpopupspro_msg_error_1);
						} else if (result === 2) {
							PdPopupsPro.pdMessageForm(pdpopupspro_msg_error_2);
						} else if (result === 3) {
							PdPopupsPro.pdMessageForm(pdpopupspro_msg_verification_email_send);
							PdPopupsPro.pdSetCookie(pdpopupspro_cookie_name);
						} else if (result === 4) {
							PdPopupsPro.pdMessageForm(pdpopupspro_msg_success);
							PdPopupsPro.pdSetCookie(pdpopupspro_cookie_name);
						}
					}
				});
			} else {
				$('#pdpopupspro .form_message').text(pdpopupspro_msg_required);
			}
		});
	},
    pdMessageForm(msg) {
		$('#pdpopupspro .form_message').text(msg);
	},
	pdHidePopup() {
		$('#pdpopupspro-overlay').hide();
		$('#pdpopupspro').fadeOut('fast');

		if($("#pdpopupspro-checkbox").is(':checked'))
			PdPopupsPro.pdSetCookie(cookie_name);
	},
	pdShowPopup() {
		$('#pdpopupspro-overlay').show();
		$('#pdpopupspro').fadeIn('slow');
	},
	pdSetCookie(cookie_name) {
		var value = '1';
		var expire = new Date();
		expire.setDate(expire.getDate()+pdpopupspro_cookie_expire);
		document.cookie = cookie_name + "=" + escape(value) +";path=/;" + ((expire==null) ? "" : ("; expires=" + expire.toGMTString()))
	},
	pdGetCookie(cookie_name) {
		var name = cookie_name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
		}
		return false;
	}
}


