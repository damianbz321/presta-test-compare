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

namespace InPost\Shipping\Handler\ShippingService;

use Carrier;
use InPost\Shipping\CarrierConfigurationUpdater;
use InPost\Shipping\CarrierUpdater;
use InPost\Shipping\Traits\ErrorsTrait;
use InPostCarrierModel;
use InPostShipping;

class AddServiceHandler
{
    use ErrorsTrait;

    const TRANSLATION_SOURCE = 'AddServiceHandler';

    protected $module;
    protected $carrierUpdater;
    protected $configurationUpdater;

    public function __construct(
        InPostShipping $module,
        CarrierUpdater $carrierUpdater,
        CarrierConfigurationUpdater $configurationUpdater
    ) {
        $this->module = $module;
        $this->carrierUpdater = $carrierUpdater;
        $this->configurationUpdater = $configurationUpdater;
    }

    public function handle(array $request)
    {
        $this->resetErrors();

        $inpostCarrier = new InPostCarrierModel();
        $inpostCarrier->service = $request['service'];
        $inpostCarrier->cod = $request['cashOnDelivery'];
        $inpostCarrier->weekend_delivery = $request['weekendDelivery'];

        if (isset($request['carrierReference']) && $request['carrierReference']) {
            $updateSettings = $request['updateSettings'];

            if (!$carrier = Carrier::getCarrierByReference($request['carrierReference'])) {
                $this->errors['carrierReference'] = sprintf(
                    $this->module->l('Could not find carrier with reference %s', self::TRANSLATION_SOURCE),
                    $request['carrierReference']
                );
            }
        } else {
            $updateSettings = true;

            $carrier = new Carrier();
            $carrier->name = $request['carrierName'];
        }

        if (!$this->configurationUpdater->update($request)) {
            $this->errors = array_merge(
                $this->errors,
                $this->configurationUpdater->getErrors()
            );
        }

        if (empty($this->errors)) {
            $carrier = $this->carrierUpdater->update(
                $carrier,
                $request['service'],
                $request['cashOnDelivery'],
                $request['weekendDelivery'],
                $updateSettings
            );

            if ($carrier) {
                $inpostCarrier->id = $carrier->id_reference;
                $inpostCarrier->add();

                return $inpostCarrier;
            } else {
                $this->setErrors($this->carrierUpdater->getErrors());
            }
        }

        return false;
    }
}
