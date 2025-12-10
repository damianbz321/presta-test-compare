{*
*
* @author Przelewy24
* @copyright Przelewy24
* @license https://www.gnu.org/licenses/lgpl-3.0.en.html
*
*}
<div class="panel">
    <div class="panel-heading">
        <i class="icon-money"></i> {l s='Przelewy24 Payment' mod='przelewy24'}
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th><span class="title_box"> </span></th>
                <th><span class="title_box">{l s='Date' mod='przelewy24'}</span></th>
                <th><span class="title_box">{l s='Transaction ID' mod='przelewy24'}</span></th>
                <th><span class="title_box">{l s='Przelewy24 ID' mod='przelewy24'}</span></th>
                <th><span class="title_box">{l s='Order ID' mod='przelewy24'}</span></th>
                <th><span class="title_box">{l s='Amount' mod='przelewy24'}</span></th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$p24Payments key='index' item='p24Payment'}
                <tr>
                    <td><span>{$index+1}</span></td>
                    <td><span>{$p24Payment->date_add}</span></td>
                    <td><span>{$transaction_id}</span></td>
                    <td><span>{$p24_full_order_id}</span></td>
                    <td><span>{$ps_order_id}</span></td>
                    <td><span>{$p24Payment->amount}</span></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
