<?php
/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
if (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

require $autoloadFile;

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Opensoft\\SimpleSerializer\Tests\\')) {
        $path = __DIR__.'/../tests/'.strtr($class, '\\', '/').'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
    }
});
