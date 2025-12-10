{*
* Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if isset($css) && $css}{foreach from=$css item='css_file'}
	<link type="text/css" rel="stylesheet" href="{$css_file|escape:'quotes':'UTF-8'}">{/foreach}{/if}
{if isset($js) && $js}{foreach from=$js item='js_file'}
	<script type="text/javascript" src="{$js_file|escape:'quotes':'UTF-8'}"></script>
{/foreach}{/if}