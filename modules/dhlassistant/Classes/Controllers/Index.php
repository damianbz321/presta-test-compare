<?php

namespace DhlAssistant\Classes\Controllers;

use DhlAssistant\Core;
use DhlAssistant\Core\Models;
use DhlAssistant\Wrappers;
use DhlAssistant\Traits;

class Index extends Models\Controller
{
    use Traits\ControllerWrappedOutput;

    /**
     * @inheritDoc
     */
    public function Go()
    {
        $this->MakeWrappedOutput(
            Wrappers\ConfigWrapper::Get('FullName'),
            Core\Template::Render('Index', ['controller' => $this])
        );
    }
}
