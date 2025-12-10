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

use InPost\Shipping\CarrierConfigurationUpdater;
use InPost\Shipping\CarrierUpdater;
use InPost\Shipping\Traits\ErrorsTrait;
use InPostCarrierModel;
use InPostShipping;
use Validate;

class UpdateServiceHandler
{
    use ErrorsTrait;

    const TRANSLATION_SOURCE = 'UpdateServiceHandler';

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

        if (Validate::isLoadedObject($inpostCarrier = new InPostCarrierModel($request['carrierReference']))) {
            if ($request['updateSettings']) {
                $carrier = $this->carrierUpdater->update(
                    $inpostCarrier->getCarrier(),
                    $request['service'],
                    $request['cashOnDelivery'],
                    $request['weekendDelivery']
                );

                if (!$carrier) {
                    $this->setErrors($this->carrierUpdater->getErrors());
                }
            }

            if (!$this->configurationUpdater->update($request)) {
                $this->errors = array_merge(
                    $this->errors,
                    $this->configurationUpdater->getErrors()
                );
            }

            if (!$this->hasErrors()) {
                $inpostCarrier->service = $request['service'];
                $inpostCarrier->cod = $request['cashOnDelivery'];
                $inpostCarrier->weekend_delivery = $request['weekendDelivery'];

                $inpostCarrier->update();

                return $inpostCarrier;
            }
        } else {
            $this->addError(sprintf(
                $this->module->l('No service associated with carrier with reference %s', self::TRANSLATION_SOURCE),
                $request['carrierReference']
            ));
        }

        return false;
    }
}
