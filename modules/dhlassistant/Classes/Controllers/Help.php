<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Core;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Traits;

class Help extends Models\Controller
{
    use Traits\ControllerWrappedOutput;

    /**
     * @inheritDoc
     */
    public function Go()
    {
        $this->MakeWrappedOutput(
            Wrappers\ConfigWrapper::Get('FullName') . ' - pomoc',
            Core\Template::Render('Help', ['controller' => $this])
        );
    }
}
