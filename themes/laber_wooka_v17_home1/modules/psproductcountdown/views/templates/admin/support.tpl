{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2016 Presta.Site
* @license   LICENSE.txt
*}
<div id="pspc-pro-features">
    {if $psv == 1.5}
        <br/><fieldset><legend>{l s='Support' mod='psproductcountdown'}</legend>
    {else}
        <div class="panel">
        <div class="panel-heading">
            <i class="icon-envelope"></i> {l s='Support' mod='psproductcountdown'}
        </div>
    {/if}

        <div>
            <p>{l s='Feel free to ask questions in our official thread on PrestaShop forum. Any feedback would be highly appreciated!' mod='psproductcountdown'}</p>
            <p><a target="_blank" href="https://www.prestashop.com/forums/topic/568613-free-module-product-countdown-ps-151617/">{l s='Link' mod='psproductcountdown'}</a></p>
        </div>

    {if $psv == 1.5}
        </fieldset><br/>
    {else}
        </div>
    {/if}
</div>
