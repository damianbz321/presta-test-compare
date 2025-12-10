<!--
/**
 * @author    Marcin Bogdański
 * @copyright OpenNet 2021
 * @license   license.txt
 */
-->
 
<br />
<script>
    var ruch_ajax_uri = '{$ruch_ajax_uri|escape:'htmlall':'UTF-8'}';
    var ruch_token = '{$ruch_token|escape:'htmlall':'UTF-8'}';
    var ruch_data = JSON.parse('{$ruch_data|escape:'quotes':'UTF-8'}');
    var ruch_defaults = JSON.parse('{$ruch_defaults|escape:'quotes':'UTF-8'}');
    var ruch_pdf_uri = '{$ruch_pdf_uri|escape:'htmlall':'UTF-8'}';
    var ruch_img_uri = '{$ruch_img_uri|escape:'htmlall':'UTF-8'}';
    var ruch_track_url = '{$ruch_track_url|escape:'htmlall':'UTF-8'}';
    var ruch_order_id = '{$ruch_order_id|escape:'htmlall':'UTF-8'}';
    var ruch_typ = '{$ruch_typ|escape:'htmlall':'UTF-8'}';
</script>

<iframe id="ruch_down" style="display: none;"></iframe>

<div class="card" id="ruch">
    <div class="card-header">
	    <h3 class="card-header-title">Wysyłka Orlen</h3>
	</div>

	<div id="ruch_shipment_creation"{if isset($smarty.get.scrollToShipment)} class="card-body"{/if}>
				<div id="ruch_msg_container">{if isset($errors) && $errors}{include file=$smarty.const._PS_MODULE_DIR_|cat:'ruch/views/templates/admin/errors.tpl'}{/if}</div>

				<div class="card-block row"">
					<div id="ruch_actions" class="form-horizontal">
						<div class="col-lg-4">
                            <table cellspacing="0" cellpadding="10" class="table">
                                <tbody>
                                    <tr>
							             <td><button class="btn btn-default pull-right" id="ruch_create_label" type="button"><i class="process-icon-save"></i> {l s='Utwórz i drukuj etykietę' mod='ruch'}</button></td>
                                         <td><button class="btn btn-default pull-right" id="ruch_edit_label" type="button"><i class="process-icon-save"></i> {l s='Edytuj i drukuj etykietę' mod='ruch'}</button></td>
							             <td><button class="btn btn-default pull-right" id="ruch_print_label" type="button" style="display: none;"><i class="process-icon-save"></i> {l s='Drukuj etykietę' mod='ruch'}</button></td>
							        </tr>
							    </tbody>
							</table>
						</div>
					</div>
				</div>
				
				<hr/>
	            
	    <form id="ruch_form">
	        
	    	<div class="card-block row" id="ruch_form_div" style="display: none;">
	            <div class="col-10">
                    <h3 class="title-top">Odbiorca</h3>
                    
                    <div class="form-group row type-text" id="ruch_formo1">
                        <label for="ruch_form_nazwa" class="form-control-label col-3">Nazwa</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_nazwa" name="ruch_form_nazwa" value="" maxlength="120" size="120" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row type-text ruch_oadr" id="ruch_formo2">
                        <label for="ruch_form_adres1" class="form-control-label col-3">Ulica</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_adres1" name="ruch_form_adres1" value="" maxlength="50" size="50" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row type-text ruch_oadr" id="ruch_formo3">
                        <label for="ruch_form_adres2" class="form-control-label col-3">Budynek i lokal</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_adres2" name="ruch_form_adres2" value="" maxlength="50" size="50" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row type-text ruch_oadr" id="ruch_formo4">
                        <label for="ruch_form_kod" class="form-control-label col-3">Kod</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_kod" name="ruch_form_kod" value="" maxlength="8" size="8" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row type-text ruch_oadr" id="ruch_formo5">
                        <label for="ruch_form_miasto" class="form-control-label col-3">Miasto</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_miasto" name="ruch_form_miasto" value="" maxlength="120" size="120" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row type-text" id="ruch_formo7">
                        <label for="ruch_form_imie" class="form-control-label col-3">Imię</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_imie" name="ruch_form_imie" value="" maxlength="50" size="50" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row type-text" id="ruch_formo8">
                        <label for="ruch_form_nazwisko" class="form-control-label col-3">Nazwisko</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_nazwisko" name="ruch_form_nazwisko" value="" maxlength="50" size="50" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row type-text" id="ruch_formo9">
                        <label for="ruch_form_tel" class="form-control-label col-3">Telefon</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_tel" name="ruch_form_tel" value="" maxlength="15" size="15" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row type-text" id="ruch_formo10">
                        <label for="ruch_form_email" class="form-control-label col-3">E-mail</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_email" name="ruch_form_email" value="" maxlength="80" size="80" class="form-control">
                        </div>
                    </div>

                </div>

				<div class="col-10">
				
					<h3 class="title-top">Paczki</h3>
				
				    <div class="form-group" id="ruch_forms1">
                        <label for="ruch_form_serv" class="control-label col-lg-3">Usługa</label>
                        <div class="input-group col-lg-5">
                            <select id="ruch_form_serv" name="ruch_form_serv" size="2"><option id="S">Standard</option><option id="M" selected>Mini</option></select>
                        </div>
                    </div>
				    <div class="form-group row type-text" id="ruch_forms1" style="display: none;">
                        <label for="ruch_form_serv" class="form-control-label col-3">Usługa</label>
                        <div class="col-7">
                            <select id="ruch_form_serv" name="ruch_form_serv" size="2"><option id="S">Standard</option><option id="M" selected>Mini</option></select>
                        </div>
                    </div>

					{include file=$smarty.const._PS_MODULE_DIR_|cat:'ruch/views/templates/admin/paczki.tpl'}
                </div>
                
                <div class="col-10">
                	<h3 class="title-top">Usługi dodatkowe</h3>

				    <div class="form-group row type-text" id="ruch_forma1">
                        <label for="ruch_form_kwota" class="form-control-label col-3">Kwota pobrania</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_kwota" name="ruch_form_kwota" autocomplete="off" onchange="this.value = this.value.replace(/,/g, '.');" value="" maxlength="12" size="12" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group row type-text" id="ruch_forma2">
                        <label for="ruch_form_ubezp" class="form-control-label col-3">Ubezpieczenie</label>
                        <div class="col-7">
                            <input type="text" id="ruch_form_ubezp" name="ruch_form_ubezp" value="" maxlength="10" size="10" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row" id="ruch_forma3">
                        <label for="ruch_form_mail" class="form-control-label col-3">Mail</label>
                        <div class="col-7">
                            <input type="checkbox" id="ruch_form_mail" name="ruch_form_mail" value="1">
                        </div>
                    </div>

                    <div class="form-group row" id="ruch_forma4">
                        <label for="ruch_form_sms" class="form-control-label col-3">SMS</label>
                        <div class="col-7">
                            <input type="checkbox" id="ruch_form_sms" name="ruch_form_sms" value="1">
                        </div>
                    </div>

                </div>

                <div id="ruch_actions" class="form-horizontal col-10">
                    <div class="col-7 clearfix">
                    	<table>
                        	<tbody>
                            	<tr>
                                	<td style="padding: 10px;"><button class="btn btn-default" id="ruch_label" type="button"><i class="process-icon-save"></i> {l s='Drukuj etykietę' mod='ruch'}</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

			</div>
		</form>

			<div id="ruch-status-panel" class="col-10">
				<div class="panel">
					<div id="ruch-status">
						<div class="panel-body">
							<table cellspacing="0" cellpadding="10" class="table">
								<thead>
									<tr>
										<th width="200"><span class="title_box">{l s='Akcja' mod='ruch'}</span></th>
										<th width="50"><span class="title_box">{l s='Status' mod='ruch'}</span></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{l s='Etykieta utworzona' mod='ruch'}</td>
										<td id="ruch_status">Nie</td>
									</tr>
	                                <tr>
    	                                <td>{l s='Link śledzenia' mod='ruch'}</td>
        	                        	<td id="ruch_track_url"></td>
            	                	</tr>
                                    <tr>
										<td>{l s='Punkt' mod='ruch'}</td>
										<td id="ruch_pkt">{$ruch_pkt_id|escape:'htmlall':'UTF-8'}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
