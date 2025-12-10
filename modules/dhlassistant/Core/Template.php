<?php

namespace DhlAssistant\Core;

use DhlAssistant;
use DhlAssistant\Core\Exceptions;
use DhlAssistant\Core\TemplateExtension;

class Template
{
    /**
     * @param $sFileName
     * @param array $aVars
     * @return false|string
     * @throws Exceptions\LoggedException
     */
    public static function Render($sFileName, $aVars = [])
    {
        $is_template = true;
        $file_path = DhlAssistant\Core::$BASEDIR . 'Templates/' . $sFileName . '.php';

        if (!is_file($file_path)) {
            throw new Exceptions\LoggedException("Brak szablonu '$sFileName'!");
        }

        ob_start();
        require $file_path;
        return ob_get_clean();
    }
}

?>