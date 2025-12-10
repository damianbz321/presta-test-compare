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
<div class="tab tab-pane" id="inpostshipping">
  <div class="card card-details mb-0">
    <div class="card-header d-none d-print-block">
      {l s='InPost Shipments' mod='inpostshipping'} (<span class="count">{$inPostShipments|count}</span>)
    </div>
    <div class="card-body">
      <div class="form-horizontal">
        <div class="table-responsive">
          <table class="table">
            <thead>
            <tr>
              <th>{l s='Service' mod='inpostshipping'}</th>
              <th>{l s='Shipment number' mod='inpostshipping'}</th>
              <th>{l s='State' mod='inpostshipping'}</th>
              <th class="text-right">{l s='Price' mod='inpostshipping'}</th>
              <th>{l s='Created at' mod='inpostshipping'}</th>
              <th class="text-right product_actions d-print-none">
                {l s='Actions' mod='inpostshipping'}
              </th>
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
                    <span class="text-primary cursor-pointer" data-toggle="pstooltip" data-boundary="window" data-original-title="{$shipment.status.description|escape:'html':'UTF-8'}">
                      {$shipment.status.title|escape:'html':'UTF-8'}
                    </span>
                  {else}
                    {$shipment.status.title|escape:'html':'UTF-8'}
                  {/if}
                </td>
                <td class="text-right">
                  {if $shipment.price}{$shipment.price|escape:'html':'UTF-8'}{else}--{/if}
                </td>
                <td>
                  {$shipment.date_add|escape:'html':'UTF-8'}
                </td>
                <td class="d-print-none action-type">
                  <div class="btn-group-action text-right">
                    <div class="btn-group">
                      <a href="{$shipment.viewUrl|escape:'html':'UTF-8'}"
                         class="btn tooltip-link js-view-inpost-shipment-details"
                         data-toggle="pstooltip"
                         data-original-title="{l s='Details' mod='inpostshipping'}"
                         data-id-shipment="{$shipment.id|intval}"
                      >
                        <i class="material-icons">zoom_in</i>
                      </a>
                      <button class="btn btn-link dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                      <div class="dropdown-menu dropdown-menu-right">
                        {foreach from=$shipment.actions key=key item=action name=actions}
                          <a class="btn tooltip-link js-{$key|escape:'html':'UTF-8'} dropdown-item"
                             href="{$action.url|escape:'html':'UTF-8'}"
                             data-id-shipment="{$shipment.id|intval}"
                          >
                            <i class="material-icons">
                              {if $action.icon === 'truck'}
                                local_shipping
                              {else}
                                {$action.icon|escape:'html':'UTF-8'}
                              {/if}
                            </i>
                            {$action.text|escape:'html':'UTF-8'}
                          </a>
                        {/foreach}
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            {/foreach}
            </tbody>
          </table>

          <div class="row no-gutters d-print-none">
            <div class="col-sm-12 text-right">
              <a class="btn btn-secondary" href="{$inPostShipmentsListUrl|escape:'html':'UTF-8'}">
                {l s='Go to shipments list' mod='inpostshipping'}
              </a>
              <button class="btn btn-primary" data-toggle="modal" data-target="#inpost-create-shipment-modal">
                {l s='New shipment' mod='inpostshipping'}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
