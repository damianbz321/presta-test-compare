<?php
/**
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
 */

namespace InPost\Shipping\Hook;

use InPost\Shipping\DataProvider\CustomerChoiceDataProvider;
use InPost\Shipping\DataProvider\PointDataProvider;
use InPost\Shipping\Presenter\PointAddressPresenter;
use InPost\Shipping\ShipX\Resource\Service;
use Order;

class Mail extends AbstractHook
{
    const HOOK_LIST = [
        'actionGetExtraMailTemplateVars',
    ];

    public function hookActionGetExtraMailTemplateVars($params)
    {
        /** @var Order $order */
        if ($params['template'] === 'new_order' &&
            isset($params['template_vars']['{order_name}']) &&
            $order = Order::getByReference($params['template_vars']['{order_name}'])->getFirst()
        ) {
            /** @var CustomerChoiceDataProvider $customerChoiceDataProvider */
            $customerChoiceDataProvider = $this->module->getService('inpost.shipping.data_provider.customer_choice');

            if (($customerChoice = $customerChoiceDataProvider->getDataByCartId($order->id_cart)) &&
                $customerChoice->service === Service::INPOST_LOCKER_STANDARD
            ) {
                /** @var PointDataProvider $pointDataProvider */
                $pointDataProvider = $this->module->getService('inpost.shipping.data_provider.point');

                if ($point = $pointDataProvider->getPointData($customerChoice->point)) {
                    /** @var PointAddressPresenter $pointAddressPresenter */
                    $pointAddressPresenter = $this->module->getService('inpost.shipping.presenter.point_address');

                    $params['extra_template_vars'] = array_merge($params['extra_template_vars'], [
                        '{delivery_block_txt}' => $pointAddressPresenter->present($point, true, $params['id_lang']),
                        '{delivery_block_html}' => $pointAddressPresenter->present($point, false, $params['id_lang']),
                    ]);
                }
            }
        }
    }
}
