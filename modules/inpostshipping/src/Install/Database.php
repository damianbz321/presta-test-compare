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

namespace InPost\Shipping\Install;

use Db;
use InPost\Shipping\PrestaShopContext;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPost\Shipping\ShipX\Resource\SendingMethod;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\ShipX\Resource\Status;
use InPostShipmentStatusModel;
use Language;

class Database implements InstallerInterface
{
    const ID_LANG_DEF_16 = 'INT(10) UNSIGNED NOT NULL';
    const ID_LANG_DEF_17 = 'INT(11) NOT NULL';

    protected $shopContext;
    protected $db;

    public function __construct(PrestaShopContext $shopContext)
    {
        $this->shopContext = $shopContext;
        $this->db = Db::getInstance();
    }

    public function install()
    {
        $result = true;

        $services = '"' . implode('","', Service::SERVICES) . '"';
        $idLangDef = $this->shopContext->is17()
            ? self::ID_LANG_DEF_17
            : self::ID_LANG_DEF_16;

        $result &= $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'inpost_shipment_status` (
                `id_status` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
                `name` VARCHAR(64) NOT NULL,
                PRIMARY KEY (`id_status`),
                UNIQUE (`name`)
            )
            ENGINE = ' . _MYSQL_ENGINE_ . '
            CHARSET = utf8
            COLLATE = utf8_general_ci
        ');

