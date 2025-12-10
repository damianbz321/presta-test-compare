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
<div id="pspc-instructions">
    {if $psv == 1.5}
        <br/><fieldset><legend>{l s='Additional instructions' mod='psproductcountdown'}</legend>
    {else}
        <div class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Additional instructions' mod='psproductcountdown'}
        </div>
    {/if}

        <p>
            {l s='You can use this custom hook to place a countdown anywhere in your template:' mod='psproductcountdown'}
            <b>{literal}{hook h='PSProductCountdown' id_product='X'}{/literal}</b>
            <br> ({l s='Replace X by some product ID' mod='psproductcountdown'})
        </p>

        <p>
            {l s='Here are examples for the most common cases:' mod='psproductcountdown'} <br>
            <ul>
                <li>
                    {l s='In' mod='psproductcountdown'} <b>product.tpl</b> {l s='use' mod='psproductcountdown'}
                    {literal}{hook h='PSProductCountdown' id_product=$product.id_product}{/literal}
                </li>
            </ul>
        </p>

        <p>{l s='Simply paste that code to some place in your template file.' mod='psproductcountdown'}</p>

    {if $psv == 1.5}
        </fieldset><br/>
    {else}
        </div>
    {/if}
</div>
