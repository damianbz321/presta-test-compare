<form method="post">
    <div class="panel">
        <div class="panel-heading"><i class="icon-cog"></i> {l s='Settings' mod='phdescription'}</div>
        <div class="form-horizontal">
            <div class="rows">
                <div class="form-group">
                    <label class="control-label col-lg-3 text-right">{l s='Open tab in product' mod='phdescription'}:</label>
                    <div class="col-lg-3">
						<span class="switch prestashop-switch fixedd-width-lg">
							<input id="ac1_on" onClick="toggleDraftWarning(false);showRedirectProductOptions(false);" name="PHDESCRIPTION_EDIT" value="1" type="radio"{if $edit == 1} checked="checked"{/if} />
							<label class="radioCheck" for="ac1_on">{l s='YES' mod='phdescription'}</label>
							<input id="ac1_off" onClick="toggleDraftWarning(true);showRedirectProductOptions(true);" name="PHDESCRIPTION_EDIT" value="0" type="radio"{if $edit == 0} checked="checked"{/if} />
							<label class="radioCheck" for="ac1_off">{l s='NO' mod='phdescription'}</label>
							<a class="slide-button btn"></a>
						</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button id="configuration_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitSaveSettings"><i class="process-icon-save"></i> {l s='Save' mod='phdescription'}</button>
        </div>
    </div>
</form>

<div class="panel">
    <div class="panel-heading"><i class="icon-cog"></i> {l s='Settings' mod='phdescription'}</div>
    <div class="form-horizontal">
        <div class="alert alert-info">
            <p>
                Moduł umieszcza modułowy opis w hooku <b>displayFooterProduct</b>.
                <br />Możesz też za pomocą hooka <b>displayModularDESC</b> zmienić jego miejsce wyświetlania na dowolne na karcie produktu używając tego wpisu:<br />
                <input type="text" value="&#123;hook h='displayModularDESC' pro=$product&#125;" readonly style="background: none;border: none;cursor: text;" />
            </p>
        </div>
    </div>
</div>
