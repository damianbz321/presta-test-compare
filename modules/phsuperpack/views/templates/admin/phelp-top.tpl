{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author    PrestaHelp.com
*  @copyright 2019 PrestaHelp
*  @license   LICENSE.txt
*}

<div class="view">
    <div class="box-auto">
        <div class="container-auto">
            <div class="container-row">
                <div class="branding">
                    <img src="{$moduleAssets}img/Branding.png" alt="Prestahelp Branding">
                </div>
                <div class="inclusion">
                    <span class="inclusion-paczkomaty">{$moduleName}</span>
                    <span class="inclusion-inpost">({$moduleNameInfo}, v {$moduleVersion})</span>
                </div>
                <div class="buttons">
                    {if $lastestVersion}
                        <div class="btn-prestahelp green--bg">
                            <img src="{$moduleAssets}img/arrow_down.png" alt="Prestahelp Arrow">Twój moduł jest aktualny.
                            <a href="https://www.prestahelp.com/pl/glowna/309-zestaw-produktow-do-prestashop-16-oraz-17.html" target="_blank">zostaw opinię <img src="{$moduleAssets}img/arrow_right.png" alt="Prestahelp Arrow"></a>
                        </div>
                    {else}
                        <div class="btn-prestahelp red--bg">
                            <img src="{$moduleAssets}img/close.png" alt="Prestahelp Close">Twój moduł jest nieaktualny.
                            (aktualna wersja {$currentModuleVersion})
                            <a href="{$updateLink}" target="_blank">Aktualizuj moduł <img src="{$moduleAssets}img/arrow_right-red.png" alt="Prestahelp Arrow"></a>
                        </div>
                    {/if}
                    <a id="chengelog-btn" href="#changelog-btn" class="chengelog-btn">changelog <img src="{$moduleAssets}img/chengelog_down.png" alt="Prestahelp Arrow"></a>
                </div>
            </div>
            <div class="container-row middle">
                <div class="buttons buttonsSupport">

                </div>

            </div>
        </div>
    </div>
</div>
