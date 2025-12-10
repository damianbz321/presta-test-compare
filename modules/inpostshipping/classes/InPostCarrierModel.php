<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use InPost\Shipping\ShipX\Resource\Service;

class InPostCarrierModel extends ObjectModel
{
    public $force_id = true;

    public $service;
    public $cod = false;
    public $weekend_delivery = false;

    public static $definition = [
        'table' => 'inpost_carrier',
        'primary' => 'id_reference',
        'fields' => [
            'service' => [
                'type' => self::TYPE_STRING,
                'values' => Service::SERVICES,
            ],
            'cod' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'weekend_delivery' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
        ],
    ];

    public function add($auto_date = true, $null_values = false)
    {
        $id_reference = $this->id;

        if ($result = parent::add($auto_date, $null_values)) {
            $this->id = $id_reference;
        }

        return $result;
    }

    public function delete()
    {
        if ($carrier = $this->getCarrier()) {
            $carrier->active = false;
            $carrier->is_module = false;
            $carrier->external_module_name = null;

            if (!$carrier->update()) {
                return false;
            }
        }

        return parent::delete();
    }

    public function getCarrier()
    {
        return Carrier::getCarrierByReference($this->id) ?: null;
    }

    public static function getDataByCarrierId($id_carrier)
    {
        static $carriers;

        if (!isset($carriers)) {
            $query = (new DbQuery())
                ->select('ic.*, c.id_carrier')
                ->from('inpost_carrier', 'ic')
                ->innerJoin('carrier', 'c', 'c.id_reference = ic.id_reference')
                ->where('c.deleted = 0')
                ->where('c.active = 1');

            foreach (Db::getInstance()->executeS($query) as $row) {
                $carriers[$row['id_carrier']] = [
                    'id_carrier' => $row['id_carrier'],
                    'cashOnDelivery' => $row['cod'],
                    'service' => $row['service'],
                    'lockerService' => $row['service'] === Service::INPOST_LOCKER_STANDARD,
                    'weekendDelivery' => $row['weekend_delivery'],
                ];
            }
        }

        return isset($carriers[$id_carrier]) ? $carriers[$id_carrier] : null;
    }

    /** @return self[] */
    public static function getNonDeletedCarriers()
    {
        $subQuery = (new DbQuery())
            ->from('carrier')
            ->where('id_reference = ic.id_reference')
            ->where('deleted = 0');

        $query = (new DbQuery())
            ->from('inpost_carrier', 'ic')
            ->where('EXISTS (' . $subQuery . ')');

        return self::hydrateCollection(
            self::class,
            Db::getInstance()->executeS($query)
        );
    }
}
