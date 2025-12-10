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

namespace InPost\Shipping;

use InPost\Shipping\DataProvider\PointDataProvider;
use InPost\Shipping\Traits\ErrorsTrait;
use InPostCartChoiceModel;
use InPostShipping;
use Validate;

class CartChoiceUpdater
{
    use ErrorsTrait;

    const TRANSLATION_SOURCE = 'CartChoiceUpdater';

    protected $module;
    protected $pointDataProvider;

    protected $weekendDelivery = false;
    protected $cashOnDelivery = false;
    protected $service;

    /** @var InPostCartChoiceModel */
    protected $cartChoice;

    public function __construct(
        InPostShipping $module,
        PointDataProvider $pointDataProvider
    ) {
        $this->module = $module;
        $this->pointDataProvider = $pointDataProvider;
    }

    public function getCartChoice()
    {
        return $this->cartChoice;
    }

    public function setCartChoice(InPostCartChoiceModel $cartChoice)
    {
        $this->cartChoice = $cartChoice;

        return $this;
    }

    public function setCarrierData(array $carrierData)
    {
        $this->weekendDelivery = $carrierData['weekendDelivery'];
        $this->cashOnDelivery = $carrierData['cashOnDelivery'];
        $this->service = $carrierData['service'];

        return $this;
    }

    public function setTargetPoint($pointId)
    {
        if ($pointId && $point = $this->pointDataProvider->getPointData($pointId)) {
            if ((!$this->weekendDelivery || $point->location_247) &&
                (!$this->cashOnDelivery || $point->payment_available)
            ) {
                $this->cartChoice->point = $point->getId();
            } else {
                $this->errors['locker'] = $this->module->l('Selected locker is not available for the selected delivery option.', self::TRANSLATION_SOURCE);
            }
        } else {
            $this->errors['locker'] = $this->module->l('Please select a locker.', self::TRANSLATION_SOURCE);
        }

        return $this;
    }

    public function setEmail($email)
    {
        if (!Validate::isEmail($email)) {
            $this->errors['email'] = $this->module->l('Provided email is invalid.', self::TRANSLATION_SOURCE);
        } else {
            $this->cartChoice->email = $email;
        }

        return $this;
    }

    public function setPhone($phone)
    {
        if (preg_match('/^[0-9]{9}$/', $phone = InPostCartChoiceModel::formatPhone($phone))) {
            $this->cartChoice->phone = $phone;
        } else {
            $this->errors['phone'] = $this->module->l('Provided phone number is invalid - should look like XXXXXXXXX (e.g. 123456789).', self::TRANSLATION_SOURCE);
        }

        return $this;
    }

    public function saveChoice($id_cart)
    {
        $this->cartChoice->service = $this->service;

        if (!Validate::isLoadedObject($this->cartChoice)) {
            $this->cartChoice->id = $id_cart;
            $this->cartChoice->add();
        } else {
            $this->cartChoice->update();
        }

        return $this;
    }
}
