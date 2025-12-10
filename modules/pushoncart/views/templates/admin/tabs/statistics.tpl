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

<h3><i class="icon-book"></i> {l s='Usage Statistics' mod='pushoncart'} <small>{$module_display|escape:'htmlall':'UTF-8'}</small></h3>
<div class="table-responsive clearfix">
	<div class="row-fluid">
		<div class="col-sm-12 col-md-12 col-lg-12">

			<form role="form" class="form-inline" action="{$requestUri|escape:'htmlall':'UTF-8'}" method="post">
				<p>{l s='Welcome to the Statistics tab of your module.' mod='pushoncart'}</p>
				<p>{l s='In this tab, you will find module statistics including additional figure that allow you to generate business.' mod='pushoncart'}</p>
				<p><b>{l s='All amounts exclude tax (before VAT).' mod='pushoncart'}</b></p>
				<p>{l s='Select a period to view statistics' mod='pushoncart'}</p>
				<p><i>{l s='Only the generated revenue is set to match the set dates.' mod='pushoncart'}</i></p>
				
				<div class="col-sm-12 col-md-12 col-lg-12">

						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
							<label {if $ps_version neq 1}style="text-align: left;"{/if} for="stats_date_from" class="control-label">{l s='Period from:' mod='pushoncart'}&nbsp;&nbsp;</label> 
							{if $browser eq 'Chrome'}
								<div class="input-group">
									<input type="date" class="form-control" id="stats_date_from" name="stats_date_from" value="{$date_from|escape:'htmlall':'UTF-8'}">
									<span class="input-group-addon">
											 {if $ps_version eq 1}<i class="icon-calendar-empty">{else}<i class="icon-calendar-o">{/if}</i>
									</span>
								</div>
							{else}
								<div {if $ps_version neq 1}style="width:150px;"{/if} class="input-group">
									<input class="bootstrap datepicker form-control" autocomplete="off" name="stats_date_from_alt" id="stats_date_from_alt" value="{$date_from_alt|escape:'htmlall':'UTF-8'}">
									<span class="input-group-addon">
										{if $ps_version eq 1}<i class="icon-calendar-empty">{else}<i class="icon-calendar-o">{/if}</i>
									</span>
								</div>
							{/if}                           
							&nbsp;&nbsp;
						</div>

						<div class="col-xs-12 col-sm-12 col-md-6 col-lg-3">
							<label {if $ps_version neq 1}style="text-align: left;"{/if} for="stats_date_to" class="control-label">{l s='Period to:' mod='pushoncart'}&nbsp;&nbsp;</label>
								{if $browser eq 'Chrome'}
										<div class="input-group">
											<input type="date" class="form-control" id="stats_date_to" name="stats_date_to" value="{$date_to|escape:'htmlall':'UTF-8'}">
											<span class="input-group-addon">
												{if $ps_version eq 1}<i class="icon-calendar-empty">{else}<i class="icon-calendar-o">{/if}</i>
											</span>
										</div>
								{else}
										<div {if $ps_version neq 1}style="width:150px;"{/if} class="input-group">
											<input class="bootstrap datepicker form-control" autocomplete="off" id="stats_date_to_alt" name="stats_date_to_alt" value="{$date_to_alt|escape:'htmlall':'UTF-8'}">
											<span class="input-group-addon">
												{if $ps_version eq 1}<i class="icon-calendar-empty">{else}<i class="icon-calendar-o">{/if}</i>
											</span>
										</div>
								{/if}
							&nbsp;&nbsp;
						</div>

						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
							<div class="clear">&nbsp;</div>
						</div>
						<div class="clear">&nbsp;</div>
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<div class="input-group">
								<input type="hidden" name="browser" value="{$browser|escape:'htmlall':'UTF-8'}" />
								&nbsp;&nbsp;<button type="submit" name="submitStats" class="btn btn-info"><i class="icon-repeat"></i>&nbsp;{l s='Update' mod='pushoncart'}</button>
								&nbsp;&nbsp;<button type="submit" name="resetStats" class="btn btn-warning"><i class="icon-refresh"></i>&nbsp;{l s='Reset' mod='pushoncart'}</button>
							</div>
						</div>

				</div>

				<div class="clear">&nbsp;</div>
				<div class="clear">&nbsp;</div>
				<div id="statisticTable">
					
					<table id='statisticTable' cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered table-hover dataTable">
						<thead>
							<tr>
								<th>{l s='View' mod='pushoncart'}</th>
								<th>{l s='Discount ID' mod='pushoncart'}</th>
								<th>{l s='Discount Name' mod='pushoncart'}</th>
								<th>{l s='Product ID' mod='pushoncart'}</th>
								<th>{l s='Product' mod='pushoncart'}</th>
								<th>{l s='Retail Price' mod='pushoncart'}</th><!-- Saved value in PushOnCartProducts table -->
								<th>{l s='Views' mod='pushoncart'}</th>
								<th>{l s='No of Sales' mod='pushoncart'}</th>
								<th>{l s='Promotion Efficiency' mod='pushoncart'}</th>
								<th>{l s='Generated Revenue' mod='pushoncart'}</th>
								<th>{l s='Erase' mod='pushoncart'}</th>
							</tr>
						</thead>
						<tbody id="statistics_table_body">
							{$stats_table} <!-- Contains HTML, cannot be escaped -->
						</tbody>
					</table>
				</div>

				<div id="statsOrdersTable_area" name="statsOrdersTable_area">
					<table id='statsOrdersTable' name='statsOrdersTable' cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><i style="cursor:pointer;" class="icon-eye-close icon-2x" onClick="hideStatsOrders();"></i></th>
								<th>{l s='Discount ID' mod='pushoncart'}</th>
								<th>{l s='Shop ID' mod='pushoncart'}</th>
								<th>{l s='Date' mod='pushoncart'}</th>
								<th>{l s='Order ID' mod='pushoncart'}</th>
								<th>{l s='Customer ID' mod='pushoncart'}</th>
								<th>{l s='Customer Email' mod='pushoncart'}</th>
							</tr>
						</thead>
						<tbody id="statsOrdersTable_body" name="statsOrdersTable_body">
						</tbody>
					</table>
				</div>
			</form>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div id="monthlyStatisticTable">
					<table id='monthlyStatisticTable' cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>{l s='Month' mod='pushoncart'}</th>
								<th>{l s='Additional Revenue' mod='pushoncart'}</th>
							</tr>
						</thead>
						<tbody id="monthly_statistics_table_body">
							{$monthly_table} <!-- Contains HTML, cannot be escaped -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
						
{literal}
<script> 
	
function hideStatsOrders()
{
	$("#statsOrdersTable_body").hide(300);
	$("#statsOrdersTable").hide(300);
}

$("#statsOrdersTable_area").hide();
$("#statsOrdersTable_body").hide();
$("#statsOrdersTable").hide();   
</script>
{/literal}
