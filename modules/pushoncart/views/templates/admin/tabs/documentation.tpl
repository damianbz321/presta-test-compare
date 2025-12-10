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
<h3><i class="icon-book"></i> {l s='Documentation' mod='pushoncart'} <small>{$module_display|escape:'htmlall':'UTF-8'}</small></h3>
<div class="media">
     <ul style='list-style-type:circle;'>
        <li><b>{l s='Attached you will find the documentation for your module. Do not hesitate to consult to properly configure it. ' mod='pushoncart'}</b></li>
     </ul>
        <!--<p>{*l s='Download the official user guide: ' mod='pushoncart'*}</p>-->
        <a href="{$module_dir|escape:'htmlall':'UTF-8'}{$guide_link|escape:'htmlall':'UTF-8'}" target="_blank"><img src="{$module_dir|escape:'htmlall':'UTF-8'}/img/pdf.png"></a><br /><br /><br />
        <ul style='list-style-type:circle;'>
            <li>{l s='Access to Prestashops free documentation: ' mod='pushoncart'}</li>
        </ul>
        <a href="http://doc.prestashop.com/dashboard.action" target="_blank"> http://doc.prestashop.com/dashboard.action</a><br /><br />
        <p>{l s='Need help? Click the "contact" tab ' mod='pushoncart'}</p><br />
</div>