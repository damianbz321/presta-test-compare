<?php

namespace DhlAssistant\Core\Models;

use DhlAssistant\Wrappers;
use DhlAssistant\Wrappers\ConfigWrapper;
use DhlAssistant\Core\Exceptions\LoggedException;

abstract class Controller
{
    /**
     * Go(start) method.
     *
     * @return mixed
     */
    public abstract function Go();

    /**
     * Get link.
     *
     * @param array $params
     * @return string
     * @throws LoggedException
     */
    public function GetLink($params = array())
    {
        $controller_name = explode('\\', get_class($this));
        $controller_name = end($controller_name);
        $url = Wrappers\ConfigWrapper::Get('BaseUrl') . 'index.php?controller=' . $controller_name;

        if (is_array($params) && $params) {
            foreach ($params as $key => $value) {
                $url .= '&' . $key . '=' . urlencode($value);
            }
        }

        return $url;
    }
}

?>