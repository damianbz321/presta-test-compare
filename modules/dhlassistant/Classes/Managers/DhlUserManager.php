<?php

namespace DhlAssistant\Classes\Managers;

use DhlAssistant\Core\Exceptions;
use DhlAssistant\Wrappers;
use DhlAssistant\Classes\DataModels;

/**
 *
 */
class DhlUserManager
{
    /**
     * @param $iDhlUserId
     * @return mixed
     * @throws Exceptions\SourceLoggedException
     */
    public static function GetById($iDhlUserId)
    {
        if (!Wrappers\DbWrapper::Exists(DataModels\DhlUser::GetTreeDataObjectInfo(), $iDhlUserId)) {
            throw new Exceptions\SourceLoggedException("Błąd obsługi użytkownika DHL o ID #{$iDhlUserId}!");
        }

        return Wrappers\DbWrapper::Load(DataModels\DhlUser::GetTreeDataObjectInfo(), $iDhlUserId);
    }

    /**
     *
     * @return DataModels\DhlUser[]
     */
    public static function GetList()
    {
        return Wrappers\DbWrapper::LoadAll(DataModels\DhlUser::GetTreeDataObjectInfo());
    }
}

?>