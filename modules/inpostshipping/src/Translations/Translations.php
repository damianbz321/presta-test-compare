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

namespace InPost\Shipping\Translations;

use InPostShipping;

class Translations
{
    const TRANSLATION_SOURCE = 'Translations';

    protected $module;

    /**
     * @param InPostShipping $module
     */
    public function __construct(InPostShipping $module)
    {
        $this->module = $module;
    }

    public function getTranslations()
    {
        return [
            'menu' => $this->getNavMenuTranslations(),
            'general' => [
                'add' => $this->module->l('Add new', self::TRANSLATION_SOURCE),
                'edit' => $this->module->l('Edit', self::TRANSLATION_SOURCE),
                'yes' => $this->module->l('Yes', self::TRANSLATION_SOURCE),
                'no' => $this->module->l('No', self::TRANSLATION_SOURCE),
                'save' => $this->module->l('Save', self::TRANSLATION_SOURCE),
                'back' => $this->module->l('Back', self::TRANSLATION_SOURCE),
                'clear' => $this->module->l('Clear', self::TRANSLATION_SOURCE),
                'submit' => $this->module->l('Submit', self::TRANSLATION_SOURCE),
                'notApplicable' => $this->module->l('N/A', self::TRANSLATION_SOURCE),
            ],
            'warnings' => [
                'configurationMissing' => $this->module->l('API access configuration is missing', self::TRANSLATION_SOURCE),
                'configurationInvalid' => $this->module->l('API access configuration is invalid', self::TRANSLATION_SOURCE),
                'apiError' => $this->module->l('An error occurred when trying to retrieve organization data', self::TRANSLATION_SOURCE),
                'sandboxEnabled' => $this->module->l('Sandbox mode is enabled', self::TRANSLATION_SOURCE),
            ],
            'success' => [
                'configUpdated' => $this->module->l('The configuration has been successfully updated', self::TRANSLATION_SOURCE),
            ],
            'pages' => [
                'authorization' => [
                    'info' => $this->module->l('API access configuration info text', self::TRANSLATION_SOURCE),
                    'header' => $this->module->l('Production API', self::TRANSLATION_SOURCE),
                    'apiToken' => $this->module->l('Token', self::TRANSLATION_SOURCE),
                    'organizationId' => $this->module->l('Organization ID', self::TRANSLATION_SOURCE),
                ],
                'sandbox' => [
                    'header' => $this->module->l('Sandbox API', self::TRANSLATION_SOURCE),
                    'enable' => $this->module->l('Enable sandbox mode', self::TRANSLATION_SOURCE),
                ],
                'organization' => [
                    'companyName' => $this->module->l('Company name', self::TRANSLATION_SOURCE),
                    'firstName' => $this->module->l('First name', self::TRANSLATION_SOURCE),
                    'lastName' => $this->module->l('Last name', self::TRANSLATION_SOURCE),
                    'email' => $this->module->l('Email address', self::TRANSLATION_SOURCE),
                    'phone' => $this->module->l('Phone number', self::TRANSLATION_SOURCE),
                    'street' => $this->module->l('Street', self::TRANSLATION_SOURCE),
                    'buildingNumber' => $this->module->l('Building number', self::TRANSLATION_SOURCE),
                    'city' => $this->module->l('City', self::TRANSLATION_SOURCE),
                    'postCode' => $this->module->l('Postal code', self::TRANSLATION_SOURCE),
                    'autofill' => $this->module->l('Fill with organization data', self::TRANSLATION_SOURCE),
                ],
                'sending' => [
                    'method' => $this->module->l('Default sending method', self::TRANSLATION_SOURCE),
                    'dispatchPoint' => $this->module->l('Default dispatch point', self::TRANSLATION_SOURCE),
                    'locker' => $this->module->l('Default locker', self::TRANSLATION_SOURCE),
                    'pop' => $this->module->l('Default POP', self::TRANSLATION_SOURCE),
                    'noDispatchPoints' => $this->module->l('You do not have any dispatch points yet', self::TRANSLATION_SOURCE),
                ],
                'services' => [
                    'editCarrier' => $this->module->l('Go to carrier settings', self::TRANSLATION_SOURCE),
                    'delete' => $this->module->l('Delete', self::TRANSLATION_SOURCE),
                    'column' => [
                        'name' => $this->module->l('Service name', self::TRANSLATION_SOURCE),
                        'carrier' => $this->module->l('Carrier', self::TRANSLATION_SOURCE),
                        'weekendDelivery' => $this->module->l('Weekend delivery', self::TRANSLATION_SOURCE),
                        'cod' => $this->module->l('Cash on delivery', self::TRANSLATION_SOURCE),
                        'active' => $this->module->l('Active', self::TRANSLATION_SOURCE),
                        'actions' => $this->module->l('Actions', self::TRANSLATION_SOURCE),
                    ],
                    'form' => [
                        'new' => $this->module->l('Add a new service', self::TRANSLATION_SOURCE),
                        'edit' => $this->module->l('Edit a service', self::TRANSLATION_SOURCE),
                        'service' => $this->module->l('Service', self::TRANSLATION_SOURCE),
                        'cod' => $this->module->l('Cash on delivery', self::TRANSLATION_SOURCE),
                        'weekendDelivery' => $this->module->l('Weekend delivery', self::TRANSLATION_SOURCE),
                        'template' => $this->module->l('Default dimension template', self::TRANSLATION_SOURCE),
                        'existing' => $this->module->l('Use an existing carrier', self::TRANSLATION_SOURCE),
                        'carrier' => $this->module->l('Existing carrier', self::TRANSLATION_SOURCE),
                        'carrierName' => $this->module->l('New carrier name', self::TRANSLATION_SOURCE),
                        'updateSettings' => $this->module->l('Update the carrier settings', self::TRANSLATION_SOURCE),
                        'updateSettingsDescription' => $this->module->l('Update the carrier settings description', self::TRANSLATION_SOURCE),
                        'copyServiceName' => $this->module->l('Copy service name', self::TRANSLATION_SOURCE),
                        'dimensions' => $this->module->l('Default shipment dimensions', self::TRANSLATION_SOURCE),
                        'length' => $this->module->l('Length', self::TRANSLATION_SOURCE),
                        'height' => $this->module->l('Height', self::TRANSLATION_SOURCE),
                        'width' => $this->module->l('Width', self::TRANSLATION_SOURCE),
                        'weight' => $this->module->l('Weight', self::TRANSLATION_SOURCE),
                    ],
                ],
                'weekendDelivery' => [
                    'startDay' => $this->module->l('Available from weekday', self::TRANSLATION_SOURCE),
                    'startHour' => $this->module->l('Available from hour', self::TRANSLATION_SOURCE),
                    'endDay' => $this->module->l('Available to weekday', self::TRANSLATION_SOURCE),
                    'endHour' => $this->module->l('Available to hour', self::TRANSLATION_SOURCE),
                    'hour' => $this->module->l('Hour', self::TRANSLATION_SOURCE),
                    'minutes' => $this->module->l('Minutes', self::TRANSLATION_SOURCE),
                ],
                'szybkieZwroty' => [
                    'header' => $this->module->l('Szybkie Zwroty', self::TRANSLATION_SOURCE),
                    'signUp' => $this->module->l('Signup information placeholder', self::TRANSLATION_SOURCE),
                    'clickHere' => $this->module->l('Click here', self::TRANSLATION_SOURCE),
                    'storeName' => $this->module->l('Store name', self::TRANSLATION_SOURCE),
                    'formLink' => $this->module->l('Check if the link is correct', self::TRANSLATION_SOURCE),
                ],
            ],
        ];
    }

    public function getNavMenuTranslations()
    {
        return [
            'authorization' => $this->module->l('Authorization', self::TRANSLATION_SOURCE),
            'organization' => $this->module->l('Sender details', self::TRANSLATION_SOURCE),
            'sending' => $this->module->l('Sending method', self::TRANSLATION_SOURCE),
            'services' => $this->module->l('Shipping services', self::TRANSLATION_SOURCE),
            'weekendDelivery' => $this->module->l('Weekend delivery', self::TRANSLATION_SOURCE),
            'dispatchPoints' => $this->module->l('Dispatch points', self::TRANSLATION_SOURCE),
            'szybkieZwroty' => $this->module->l('Szybkie Zwroty', self::TRANSLATION_SOURCE),
        ];
    }
}
