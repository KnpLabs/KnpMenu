<?php

if (defined('TWIG_LIB_DIR') && 'NOT_SET' !== TWIG_LIB_DIR) {
    require_once TWIG_LIB_DIR.'/Twig/Autoloader.php';
    Twig_Autoloader::register();
}

if (defined('PIMPLE_LIB_DIR') && 'NOT_SET' !== PIMPLE_LIB_DIR) {
    require_once PIMPLE_LIB_DIR.'/Pimple.php';
}

spl_autoload_register(function($class) {
    $class = ltrim($class, '\\');
    foreach (array('Knp\Menu\Tests' => __DIR__, 'Knp\Menu' => __DIR__.'/../src') as $namespace => $dir) {
        if (0 === strpos($class, $namespace)) {
            $file = $dir.'/'.str_replace('\\', '/', $class).'.php';
            if (file_exists($file)) {
                require $file;
            }
        }
    }
});
