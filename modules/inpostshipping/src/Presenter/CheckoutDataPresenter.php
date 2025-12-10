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

namespace InPost\Shipping\Presenter;

use Address;
use Context;
use InPost\Shipping\DataProvider\CustomerChoiceDataProvider;
use InPost\Shipping\DataProvider\PointDataProvider;

class CheckoutDataPresenter
{
    protected $customerChoiceDataProvider;
    protected $pointDataProvider;

    protected $context;

    public function __construct(
        CustomerChoiceDataProvider $customerChoiceDataProvider,
        PointDataProvider $pointDataProvider
    ) {
        $this->customerChoiceDataProvider = $customerChoiceDataProvider;
        $this->pointDataProvider = $pointDataProvider;

        $this->context = Context::getContext();
    }

    public function present(array $carrierData, array $sessionData)
    {
        $carrierData['locker'] = null;
        $carrierData['errors'] = $sessionData ? $sessionData['errors'] : [];

        if ($choice = $this->customerChoiceDataProvider->getDataByCartId($this->context->cart->id)) {
            $carrierData['email'] = $sessionData ? $sessionData['email'] : $choice->email;
            $carrierData['phone'] = $sessionData ? $sessionData['phone'] : $choice->phone;

            if ($choice->point) {
                if ($point = $this->pointDataProvider->getPointData($choice->point)) {
                    if ((!$carrierData['weekendDelivery'] || $point->location_247) &&
                        (!$carrierData['cashOnDelivery'] || $point->payment_available)
                    ) {
                        $carrierData['locker'] = $point->toArray();
                    }
                }
            }
        } elseif ($sessionData) {
            $carrierData['email'] = $sessionData['email'];
            $carrierData['phone'] = $sessionData['phone'];
        }

        if (!isset($carrierData['email']) || empty($carrierData['email'])) {
            $carrierData['email'] = $this->context->customer->email;
        }

        if (!isset($carrierData['phone']) || empty($carrierData['phone'])) {
            $address = new Address($this->context->cart->id_address_delivery);
            $carrierData['phone'] = $address->phone_mobile ?: $address->phone;
        }

        return $carrierData;
    }
}
