<!--
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */
-->
 
<script type="text/javascript">

var ruch_ajax_uri = '{$ruch_ajax_uri|escape:'htmlall':'UTF-8'}';
var ruch_img_uri = '{$ruch_img_uri|escape:'htmlall':'UTF-8'}';
var ruch_token = '{$ruch_token|escape:'htmlall':'UTF-8'}';
var ruch_serv = [{$ruch_serv|escape:'htmlall':'UTF-8'}];
var ruch_codserv = [{$ruch_codserv|escape:'htmlall':'UTF-8'}];
var ruch_tel = '{$ruch_tel|escape:'htmlall':'UTF-8'}';
var ruch_adr = '{$ruch_adr|escape:'htmlall':'UTF-8'}';
var ruch_ceny = JSON.parse('{$ruch_ceny|escape:'htmlall':'UTF-8'}'.replace(/&quot;/g, '\"'));
var ruch_baseLink = '{$ruch_baseLink|escape:'htmlall':'UTF-8'}';
var ruch_sandbox = '{$ruch_sandbox|escape:'htmlall':'UTF-8'}';
var ruch_async_carrier_loaded = '{$ruch_async_carrier_loaded|escape:'htmlall':'UTF-8'}';
var ruch_display_map_as_popup  = '{$ruch_display_map_as_popup|escape:'htmlall':'UTF-8'}';
var ruch_showCodFilter = '{$ruch_showCodFilter|escape:'htmlall':'UTF-8'}';
var ruch_showPointTypeFilter = '{$ruch_showPointTypeFilter|escape:'htmlall':'UTF-8'}';
var ruch_initial_address = '{$ruch_initial_address|escape:'htmlall':'UTF-8'}';
var ruch_chk_type = '{$ruch_chk_type|escape:'htmlall':'UTF-8'}';
var ruch_msg_nopkt = 'Metoda dostawy wymaga wybrania punktu odbioru';
var ruch_msg_nopayment = 'Metoda dostawy wymaga wybrania punktu odbioru obsługującego płatności';
var ruch_msg_notel = 'Metoda dostawy wymaga podania telefonu. Wróć do wcześniejszego kroku i wypełnij pole Nr telefonu.';
var ruch_msg_btel = 'Metoda dostawy wymaga podania prawidłowego telefonu. Wróć do wcześniejszego kroku i wypełnij pole Nr telefonu.';
var ruch_msg_noadr = 'Metoda dostawy wymaga podania ulicy i numeru budynku. Wróć do wcześniejszego kroku i wypełnij adres.';
var ruch_html_info = '<br/><div class="title" id="ruch_selpkt_desc" style=""></div><br/><div class="title" id="ruch_validation_message" style=""></div><br/>';
var ruch_html_info_popup = '<div class="title ruch-widget-margin-top" id="ruch_selpkt_desc" style=""></div><div class="title" id="ruch_validation_message" style=""></div>';
var ruch_html = '<div id="ruchWidgetMapContainer"><div id="ruch_pkt"><div class="widget" id="ruch_widget"></div></div>' + ((ruch_display_map_as_popup) ? '' : ruch_html_info) + '</div>';
var ruch_detection_carrier_interval = 350;
var ruch_detection_carrier_timeout = 7000;
var ruch_detection_delay_for_async_content_interval = 350;
var ruch_detection_delay_for_async_content_timeout = 2000;
var ruch_selpkt='';
var ruch_debug  = {$ruch_debug};

if((ruch_chk_type == 'supercheckout') || (ruch_chk_type == 'wk_opc') || (ruch_chk_type == 'default')) ruch_display_map_as_popup = 1;

function ruchDelayForAsyncLoadedContentStart() {
	var ruch_detection_delay_for_async_content_id = setInterval(function () {
		if (typeof ruchDetectionCarrierStart === 'function')
		{
			ruchDelayForAsyncLoadedContentStop();
			ruchDetectionCarrierStart();
            clearInterval(ruch_detection_delay_for_async_content_id);
		}
	}, ruch_detection_delay_for_async_content_interval);

	setTimeout(function () {
		ruchDelayForAsyncLoadedContentStop(ruch_detection_delay_for_async_content_id);
	}.bind(ruch_detection_delay_for_async_content_id), ruch_detection_delay_for_async_content_timeout);
}

function ruchDelayForAsyncLoadedContentStop(ruch_nterval_id) {
		clearInterval(ruch_nterval_id);
}

document.addEventListener("DOMContentLoaded", function(event) { 
	//some OPC module load cart.tpl on every changes on page and we have to register event one more time.
	ruchDelayForAsyncLoadedContentStart();

	//For Supercheckout OPC - Override the core html method in the jQuery object - execute our script during shipping methods dynamic load
	if(ruch_chk_type == 'supercheckout') {
		(function($, oldHtmlMethod){
		    $.fn.html = function(){
		        var results = oldHtmlMethod.apply(this, arguments);
	        	if(this.get(0) && this.get(0).id && this.get(0).id == 'shipping-method') {
	    	       ruchDetectionCarrierAsync();
		        }
		        return results;
	    	};
		})(jQuery, jQuery.fn.html);
	}
});

</script>