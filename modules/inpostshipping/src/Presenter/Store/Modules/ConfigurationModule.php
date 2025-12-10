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

namespace InPost\Shipping\Presenter\Store\Modules;

use InPost\Shipping\Configuration\SendingConfiguration;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\Configuration\SzybkieZwrotyConfiguration;
use InPost\Shipping\Presenter\Store\PresenterInterface;

class ConfigurationModule implements PresenterInterface
{
    protected $shipXConfiguration;
    protected $sendingConfiguration;
    protected $szybkieZwrotyConfiguration;

    public function __construct(
        ShipXConfiguration $shipXConfiguration,
        SendingConfiguration $sendingConfiguration,
        SzybkieZwrotyConfiguration $szybkieZwrotyConfiguration
    ) {
        $this->shipXConfiguration = $shipXConfiguration;
        $this->sendingConfiguration = $sendingConfiguration;
        $this->szybkieZwrotyConfiguration = $szybkieZwrotyConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function present()
    {
        return [
            'config' => [
                'api' => [
                    'token' => $this->shipXConfiguration->getProductionApiToken(),
                    'organizationId' => $this->shipXConfiguration->getProductionOrganizationId() ?: '',
                    'sandbox' => [
                        'enabled' => $this->shipXConfiguration->isSandboxModeEnabled(),
                        'token' => $this->shipXConfiguration->getSandboxApiToken(),
                        'organizationId' => $this->shipXConfiguration->getSandboxOrganizationId() ?: '',
                    ],
                ],
                'sending' => [
                    'sender' => $this->sendingConfiguration->getSenderDetails(),
                    'defaults' => [
                        'sendingMethod' => $this->sendingConfiguration->getDefaultSendingMethod(),
                        'locker' => $this->sendingConfiguration->getDefaultLocker(),
                        'pop' => $this->sendingConfiguration->getDefaultPOP(),
                        'dispatchPoint' => $this->sendingConfiguration->getDefaultDispatchPointId(),
                    ],
                ],
                'szybkieZwroty' => [
                    'storeName' => $this->szybkieZwrotyConfiguration->getStoreName(),
                    'urlTemplate' => $this->szybkieZwrotyConfiguration->getUrlTemplate(),
                ],
            ],
        ];
    }
}
