{**
 * Copyright 2021-2021 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2021 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 *}
<div class="tab-pane" id="inpostshipping">
  <h4 class="visible-print">{l s='InPost Shipments' mod='inpostshipping'}</h4>
    <div class="form-horizontal">
      <div class="table-responsive">
        <table class="table">
          <thead>
          <tr>
            <th>
              <span class="title_box">{l s='Service' mod='inpostshipping'}</span>
            </th>
            <th>
              <span class="title_box">{l s='Shipment number' mod='inpostshipping'}</span>
            </th>
            <th>
              <span class="title_box">{l s='State' mod='inpostshipping'}</span>
            </th>
            <th>
              <span class="title_box">{l s='Price' mod='inpostshipping'}</span>
            </th>
            <th>
              <span class="title_box">{l s='Created at' mod='inpostshipping'}</span>
            </th>
            <th></th>
          </tr>
          </thead>
          <tbody>
          {foreach $inPostShipments as $shipment}
            <tr>
              <td>
                {$shipment.service|escape:'html':'UTF-8'}
              </td>
              <td>
                {$shipment.tracking_number|escape:'html':'UTF-8'}
              </td>
              <td>
                {if $shipment.status.description}
                  <a data-toggle="tooltip" title="{$shipment.status.description|escape:'html':'UTF-8'}">
                    {$shipment.status.title|escape:'html':'UTF-8'}
                  </a>
                {else}
                  {$shipment.status.title|escape:'html':'UTF-8'}
                {/if}
              </td>
              <td>
                {if $shipment.price}{$shipment.price|escape:'html':'UTF-8'}{else}--{/if}
              </td>
              <td>
                {$shipment.date_add|escape:'html':'UTF-8'}
              </td>
              <td class="text-right">
                <div class="btn-group-action">
                  <div class="btn-group pull-right">
                    <a href="{$shipment.viewUrl|escape:'html':'UTF-8'}"
                       class="btn btn-default js-view-inpost-shipment-details"
                       data-id-shipment="{$shipment.id|intval}"
                    >
                      <i class="icon-eye"></i>
                      {l s='Details' mod='inpostshipping'}
                    </a>

                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <i class="icon-caret-down"></i>&nbsp;
                    </button>

                    <ul class="dropdown-menu" role="menu">
                      {foreach from=$shipment.actions key=key item=action name=actions}
                        <li>
                          <a href="{$action.url|escape:'html':'UTF-8'}"
                             class="js-{$key|escape:'html':'UTF-8'}"
                             data-id-shipment="{$shipment.id|intval}"
                          >
                            <i class="icon-{$action.icon|escape:'html':'UTF-8'}"></i>
                            {$action.text|escape:'html':'UTF-8'}
                          </a>
                        </li>
                      {/foreach}
                    </ul>
                  </div>
                </div>
              </td>
            </tr>
          {/foreach}
          </tbody>
        </table>

        <div class="row">
          <a class="btn btn-default" href="{$inPostShipmentsListUrl|escape:'html':'UTF-8'}">
            {l s='Go to shipments list' mod='inpostshipping'}
          </a>
          <button class="btn btn-primary" data-toggle="modal" data-target="#inpost-create-shipment-modal">
            {l s='New shipment' mod='inpostshipping'}
          </button>
        </div>
      </div>
    </div>
</div>
