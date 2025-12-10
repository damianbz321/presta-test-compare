{extends file='_partials/form-fields.tpl'}

{block name='form_field_item_radio'}
    {foreach from=$field.availableValues item="label" key="value"}
        <div>
            <label class="radio">
              <span class="custom-radio">
                <input
                        name="{$field.name}"
                        type="radio"
                        value="{$value}"
                        {if $field.required}required{/if}
                        {if $value eq $field.value} checked {/if}
                >
                <span></span>
              </span>
                {$label nofilter}
            </label>
        </div>
    {/foreach}
{/block}