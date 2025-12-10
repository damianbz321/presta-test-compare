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

<h3>
	<i class="icon-link"></i> {l s='Create Promotions' mod='pushoncart'} <small>{$module_display|escape:'htmlall':'UTF-8'}</small>
</h3>

<div class="table-responsive clearfix">
<div class="row-fluid">
				<div class="col-sm-12 col-md-12 col-lg-12">
					<div id="success_alert">{$return_html|escape:'UTF-8'}</div> <!-- variable is escaped in $this->displayConfirmation() -->
					<form role="form" class="form-inline" action="{$requestUri|escape:'htmlall':'UTF-8'}" method="post">
						<p><b>{l s='Welcome to the interface of your Push On Cart page !' mod='pushoncart'}</b></p>
						<ul style='list-style-type:circle;'>
							<li>{l s=' This interface allows you to set personlized promotions selected by your prospective client\'s products. ' mod='pushoncart'}</li>
							<li>{l s=' Once you have chosen the product, you need to define the type and the amount of the discount.' mod='pushoncart'}</li>
						</ul>
						<div class="form-group col-sm-12 col-md-12 col-lg-12">
							<hr/>

							<h4><b>1. {l s='Promotion Name:' mod='pushoncart'}</b></h4>&nbsp;
							<p>{l s='Create a name for this promotion.' mod='pushoncart'}{l s=' This name will appear in your customer\'s shopping basket summary if he chose to take the offer.' mod='pushoncart'}</p>
							<p>{l s='If a name is not given to a promotion, "Special Offer" will be the default name shown to the clients. ' mod='pushoncart'}</p>
							<input class="form-control" type="text" name="discountName" value = "" placeholder = "{l s='ie. Promotion' mod='pushoncart'}" maxlength = "20" style="width:200px;">
							<hr/>

							<h4><b>2. {l s='Product:' mod='pushoncart'}</b></h4>&nbsp;
							<p><span style="color:red">&nbsp;<b>*</b>&nbsp;</span>{l s='Search for a product and choose the product from the list.' mod='pushoncart'}</p>
						   	<input type="hidden" name="token_search" id="token_search" value="{$pushoncart_token|escape:'htmlall':'UTF-8'}">
						   	<input type="hidden" name="lang_search" id="lang_search" value="{$id_lang|escape:'htmlall':'UTF-8'}">
						   	<input placeholder ="{l s='Enter the product name.' mod='pushoncart'}" autocomplete="off" class="form-control" type="text" id="liveSearch" name="liveSearch"><br/>
							<div id="liveSearchResults" class="col-lg-3 col-md-3" style="display: none; border: 1px solid black; box-shadow: 8px 8px 12px; border-radius: 4px; border-top:none;background:white; padding:0px;">
					&nbsp;
				</div>
				<div>
					<span id="selectedProduct" style="cursor:default"></span>
				</div>
				<input type="hidden" id="product" name="product" value = "0"/>
							<br/>
							<div id="duplicate" class="well well-sm">
								<i style='float:left; padding: 0px 10px 0px 3px;' class="icon-exclamation-triangle icon-3x"></i>&nbsp;
								<ul style="list-style: circle inside;">
									<li>{l s='The default attributes are taken. You can change the default attributes in "Combinations" in the product page.' mod='pushoncart'}</li>
									<li>{l s='You can only create one promotion per product and one product per promotion.' mod='pushoncart'}</li>
									<li>{l s='If a product is already linked to a promotion, you will have to delete the existing promotion.' mod='pushoncart'}</li>
								</ul>
							</div>

							<h4><b>3. {l s='Discount:' mod='pushoncart'}</b></h4>&nbsp;
							<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
									{*YES/NO Multiple discounts allowed*}
									<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
										<label for="mutiple_codes_allowed" class="control-label">
											{l s='Multiple discount codes allowed :  ' mod='pushoncart'}
										</label>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
										<span id="range_options" class="switch prestashop-switch input-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<input type="radio" name="multiple_codes_allowed" id="multiple_discounts_on" value= "1" checked="checked" />
												<label for="multiple_discounts_on" class="radioCheck">
														<i class="color_success"></i> {l s='Yes' mod='pushoncart'}
												</label>
												<input type="radio" name="multiple_codes_allowed" id="multiple_discounts_off" value= "0" />
												<label for="multiple_discounts_off" class="radioCheck">
														<i class="color_danger"></i> {l s='No' mod='pushoncart'}
												</label>
												<a class="slide-button btn"></a>
										</span>
									</div>
									<small>&nbsp;{l s='Allowing multiple discounts on a cart will allow multiple PushOnCart promotions to be added to a cart and a promotion to be added to a cart that contains a non-PushOnCart cart rule.' mod='pushoncart'}</small><br/>

									<div class="clear">&nbsp;</div>
									{*YES/NO Ranges*}
									<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
										<label for="use_discount_range" class="control-label">
												{l s='Would you like the value of your promotional offer varied depending on the cart total of the customer' mod='pushoncart'}
										</label>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
											<span id="range_options" class="switch prestashop-switch input-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<input type="radio" name="use_discount_range" id="need_instance_on" value="1" />
													<label for="need_instance_on" class="radioCheck">
															<i class="color_success"></i> {l s='Yes' mod='pushoncart'}
													</label>
													<input type="radio" name="use_discount_range" id="need_instance_off" value="0" checked="checked" />
													<label for="need_instance_off" class="radioCheck">
															<i class="color_danger"></i> {l s='No' mod='pushoncart'}
													</label>
													<a class="slide-button btn"></a>
											</span>
									</div>
							</div>
							<div class="clear">&nbsp;</div>
							<div class="well col-xs-12 col-sm-12 col-md-12 col-lg-12">
{*When YES is selected in step 3 of creating a promotion*}
									<div id="ranges_yes">
											<label for="ranges" class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2">{l s='Choose the number of discount ranges:' mod='pushoncart'}&nbsp;</label>
											 <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
												<select id="ranges" name="num_of_ranges" class="form-control" >
													<option value="1" selected="selected">1</option>
													<option value="2">2</option>
													<option value="3">3</option>
													<option value="4">4</option>
												</select>
											 </div>
											 <div class="clear">&nbsp;</div>
											 <div class="well col-xs-12 col-sm-12 col-md-12 col-lg-12">
												<ul style='list-style-type:circle;'>
													<li>
														{l s=' A promotion offered on a cart total of less than €50 will offer the promotion on carts with a total of €0 - €49.99 and a promotion offered on a cart total of more than €50 will offer the promotion on carts  with a total of €50+' mod='pushoncart'}
													</li>
												</ul>
											</div>

											 <div id="cart_max_min_div">
												<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
														<label for="cart_max_min" class="control-label">
															{l s='To benefit from the promotion, the cart must have :' mod='pushoncart'}&nbsp;&nbsp;
														</label>
												</div>
												<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
														<select id="cart_max_min" name="cart_max_min" class="form-control" >
															<option value="0">{l s='at least' mod='pushoncart'}</option>
															<option value="1">{l s='at most' mod='pushoncart'}</option>
														</select>
												</div>
											</div>

											<div class="clear">&nbsp;</div>

											<!-- range 1 -->
											<div id="range_1" class="row {if $ps_version eq 1}well {/if} col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{l s='A cart total less than:' mod='pushoncart'}&nbsp;&nbsp;</div>
													<div class=" col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<div class="input-group">
															<span class="input-group-addon">{$currency_sign|escape:'htmlall':'UTF-8'}</span>
															<input type="text" id="less_than1" name="less_than1" class="form-control" style="width:50px;">
														</div>
														<small>{l s='(exclusive)' mod='pushoncart'}</small>
													</div>
													<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">&nbsp;&nbsp;{l s='is offered a' mod='pushoncart'}&nbsp;</div>
													<div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
														<input class="form-control" type="text" name="discount_range1" value = "10" maxlength = "3" style="width:50px;">
													</div>
													<div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
														<select class="form-control" name = "discount_type_range1">
															<option name="discount_type_range1" value = "0">%</option>
															{foreach $currency key=k item=v}
																<option name="discount_type_range1" value = "{$v.id_currency|escape:'htmlall':'UTF-8'}">{$v.sign|escape:'htmlall':'UTF-8'}</option>
															{/foreach}
														</select>
													</div>
													<div class=" col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<input type="text" id="promo_code1" placeholder="{l s='Enter a Promo Code' mod='pushoncart'}" name="promo_code1">
													</div>
											</div>

											<!-- range 3-->
											<div id="range_3" class="row {if $ps_version eq 1}well {/if} col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{l s='A cart total between:' mod='pushoncart'}&nbsp;&nbsp;</div>
													<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<div class="input-group">
															<span class="input-group-addon">{$currency_sign|escape:'htmlall':'UTF-8'}</span>
															<input type="text" id="more_than3" name="more_than3" class="form-control" style="width:50px;">
														</div>
														<small>{l s='(inclusive)' mod='pushoncart'}</small>
													</div>
													<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">&nbsp;&nbsp;{l s='and' mod='pushoncart'}&nbsp;&nbsp;</div>
													<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<div class="input-group">
															<span class="input-group-addon">{$currency_sign|escape:'htmlall':'UTF-8'}</span>
															<input type="text" id="less_than3" name="less_than3" class="form-control" style="width:50px;">
														</div>
														<small>{l s='(exclusive)' mod='pushoncart'}</small>
													</div>
													<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">&nbsp;&nbsp;{l s='is offered a' mod='pushoncart'}&nbsp;</div>
													<div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
														<input class="form-control" type="text" name="discount_range3" value = "10" maxlength = "3" style="width:50px;">
													</div>
													<div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
														<select class="form-control" name = "discount_type_range3">
															<option name="discount_type_range3" value = "0">%</option>
															{foreach $currency key=k item=v}
																<option name="discount_type_range3" value = "{$v.id_currency|escape:'htmlall':'UTF-8'}">{$v.sign|escape:'htmlall':'UTF-8'}</option>
															{/foreach}
														</select>
													</div>
													<div class=" col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<input type="text" id="promo_code3" placeholder="{l s='Enter a Promo Code' mod='pushoncart'}" name="promo_code3">
													</div>
											</div>

											<!-- range 4-->
											<div id="range_4" class="row {if $ps_version eq 1}well {/if} col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{l s='A cart total between:' mod='pushoncart'}&nbsp;&nbsp;</div>
													<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<div class="input-group">
															<span class="input-group-addon">{$currency_sign|escape:'htmlall':'UTF-8'}</span>
															<input type="text" id="more_than4" name="more_than4" class="form-control" style="width:50px;">
														</div>
														<small>{l s='(inclusive)' mod='pushoncart'}</small>
													</div>
													<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">&nbsp;&nbsp;{l s='and' mod='pushoncart'}&nbsp;&nbsp;</div>
													<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<div class="input-group">
															<span class="input-group-addon">{$currency_sign|escape:'htmlall':'UTF-8'}</span>
															<input type="text" id="less_than4" name="less_than4" class="form-control" style="width:50px;">
														</div>
														<small>{l s='(exclusive)' mod='pushoncart'}</small>
													</div>
													<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">&nbsp;&nbsp;{l s='is offered a' mod='pushoncart'}&nbsp;</div>
													<div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
														<input class="form-control" type="text" name="discount_range4" value = "10" maxlength = "3" style="width:50px;">
													</div>
													<div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
														<select class="form-control" name = "discount_type_range4">
															<option name="discount_type_range4" value = "0">%</option>
															{foreach $currency key=k item=v}
																<option name="discount_type_range4" value = "{$v.id_currency|escape:'htmlall':'UTF-8'}">{$v.sign|escape:'htmlall':'UTF-8'}</option>
															{/foreach}
														</select>
													</div>
													<div class=" col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<input type="text" id="promo_code4" placeholder="{l s='Enter a Promo Code' mod='pushoncart'}" name="promo_code4">
													</div>
											</div>

											<!-- range 2 -->
											<div id="range_2" class="row {if $ps_version eq 1}well {/if} col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{l s='A cart total more than:' mod='pushoncart'}&nbsp;&nbsp;</div>
													<div class=" col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<div class="input-group">
															<span class="input-group-addon">{$currency_sign|escape:'htmlall':'UTF-8'}</span>
															<input type="text" id="more_than2" name="more_than2" class="form-control" style="width:50px;">
														</div>
														<small>{l s='(inclusive)' mod='pushoncart'}</small>
													</div>
													<div class="col-xs-12 col-sm-12 col-md-1 col-lg-1">&nbsp;&nbsp;{l s='is offered a' mod='pushoncart'}&nbsp;</div>
													<div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
														<input class="form-control" type="text" name="discount_range2" value = "10" maxlength = "3" style="width:50px;">
													</div>
													<div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
														<select class="form-control" name = "discount_type_range2">
															<option name="discount_type_range2" value = "0">%</option>
															{foreach $currency key=k item=v}
																<option name="discount_type_range2" value = "{$v.id_currency|escape:'htmlall':'UTF-8'}">{$v.sign|escape:'htmlall':'UTF-8'}</option>
															{/foreach}
														</select>
													</div>
													<div class=" col-xs-12 col-sm-12 col-md-2 col-lg-2">
														<input type="text" id="promo_code2" placeholder="{l s='Enter a Promo Code' mod='pushoncart'}" name="promo_code2">
													</div>
											</div>
											<div class="clear">&nbsp;</div>
											 <div>

												<label for="date_exp_yes" class="control-label"><span style="color:red">&nbsp;<b>*</b>&nbsp;</span>{l s='Choose an expiration date for the promotion:' mod='pushoncart'}&nbsp;</label>
												{if $browser eq 'Chrome'}
													<div class="input-group">
														<input type="date" id="date_exp_yes" name="date_exp_yes" class="form-control">
														<span class="input-group-addon">
															{if $ps_version eq 1}<i class="icon-calendar-empty">{else}<i class="icon-calendar-o">{/if}</i>
														</span>
													</div>
												{else}
													<div class="input-group">
														<input class="bootstrap datepicker form-control" autocomplete="off" name="date_exp_yes_alt" id="date_exp_yes_alt" placeholder="{l s='Click here to choose a date' mod='pushoncart'}">
														<span class="input-group-addon">
															{if $ps_version eq 1}<i class="icon-calendar-empty">{else}<i class="icon-calendar-o">{/if}</i>
														</span>
													</div>
												{/if}
											</div>
									</div>
		{*When NO is selected in step 3 of creating a promotion*}
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="ranges_no">
										<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
											<label for="discount" class="control-label">{l s='State the value and type of discount you would like to offer.' mod='pushoncart'}&nbsp;</label>
											<p>{l s='You can choose between a percentage discount or a fixed amount.' mod='pushoncart'}&nbsp;</p>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
												<input class="form-control" type="text" name="discount" value="10" maxlength="3" style="width:70px;">
												<select class="form-control" name="discount_type"  style="width:70px;">
													<option name="discount_type" value = "0">%</option>
													{foreach $currency key=k item=v}
														<option name="discount_type" value = "{$v.id_currency|escape:'htmlall':'UTF-8'}">{$v.sign|escape:'htmlall':'UTF-8'}</option>
													{/foreach}
												</select>
										</div>
										<div class="clear">&nbsp;</div>
										<hr/>
										<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
												<label for="promo_code" class="control-label">{l s='Create a promotion code:' mod='pushoncart'}&nbsp;</label>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
												<input type="text" id="promo_code" placeholder="{l s='Enter a Promo Code' mod='pushoncart'}" name="promo_code" style="width:200px;">
										</div>
										<div class="clear">&nbsp;</div>
										<hr/>
										<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
											<label for="date_exp_no" class="control-label"><span style="color:red">&nbsp;<b>*</b>&nbsp;</span>{l s='Choose an expiration date for the promotion:' mod='pushoncart'}&nbsp;</label>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
											{if $browser eq 'Chrome'}
												<div class="input-group">
													<input type="date" id="date_exp_no" name="date_exp_no" class="form-control">
													<span class="input-group-addon">
														{if $ps_version eq 1}<i class="icon-calendar-empty">{else}<i class="icon-calendar-o">{/if}</i>
													</span>
												</div>
											{else}
												<div class="input-group">
													<input class="bootstrap datepicker form-control" autocomplete="off" name="date_exp_no_alt" id="date_exp_yes_alt" placeholder="{l s='Click here to choose a date' mod='pushoncart'}">
													<span class="input-group-addon">
														{if $ps_version eq 1}<i class="icon-calendar-empty">{else}<i class="icon-calendar-o">{/if}</i>
													</span>
												</div>
											{/if}
										</div>
										<div class="clear">&nbsp;</div>
										<hr/>
										<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
													<label for="NO_cart_minimum" class="control-label">
														{l s='Cart minimum required for discount :' mod='pushoncart'}&nbsp;&nbsp;
													</label>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
												<span id="cart_minimum_switch" class="switch prestashop-switch input-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
														<input type="radio" name="NO_cart_minimum" id="cart_minimum_on" value="1" />
														<label for="cart_minimum_on" class="radioCheck">
																<i class="color_success"></i> {l s='Yes' mod='pushoncart'}
														</label>
														<input type="radio" name="NO_cart_minimum" id="cart_minimum_off" value="0" checked="checked" />
														<label for="cart_minimum_off" class="radioCheck">
																<i class="color_danger"></i> {l s='No' mod='pushoncart'}
														</label>
														<a class="slide-button btn"></a>
												</span>
										</div>
										<div class="clear">&nbsp;</div>
										<span id="cart_minimum">
											<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
												{l s='Enter the minimum amount a cart must have to benefit from the promotion :' mod='pushoncart'}&nbsp;&nbsp;
											</div>
											<div class="input-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
												<span class="input-group-addon">{$currency_sign|escape:'htmlall':'UTF-8'}</span><input type="text" id="more_than" name="more_than" class="form-control" style="width:100px;">
												<small>&nbsp;{l s='(inclusive)' mod='pushoncart'}</small>
											</div>

										</span>
									</div>
							</div>

							<p>{l s='Discounts are applied to products before tax.' mod='pushoncart'}&nbsp;</p>
							<div class="clear">&nbsp;</div>

							<input type="hidden" name="check" value="check" />
							<input type="hidden" name="browser" value="{$browser|escape:'htmlall':'UTF-8'}" />
							<center><input type="submit" name="submitModule" value="{l s='Update settings' mod='pushoncart'}" class="btn btn-info" /></center>
						</div>
					</form>
					<hr/>

					<h4><b>4. {l s='Conditions' mod='pushoncart'}</b></h4>
					<p>{l s='The discounted product on offer is based on the prospects\' activity on your e-shop in order to make sure promotional offers will always be adapted to prospects\' buying behaviour.' mod='pushoncart'}</p>
					<p>{l s='Thus, items will not be offered at a discounted price if :' mod='pushoncart'}</p>
						&nbsp;{l s=' 1. the client`s purchase history contains the product.' mod='pushoncart'}<br />
						&nbsp;{l s=' 2. the client has added the product to their current cart.' mod='pushoncart'}<br />
						&nbsp;{l s=' 3. there is a PushOnCart discount/cart rule registered in the current cart already.' mod='pushoncart'}<br />
						&nbsp;{l s=' You can choose if the PushOnCart promotions can be used simultaneously with Cart Rules that you have created outside this module.' mod='pushoncart'}<br />
					<p>{l s='If the prospect validates the proposed deal, the designated product will be in the order summary with other products already selected by the prospect.' mod='pushoncart'}</p>
					<hr/>
					<div id="discount_table_area" name="discount_table_area">
							<h4><b>5. {l s='Recap of Promotions' mod='pushoncart'}</b></h4>
							<p>{l s='PrestaShop advise you to create several promotional offers in order to make sure all your clients will receive an offer on their shopping cart summary.' mod='pushoncart'}</p>

							<div class="well">
								<p>{l s='How many promotions would you like to offer?' mod='pushoncart'}&nbsp;</p>
								<select id="promo_count" name="promo_count" class="form-control" onclick="update_promo_count();" style="width:100px;">
									<option value="1" {if $promo_count eq 1}selected="selected"{/if}>1</option>
									<option value="2"{if $promo_count eq 2}selected="selected"{/if}>2</option>
									<option value="3"{if $promo_count eq 3}selected="selected"{/if}>3</option>
									<option value="4"{if $promo_count eq 4}selected="selected"{/if}>4</option>
									<option value="5"{if $promo_count eq 5}selected="selected"{/if}>5</option>
									<option value="6"{if $promo_count eq 6}selected="selected"{/if}>6</option>
									<option value="7"{if $promo_count eq 7}selected="selected"{/if}>7</option>
									<option value="8"{if $promo_count eq 8}selected="selected"{/if}>8</option>
									<option value="9"{if $promo_count eq 9}selected="selected"{/if}>9</option>
									<option value="10"{if $promo_count eq 10}selected="selected"{/if}>10</option>
								</select>
								<p>{l s='Would you like your promotional offers to appear at random or keeping the priority order of the promotion table below?' mod='pushoncart'}&nbsp;</p>
								<div class="btn-group" data-toggle="buttons">
		                            <label style="width:150px;" for="promo_random" class="btn {if $promo_order eq 1}btn-info{else}btn-default{/if}" id="promo_random_label" onclick="update_div_class('promo_random_label');">
		                                    <input type="radio" name="promo_order" id="promo_random" value="1" {if $promo_order eq 1}checked='checked'{/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{l s='Random' mod='pushoncart'}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                            </label>
		                            <label style="width:150px;" for="promo_prioritize" class="btn {if $promo_order eq 0}btn-info{else}btn-default{/if}" id="promo_prioritize_label" onclick="update_div_class('promo_prioritize_label');">
		                                    <input type="radio" name="promo_order" id="promo_prioritize" value="0" {if $promo_order eq 0}checked='checked'{/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{l s='Prioritize' mod='pushoncart'}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                            </label>

		                            <!-- Customize Promo -->
		                            <div class="clear">&nbsp;</div>
		                            <div class="clear">&nbsp;</div>
		                            <p>{l s='Would you like to customize your promotional banner\s design?' mod='pushoncart'}&nbsp;</p>
		                            <label style="width:150px;" for="promo_custom_yes" class="btn {if $design_colors.PUSHONCART_CUSTOM eq 1}btn-info{else}btn-default{/if}" id="promo_custom_yes_label" onclick="update_div_class('promo_custom_yes_label');">
		                                <input type="radio" name="promo_custom" id="promo_custom_yes" value="1" {if $design_colors.PUSHONCART_CUSTOM eq 1}checked='checked'{/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{l s='Yes' mod='pushoncart'}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                            </label>
		                            <label style="width:150px;" for="promo_custom_no" class="btn {if $design_colors.PUSHONCART_CUSTOM eq 0}btn-info{else}btn-default{/if}" id="promo_custom_no_label" onclick="update_div_class('promo_custom_no_label');">
		                                <input type="radio" name="promo_custom" id="promo_custom_no" value="0" {if $design_colors.PUSHONCART_CUSTOM eq 0}checked='checked'{/if}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{l s='No' mod='pushoncart'}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                            </label>
		                            <div class="clear">&nbsp;</div>
		                            <small class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="design_help">{l s='Use the new tab displyed on the left. More design features will be added to future updates.' mod='pushoncart'}</small>
		                            <div class="clear">&nbsp;</div>

		                            <div class="input-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
			                            <div  style="width:300px;" class="alert alert-success" role="alert" id="promo_alert_success">
			                            	{l s='Change saved !' mod='pushoncart'}
			                            </div>
			                            <div  style="width:300px;" class="alert alert-danger" role="alert" id="promo_alert_fail">
			                            	{l s='There was a problem saving the change.' mod='pushoncart'}
			                            </div>
		                            </div>
		                            <div class="clear">&nbsp;</div>
		                        </div>
		                    </div>

							{include file="./table.tpl"}
							<div id="discountTable">
								<table id='discountTable' cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th><i style="cursor:pointer;" class="icon-eye-close icon-2x" onClick="hideDiscountInfo();"></i></th>
											<th>{l s='Product ID' mod='pushoncart'}</th>
											<th>{l s='Promotion Name' mod='pushoncart'}</th>
											<th>{l s='Cart Minimum' mod='pushoncart'}</th>
											<th>{l s='Cart Maximum' mod='pushoncart'}</th>
											<th colspan = "2">{l s='Discount' mod='pushoncart'}</th>
											<th>{l s='Code' mod='pushoncart'}</th>
											<th>{l s='Cart Rule ID' mod='pushoncart'}</th>
										</tr>
									</thead>
									<tbody id="discounts_table_body" name="discounts_table_body">
									</tbody>
								</table>
							</div>
							<div class="well well-sm">

								<h4><span class="label label-default">{l s='Note :' mod='pushoncart'}</span></h4>
								<p><b>{l s='Required :' mod='pushoncart'}&nbsp;&nbsp;</b><span style="color:red">&nbsp;<b>*</b></span>&nbsp;<span>{l s='This indicates the required fields that are necessary to create a promotion.' mod='pushoncart'}</span>
								<p><b>{l s='View :' mod='pushoncart'}&nbsp;&nbsp;</b><i class="icon-eye icon-2x"></i>&nbsp;{l s='View the promotion\'s details, including the different ranges and applied discounts.' mod='pushoncart'}<br/>
								<p><b>{l s='Hide :' mod='pushoncart'}&nbsp;&nbsp;</b><i class="icon-eye-close icon-2x"></i>&nbsp;{l s='Hide the promotion\'s details.' mod='pushoncart'}<br/>
								<p><b>{l s='Priority :' mod='pushoncart'}&nbsp;&nbsp;</b> <i class="icon-arrow-circle-up"></i> / <i class="icon-arrow-circle-down"></i>&nbsp;{l s='Priority promotions indicate which promotion applies where several are applicable.' mod='pushoncart'}<br/>
									{l s='For example, if you create a special offer on the product A and product B and the two promotions are applicable to the basket prospect, the system will supply whose priority is the highest.' mod='pushoncart'}</p>
								<p><b>{l s='Activate :' mod='pushoncart'}&nbsp;&nbsp;</b> <img src="../img/admin/enabled.gif"> / <img src="../img/admin/disabled.gif">&nbsp;{l s='Enables you to activate or deactivate a promotional offers without removing it. ' mod='pushoncart'}</p>
								<p><b>{l s='Delete :' mod='pushoncart'}&nbsp;&nbsp;</b> <i class="icon-trash-o fa-2x"></i>&nbsp;{l s='Remove a promotional offer. ' mod='pushoncart'}</p>
							</div>
					</div>
				</div>
			</div>
</div>

{literal}
<script>
var pushoncart_token = "{/literal}{$pushoncart_token|escape:'htmlall':'UTF-8'}{literal}";
var ps_version15 = "{/literal}{$ps_version15|escape:'htmlall':'UTF-8'}{literal}";

function hideDiscountInfo()
{
	$("#discounts_table_body").hide(300);
	$("#discountTable").hide(300);
}

$("#ranges").change(function(){
	switch (this.value)
	{
		case "1":
				$("#range_1").hide(500);
				$("#range_3").hide(500);
				$("#range_4").hide(500);
				$("#range_2").show(500);
				$("#cart_max_min_div").show();

				break;
		case "2":
				$("#range_1").show(500);
				$("#range_3").hide(500);
				$("#range_4").hide(500);
				$("#range_2").show(500);
				$("#cart_max_min_div").hide();
				break;
		case "3":
				$("#range_1").show(500);
				$("#range_3").show(500);
				$("#range_4").hide(500);
				$("#range_2").show(500);
				$("#cart_max_min_div").hide();
				break;
		case "4":
				$("#range_1").show(500);
				$("#range_3").show(500);
				$("#range_4").show(500);
				$("#range_2").show(500);
				$("#cart_max_min_div").hide();
				break;
	}
});

$("#cart_max_min").change(function(){
	switch (this.value)
	{
		case "0":
			$("#range_1").hide();
			$("#range_2").show();
			break;

		case "1":
			$("#range_2").hide();
			$("#range_1").show();
			break;
	}

});

$("#need_instance_on").change(function(){
	if (this.value == 1)
	{
			$("#ranges_no").hide(1000);
			$("#ranges_yes").show(1000);
			$("#cart_max_min_div").show();
	}
});
$("#need_instance_off").change(function(){
	if (this.value == 0)
	{
			$("#ranges_no").show(1000);
			$("#ranges_yes").hide(1000);
			$("#range_1").hide();
			$("#range_3").hide();
			$("#range_4").hide();
			$("#range_2").show();
			$("#ranges").val(1);
	}
});

$("#cart_minimum_on").change(function(){
	if (this.value == 1)
	{
		$("#cart_minimum").show(1000);
	}
	else
	{
		$("#cart_minimum").hide(1000);
	}

});
$("#cart_minimum_off").change(function(){
	if (this.value == 0)
	{
			$("#cart_minimum").hide(1000);
	}
	else
	{
			$("#cart_minimum").show(1000);
	}

});
</script>
{/literal}
