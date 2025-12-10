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

namespace InPost\Shipping\ShipX;

use GuzzleHttp\Exception\RequestException;
use InPost\Shipping\Api\Request;
use InPost\Shipping\Api\Response;
use InPost\Shipping\ShipX\Exception\AccessForbiddenException;
use InPost\Shipping\ShipX\Exception\ResourceNotFoundException;
use InPost\Shipping\ShipX\Exception\ShipXException;
use InPost\Shipping\ShipX\Exception\TokenInvalidException;
use InPost\Shipping\ShipX\Exception\ValidationFailedException;

class ShipXRequest extends Request
{
    /**
     * Send the API Request.
     *
     * @return Response
     *
     * @throws RequestException
     * @throws ShipXException
     */
    public function send()
    {
        try {
            return parent::send();
        } catch (RequestException $exception) {
            throw $this->wrapException($exception);
        }
    }

    protected function wrapException(RequestException $exception)
    {
        if ($exception->hasResponse()) {
            $exception->getResponse()->getBody()->seek(0);

            $contents = json_decode($exception->getResponse()->getBody()->getContents(), true);

            switch ($contents['error']) {
                case 'access_forbidden':
                    return new AccessForbiddenException($contents, $exception);
                case 'resource_not_found':
                    return new ResourceNotFoundException($contents, $exception);
                case 'token_invalid':
                    return new TokenInvalidException($contents, $exception);
                case 'validation_failed':
                    return new ValidationFailedException($contents, $exception);
                default:
                    return new ShipXException($contents, $exception);
            }
        }

        return $exception;
    }
}
