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

namespace InPost\Shipping\Handler\Shipment;

use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPost\Shipping\ShipX\Resource\Status;
use InPostShipmentModel;
use PrestaShopCollection;

class UpdateShipmentStatusHandler
{
    protected $shipXConfiguration;

    public function __construct(ShipXConfiguration $shipXConfiguration)
    {
        $this->shipXConfiguration = $shipXConfiguration;
    }

    public function handle(array $ids = [])
    {
        $collection = (new PrestaShopCollection(InPostShipmentModel::class))
            ->where('status', '<>', Status::FINAL_STATUSES)
            ->where('sandbox', '=', $this->shipXConfiguration->useSandboxMode())
            ->where('organization_id', '=', $this->shipXConfiguration->getOrganizationId());

        if (!empty($ids)) {
            $collection->where('id_shipment', '=', $ids);
        }

        if (count($collection)) {
            $shipmentIds = [];
            $collectionKeyIndex = [];

            /** @var InPostShipmentModel $shipmentModel */
            foreach ($collection as $key => $shipmentModel) {
                $shipmentIds[] = $shipmentModel->shipx_shipment_id;
                $collectionKeyIndex[$shipmentModel->shipx_shipment_id] = $key;
            }

            /** @var Shipment $shipment */
            foreach (Shipment::getCollection(['id' => $shipmentIds]) as $shipment) {
                $shipmentModel = $collection[$collectionKeyIndex[$shipment->getId()]];
                if ($shipmentModel->status != $shipment->status) {
                    $updateOrder = empty($shipmentModel->tracking_number);
                    $shipmentModel->tracking_number = $shipment->tracking_number;
                    $shipmentModel->status = $shipment->status;
                    $shipmentModel->update();
                    if ($updateOrder) {
                        $shipmentModel->updateOrderTrackingNumber();
                    }
                }
            }
        }
    }
}
