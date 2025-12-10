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

namespace InPost\Shipping\DataProvider;

use Exception;
use InPost\Shipping\ShipX\Exception\ResourceNotFoundException;
use InPost\Shipping\ShipX\Resource\Point;
use InPost\Shipping\Traits\ErrorsTrait;

class PointDataProvider
{
    use ErrorsTrait;

    /** @var Point[] */
    protected $points = [];

    public function getPointData($id)
    {
        if (!isset($this->points[$id])) {
            try {
                $this->points[$id] = Point::get($id);
            } catch (ResourceNotFoundException $exception) {
                $this->points[$id] = false;
            } catch (Exception $exception) {
                $this->addError($exception->getMessage());
            }
        }

        return isset($this->points[$id]) ? $this->points[$id] : null;
    }
}
