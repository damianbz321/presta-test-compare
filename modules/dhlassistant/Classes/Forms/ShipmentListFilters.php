<?php

namespace DhlAssistant\Classes\Forms;

use DhlAssistant\Core;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\Managers;
use DhlAssistant\Classes\Dhl\Enums;
use DhlAssistant\Classes\Controllers;

class ShipmentListFilters extends Models\Form
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('ShipmentListFilters');

        $Filter = new Models\Form('Filter');
        $Filter->AddField(
            (new Models\FormField(
                'Id',
                '',
                '')
            )->AddValiadtor(
                '|IsNInt',
                null,
                'error'
            )
        );

        $Filter->AddField(
            (new Models\FormField(
                'Content',
                '', '')
            )
        );

        $Filter->AddField(
            (new Models\FormField(
                'DateFrom',
                '',
                '')
            )->AddValiadtor(
                '|IsNDateString',
                null,
                'error'
            )
        );

        $Filter->AddField(
            (new Models\FormField(
                'DateTo',
                '',
                '')
            )->AddValiadtor(
                '|IsNDateString',
                null,
                'error'
            )
        );

        $Filter->AddField(
            (new Models\FormField(
                'Status',
                '',
                '')
            )->AddValiadtor(
                '|IsInArray',
                [
                    ['', 'Waiting', 'Sended']
                ],
                'error'
            )
        );

        $this->Merge($Filter, 'Filter');
    }
}

?>