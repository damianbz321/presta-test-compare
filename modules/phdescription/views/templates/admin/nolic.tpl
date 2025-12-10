<div class="panel">
    <div class="">
        <div class="alert alert-danger">
            <p>
                UWAGA!<br />Licencja na moduł <b>{$moduleName}</b>{if isset($moduleDomain)} dla domeny <b>{$moduleDomain}</b>{/if} wygasła lub brak jest jej w systemie.<br />
                Skontaktuj się z naszym sklepem na <a href="https://prestahelp.com" target="_blank">prestahelp.com</a> aby móc dalej korzystać z tego modułu.<br />
                Pamiętaj aby podać nazwę domeny oraz nazwę modułu, z którego korzystasz.<br />
            </p>
        </div>
        <p>
            Domena: <b>{$moduleDomain}</b><br />
            Nazwa modułu: <b>{$moduleName}</b><br />
            Moduł: <b>{$moduleName2}</b><br />
        </p>
    </div>
    <div class="panel-footer">
        <form method="post">
            <button id="configuration_form_submit_btn" class="btn btn-warning pull-left" type="submit" value="1" name="submitCheckLicence"><i class="process-icon-refresh"></i> {l s='Sprawdź licencję' mod='phstickers'}</button>
        </form>
    </div>
</div>