        $result &= $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'inpost_shipment_status_lang` (
                `id_status` INT(10) UNSIGNED NOT NULL,
                `id_lang` ' . $idLangDef . ',
                `title` VARCHAR(128) NOT NULL,
                `description` VARCHAR(512) NOT NULL,
                PRIMARY KEY (`id_status`, `id_lang`),
                FOREIGN KEY (`id_status`)
                    REFERENCES `' . _DB_PREFIX_ . 'inpost_shipment_status` (`id_status`)
                    ON DELETE CASCADE,
                FOREIGN KEY (`id_lang`)
                    REFERENCES `' . _DB_PREFIX_ . 'lang` (`id_lang`)
                    ON DELETE CASCADE
            )
            ENGINE = ' . _MYSQL_ENGINE_ . '
            CHARSET = utf8
            COLLATE = utf8_general_ci
        ');

        $result &= $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'inpost_carrier` (
                `id_reference` INT(10) UNSIGNED NOT NULL,
                `service` ENUM(' . $services . ') NOT NULL,
                `cod` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `weekend_delivery` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_reference`),
                FOREIGN KEY (`id_reference`)
                    REFERENCES `' . _DB_PREFIX_ . 'carrier` (`id_reference`)
                    ON DELETE CASCADE
            )
            ENGINE = ' . _MYSQL_ENGINE_ . '
            CHARSET = utf8
            COLLATE = utf8_general_ci
        ');

        $result &= $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'inpost_cart_choice` (
                `id_cart` INT(10) UNSIGNED NOT NULL,
                `service` ENUM(' . $services . ') NOT NULL,
                `email` VARCHAR(255),
                `phone` VARCHAR(255),
                `point` VARCHAR(32),
                PRIMARY KEY (`id_cart`)
            )
            ENGINE = ' . _MYSQL_ENGINE_ . '
            CHARSET = utf8
            COLLATE = utf8_general_ci
        ');

        $result &= $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'inpost_dispatch_point` (
                `id_dispatch_point` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `office_hours` VARCHAR(255),
                `email` VARCHAR(255),
                `phone` VARCHAR(255),
                `street` VARCHAR(255) NOT NULL,
                `building_number` VARCHAR(255) NOT NULL,
                `post_code` CHAR(6) NOT NULL,
                `city` VARCHAR(255) NOT NULL,
                `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_dispatch_point`)
            )
            ENGINE = ' . _MYSQL_ENGINE_ . '
            CHARSET = utf8
            COLLATE = utf8_general_ci
        ');

        $result &= $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'inpost_dispatch_order` (
                `id_dispatch_order` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
                `id_dispatch_point` INT(10) UNSIGNED,
                `shipx_dispatch_order_id` INT(10) UNSIGNED NOT NULL,
                `number` INT(10) UNSIGNED,
                `status` VARCHAR(64),
                `price` DECIMAL(20,6),
                `date_add` DATETIME NOT NULL,
                PRIMARY KEY (`id_dispatch_order`),
                FOREIGN KEY (`id_dispatch_point`)
                    REFERENCES `' . _DB_PREFIX_ . 'inpost_dispatch_point` (`id_dispatch_point`)
                    ON DELETE SET NULL
            )
            ENGINE = ' . _MYSQL_ENGINE_ . '
            CHARSET = utf8
            COLLATE = utf8_general_ci
        ');

        $result &= $this->db->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'inpost_shipment` (
                `id_shipment` INT(10) UNSIGNED AUTO_INCREMENT NOT NULL,
                `organization_id` INT(10) UNSIGNED NOT NULL,
                `id_order` INT(10) UNSIGNED NOT NULL,
                `sandbox` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `shipx_shipment_id` INT(10) UNSIGNED NOT NULL,
                `reference` VARCHAR(100),
                `email` varchar(255) NOT NULL,
                `phone` varchar(255) NOT NULL,
                `service` ENUM(' . $services . ') NOT NULL,
                `sending_method` ENUM("' . implode('","', SendingMethod::SENDING_METHODS) . '"),
                `sending_point` VARCHAR(32),
                `weekend_delivery` TINYINT(1) UNSIGNED,
                `template` ENUM("' . implode('","', Shipment::DIMENSION_TEMPLATES) . '"),
                `dimensions` VARCHAR(255),
                `id_dispatch_order` INT(10) UNSIGNED,
                `target_point` VARCHAR(32),
                `cod_amount` DECIMAL(20,6),
                `insurance_amount` DECIMAL(20,6),
                `tracking_number` CHAR(24),
                `status` VARCHAR(64),
                `price` DECIMAL(20,6),
                `label_printed` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `date_add` DATETIME NOT NULL,
                PRIMARY KEY (`id_shipment`),
                FOREIGN KEY (`id_order`)
                    REFERENCES `' . _DB_PREFIX_ . 'orders` (`id_order`)
                    ON DELETE CASCADE,
                FOREIGN KEY (`id_dispatch_order`)
                    REFERENCES `' . _DB_PREFIX_ . 'inpost_dispatch_order` (`id_dispatch_order`)
                    ON DELETE SET NULL
            )
            ENGINE = ' . _MYSQL_ENGINE_ . '
            CHARSET = utf8
            COLLATE = utf8_general_ci
        ');

        return $result && $this->addStatuses();
    }

    protected function addStatuses()
    {
        $languageIds = Language::getIDs(false);
        $indexPl = [];

        if ($id_lang_pl = Language::getIdByIso('PL')) {
            $statuses = Status::getAll([
                'query' => ['lang' => 'pl_PL'],
            ]);

            foreach ($statuses as $status) {
                $indexPl[$status->name] = [
                    'title' => $status->title,
                    'description' => $status->description,
                ];
            }
        }

        $statuses = Status::getAll([
            'query' => ['lang' => 'en_GB'],
        ]);

        foreach ($statuses as $status) {
            if (!InPostShipmentStatusModel::getStatusByName($status->name)) {
                $statusModel = new InPostShipmentStatusModel();

                $statusModel->name = $status->name;
                foreach ($languageIds as $id_lang) {
                    if ($id_lang == $id_lang_pl) {
                        $statusModel->title[$id_lang] = $indexPl[$status->name]['title'];
                        $statusModel->description[$id_lang] = $indexPl[$status->name]['description'];
                    } else {
                        $statusModel->title[$id_lang] = $status->title;
                        $statusModel->description[$id_lang] = $status->description;
                    }
                }

                $statusModel->add();
            }
        }

        return true;
    }

    public function uninstall()
    {
        $result = true;

        $result &= $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'inpost_carrier`');
        $result &= $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'inpost_shipment_status_lang`');
        $result &= $this->db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'inpost_shipment_status`');

        return $result;
    }
}
