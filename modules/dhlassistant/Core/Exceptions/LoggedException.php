<?php

namespace DhlAssistant\Core\Exceptions;

use DhlAssistant\Core;

class LoggedException extends \Exception
{
    /**
     * @param string $sMessage
     * @param int $iCode
     */
    public function __construct($sMessage = '', $iCode = 0)
    {
        \DhlAssistant\Core::Log($sMessage, "LoggedException ({$iCode})");

        parent::__construct($sMessage, $iCode);
    }
}

?>