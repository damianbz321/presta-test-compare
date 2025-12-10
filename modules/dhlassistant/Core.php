<?php

namespace DhlAssistant;

class Core
{
    public static $BASENAMESPACE = 'DhlAssistant';
    public static $BASEDIR = '';
    public static $DEBUG = false;
    public static $TEST = '';

    private static $S_TempLog = [];

    /**
     * @param $sClassName
     */
    public static function Autoloader($sClassName)
    {
        self::Log("Autoload {$sClassName}", 'Core Notice');

        if (substr($sClassName, 0, strlen(self::$BASENAMESPACE)) == self::$BASENAMESPACE) {
            $filename = self::$BASEDIR . str_replace(
                    '\\',
                    '/',
                    substr($sClassName, strlen(self::$BASENAMESPACE) + 1)
                    . '.php'
                );

            if (is_file(dirname($filename) . '.php') && !is_dir(dirname($filename))) {
                $filename = dirname($filename) . '.php';
            }

            if (is_file($filename)) {
                require_once($filename);

                if (method_exists($sClassName, '__AutoloadInit')) {
                    self::Log("Autoinit {$sClassName}", 'Core Notice');

                    $sClassName::__AutoloadInit();
                } else {
                    self::Log("No such file: {$filename}", 'Core Notice');
                }
            }
        }
    }

    /**
     * @param $sMessage
     * @param null $sSource
     */
    public static function Log($sMessage, $sSource = null)
    {
        if (!self::$DEBUG) {
            return;
        }

        $date = date('Y-m-d H:i:s');
        $source_info = $sSource !== null ? "; {$sSource}" : '';

        self::$S_TempLog[] = "[{$date}{$source_info}] $sMessage";
    }

    /**
     *
     */
    public static function Temp_ShowLog()
    {
        print_r(self::$S_TempLog);
    }
}

Core::$BASEDIR = dirname(__FILE__) . '/';
spl_autoload_register(Core::$BASENAMESPACE . '\Core::Autoloader', true, true);

?>