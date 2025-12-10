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
use AddressFormat;
use Country;
use InPost\Shipping\DataProvider\LanguageDataProvider;
use InPost\Shipping\ShipX\Resource\Point;
use InPostShipping;

class PointAddressPresenter
{
    const TRANSLATION_SOURCE = 'PointAddressPresenter';

    protected $module;
    protected $languageDataProvider;

    protected $countryId;

    public function __construct(
        InPostShipping $module,
        LanguageDataProvider $languageDataProvider
    ) {
        $this->module = $module;
        $this->languageDataProvider = $languageDataProvider;
    }

    public function present(Point $point, $text = false, $id_lang = null)
    {
        return AddressFormat::generateAddress(
            $this->getPointAddress($point, $id_lang),
            [],
            $text ? "\n" : '<br/>'
        );
    }

    protected function getPointAddress(Point $point, $id_lang)
    {
        $locale = $id_lang !== null
            ? $this->languageDataProvider->getLocaleById($id_lang)
            : null;

        $addressDetails = $point->address_details;

        $address = new Address();
        $address->firstname = sprintf(
            $this->module->l('InPost Locker: %s', self::TRANSLATION_SOURCE, $locale),
            $point->name
        );
        $address->lastname = '';
        $address->address1 = $this->getAddressLine($addressDetails);
        $address->city = $addressDetails['city'];
        $address->postcode = $addressDetails['post_code'];
        $address->id_country = $this->getCountryId() ?: null;

        return $address;
    }

    protected function getAddressLine($addressDetails)
    {
        $buildingNumber = $addressDetails['building_number'];
        $flatNumber = $addressDetails['flat_number'];

        return sprintf(
            '%s%s%s',
            $addressDetails['street'],
            $buildingNumber ? ' ' . $buildingNumber : '',
            $flatNumber ? ' / ' . $flatNumber : ''
        );
    }

    protected function getCountryId()
    {
        if (!isset($this->countryId)) {
            $this->countryId = Country::getByIso('PL');
        }

        return $this->countryId;
    }
}
