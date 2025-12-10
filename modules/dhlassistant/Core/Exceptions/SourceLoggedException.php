<?php

namespace DhlAssistant\Core\Exceptions;

use DhlAssistant\Wrappers;

class SourceLoggedException extends LoggedException
{
    /**
     * @param string $sMessage
     * @param int $iCode
     */
    public function __construct($sMessage = '', $iCode = 0)
    {
        Wrappers\SourceWrapper::Log($sMessage);

        parent::__construct($sMessage, $iCode);
    }
}

?>