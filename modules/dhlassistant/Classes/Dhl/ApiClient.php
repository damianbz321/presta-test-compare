<?php

namespace DhlAssistant\Classes\Dhl;

use DhlAssistant\Classes\DataModels;

class ApiClient extends \SoapClient
{
    public function __construct(DataModels\DhlApi $oApi)
    {
        $classcontainer_path = __NAMESPACE__ . '\\' . $oApi->ApiClassContainer . '\\' . $oApi->ApiClassContainer;
        parent::__construct
        (
            $oApi->WsdlUrl,
            array
            (
                'classmap' => $classcontainer_path::GetClassMap(),
                'stream_context' => stream_context_create(
                    array(
                        'http' => array(
                            'header' => 'DHLApiOrgin: PS_SII_1_7' . $oApi->ConnectionIdent,
                        )
                    )
                ),
                'trace' => 1,
            )
        );
    }
}
