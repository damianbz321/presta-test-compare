{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2018 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<div id="product_table" class="product_table">
    <table id='productTable' cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>{l s='View' mod='pushoncart'}</th>
                <th>{l s='Shop Name' mod='pushoncart'}</th>
                <th>{l s='Selected Products' mod='pushoncart'}</th>
                <th>{l s='Discount ID' mod='pushoncart'}</th>
                <th>{l s='Discount Name' mod='pushoncart'}</th>
                <th>{l s='Expiry Date' mod='pushoncart'}</th>
                <th>{l s='Multiple Codes Allowed' mod='pushoncart'}</th>
                <th>{l s='Priority' mod='pushoncart'}</th>
                <th>{l s='Active' mod='pushoncart'}</th>
                <th>{l s='Delete' mod='pushoncart'}</th>
            </tr>
        </thead>
        <tbody id="table_body">
            {$productTable} <!-- Contains HTML, cannot be escaped -->
        </tbody>
    </table>
</div>    