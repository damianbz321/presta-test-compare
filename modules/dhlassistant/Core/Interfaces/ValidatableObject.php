<?php

namespace DhlAssistant\Core\Interfaces;

interface ValidatableObject
{
    /**
     * @param null $sContextName
     * @param array $aValues
     * @return mixed
     */
    public function Validate($sContextName = null, $aValues = []);
}

?>