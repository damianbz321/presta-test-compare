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
<form id="configuration_form"
      action="{$current}&{if !empty($submit_action)}{$submit_action}=1{/if}&token={$token}" method="post"
      enctype="multipart/form-data">
    {foreach $fields as $f => $fieldset}
		<fieldset id="fieldset_{$f}">
            {foreach $fieldset.form as $key => $field}
                {if $key == 'legend'}
					<legend>
                        {if isset($field.image)}<img src="{$field.image}" alt="{$field.title|escape:'htmlall':'UTF-8'}" />{/if}
                        {$field.title}
					</legend>
                {elseif $key == 'input'}
                    {foreach $field as $input}
                        {if $input.type == 'hidden'}
							<input type="hidden" name="{$input.name}" id="{$input.name}" value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}"/>
                        {else}
                            {block name="label"}
                                {if isset($input.label)}<label>{$input.label} </label>{/if}
                            {/block}
                            {block name="field"}
								<div class="margin-form">
                                    {block name="input"}
										{if $input.type == 'text'}
											{assign var='value_text' value=$fields_value[$input.name]}
                                            {if $input.name == 'ETS_MC_ACCESS_TOKEN'}
                                                <div class="ETS_MC_ACCESS_TOKEN_group">
                                            {/if}
											<input type="text" 
													name="{$input.name}"
													id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
													value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
													class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class}{/if}"
													{if isset($input.size)}size="{$input.size}"{/if}
													{if isset($input.maxlength)}maxlength="{$input.maxlength}"{/if}
													{if isset($input.class)}class="{$input.class}"{/if}
													{if isset($input.readonly) && $input.readonly}readonly="readonly"{/if}
													{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
                                                    {if isset($input.title) && $input.title}title="{$input.title}"{/if}
													{if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if} />
                                                    {if $input.name == 'ETS_MC_ACCESS_TOKEN'}
                                                        <span class="data_copied">{l s='Copied' mod='ets_migrate_connector'}</span>
		                                              </div>
                                                    {/if}
                                                    {if $input.name == 'ETS_MC_ACCESS_TOKEN'}
                                                        <span class="input-group-btn">
                                                            <a id="ets_mc_gencode" class="btn btn-default" href="#">
                                                                <svg class="w_14 h_14" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path
                                                			                d="M504.971 359.029c9.373 9.373 9.373 24.569 0 33.941l-80 79.984c-15.01 15.01-40.971 4.49-40.971-16.971V416h-58.785a12.004 12.004 0 0 1-8.773-3.812l-70.556-75.596 53.333-57.143L352 336h32v-39.981c0-21.438 25.943-31.998 40.971-16.971l80 79.981zM12 176h84l52.781 56.551 53.333-57.143-70.556-75.596A11.999 11.999 0 0 0 122.785 96H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12zm372 0v39.984c0 21.46 25.961 31.98 40.971 16.971l80-79.984c9.373-9.373 9.373-24.569 0-33.941l-80-79.981C409.943 24.021 384 34.582 384 56.019V96h-58.785a12.004 12.004 0 0 0-8.773 3.812L96 336H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h110.785c3.326 0 6.503-1.381 8.773-3.812L352 176h32z"/></svg>
                                                             {l s='Generate' mod='ets_migrate_connector'}</a>
                                                        </span>
                                                    {/if}
											{if isset($input.suffix)}{$input.suffix}{/if}
											{if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
										{elseif $input.type == 'select'}
											{if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
												{$input.empty_message}
												{$input.required = false}
												{$input.desc = null}
											{else}
												<select name="{$input.name}" class="{if isset($input.class)}{$input.class}{/if}"
														id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
														{if isset($input.multiple)}multiple="multiple" {/if}
														{if isset($input.size)}size="{$input.size}"{/if}
														{if isset($input.onchange)}onchange="{$input.onchange}"{/if}>
													{if isset($input.options.default)}
														<option value="{$input.options.default.value}">{$input.options.default.label}</option>
													{/if}
													{if isset($input.options.optiongroup)}
														{foreach $input.options.optiongroup.query AS $optiongroup}
															<optgroup label="{$optiongroup[$input.options.optiongroup.label]}">
																{foreach $optiongroup[$input.options.options.query] as $option}
																	<option value="{$option[$input.options.options.id]}"
																		{if isset($input.multiple)}
																			{foreach $fields_value[$input.name] as $field_value}
																				{if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
																			{/foreach}
																		{else}
																			{if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
																		{/if}
																	>{$option[$input.options.options.name]}</option>
																{/foreach}
															</optgroup>
														{/foreach}
													{else}
														{foreach $input.options.query AS $option}
															{if is_object($option)}
																<option value="{$option->$input.options.id}"
																	{if isset($input.multiple)}
																		{foreach $fields_value[$input.name] as $field_value}
																			{if $field_value == $option->$input.options.id}
																				selected="selected"
																			{/if}
																		{/foreach}
																	{else}
																		{if $fields_value[$input.name] == $option->$input.options.id}
																			selected="selected"
																		{/if}
																	{/if}
																>{$option->$input.options.name}</option>
															{elseif $option == "-"}
																<option value="">--</option>
															{else}
																<option value="{$option[$input.options.id]}"
																	{if isset($input.multiple)}
																		{foreach $fields_value[$input.name] as $field_value}
																			{if $field_value == $option[$input.options.id]}
																				selected="selected"
																			{/if}
																		{/foreach}
																	{else}
																		{if $fields_value[$input.name] == $option[$input.options.id]}
																			selected="selected"
																		{/if}
																	{/if}
																>{$option[$input.options.name]}</option>

															{/if}
														{/foreach}
													{/if}
												</select>
												{if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
											{/if}
										{elseif $input.type == 'radio'}
											{foreach $input.values as $value}
												<input type="radio" name="{$input.name}" id="{$value.id}" value="{$value.value|escape:'htmlall':'UTF-8'}"
														{if $fields_value[$input.name] == $value.value}checked="checked"{/if}
														{if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
												<label {if isset($input.class)}class="{$input.class}"{/if} for="{$value.id}">
												 {if isset($input.is_bool) && $input.is_bool == true}
													{if $value.value == 1}
														<img src="../img/admin/enabled.gif" alt="{$value.label}" title="{$value.label}" />
													{else}
														<img src="../img/admin/disabled.gif" alt="{$value.label}" title="{$value.label}" />
													{/if}
												 {else}
													{$value.label}
												 {/if}
												</label>
												{if isset($input.br) && $input.br}<br />{/if}
												{if isset($value.p) && $value.p}<p>{$value.p}</p>{/if}
											{/foreach}
										{elseif $input.type == 'textarea'}
											{if isset($input.lang) AND $input.lang}
												<div class="translatable">
													{foreach $languages as $language}
														<div class="lang_{$language.id_lang}" id="{$input.name}_{$language.id_lang}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
															<textarea cols="{$input.cols}" rows="{$input.rows}" name="{$input.name}_{$language.id_lang}" {if isset($input.autoload_rte) && $input.autoload_rte}class="rte autoload_rte {if isset($input.class)}{$input.class}{/if}"{/if} >{$fields_value[$input.name][$language.id_lang]|escape:'htmlall':'UTF-8'}</textarea>
														</div>
													{/foreach}
												</div>
											{else}
												<textarea name="{$input.name}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}" cols="{$input.cols}" rows="{$input.rows}" {if isset($input.autoload_rte) && $input.autoload_rte}class="rte autoload_rte {if isset($input.class)}{$input.class}{/if}"{/if}>{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}</textarea>
											{/if}
										{elseif $input.type == 'checkbox'}
											{foreach $input.values.query as $value}
												{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
												<input type="checkbox"
													name="{$id_checkbox}"
													id="{$id_checkbox}"
													class="{if isset($input.class)}{$input.class}{/if}"
													{if isset($value.val)}value="{$value.val|escape:'htmlall':'UTF-8'}"{/if}
													{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]}checked="checked"{/if} />
												<label for="{$id_checkbox}" class="t"><strong>{$value[$input.values.name]}</strong></label><br />
											{/foreach}
										{/if}
	                                    {if isset($input.required) && $input.required && $input.type != 'radio'}
											<sup>*</sup>
	                                    {/if}
                                    {/block}{* end block input *}
                                    {block name="description"}
                                        {if isset($input.desc) && !empty($input.desc)}
											<p class="preference_description">
                                                {if is_array($input.desc)}
                                                    {foreach $input.desc as $p}
                                                        {if is_array($p)}
															<span id="{$p.id}">{$p.text}</span>
															<br/>
                                                        {else}
                                                            {$p}
															<br/>
                                                        {/if}
                                                    {/foreach}
                                                {else}
                                                    {$input.desc}
                                                {/if}
											</p>
                                        {/if}
                                    {/block}
                                    {if isset($input.lang) && isset($languages)}
										<div class="clear"></div>
                                    {/if}
								</div>
								<div class="clear"></div>
                            {/block}
                        {/if}
                    {/foreach}
                {elseif $key == 'submit'}
					<div class="margin-form">
						<input type="submit"
						       id="{if isset($field.id)}{$field.id}{else}{$table}_form_submit_btn{/if}"
						       value="{$field.title}"
						       name="{if isset($field.name)}{$field.name}{else}{$submit_action}{/if}{if isset($field.stay) && $field.stay}AndStay{/if}"
                               {if isset($field.class)}class="{$field.class}"{/if} />
					</div>
                {/if}
            {/foreach}
		</fieldset>
    {/foreach}
</form>