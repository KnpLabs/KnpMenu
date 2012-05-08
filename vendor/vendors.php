#!/usr/bin/env php
<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

set_time_limit(0);

$vendorDir = __DIR__;
$deps = array(
    array('Symfony/Component/Routing', 'git://github.com/symfony/Routing.git', 'origin/master'),
    array('Symfony/Component/HttpFoundation', 'git://github.com/symfony/HttpFoundation.git', 'origin/master'),
    array('Symfony/Component/HttpKernel', 'git://github.com/symfony/HttpKernel.git', 'origin/master'),
    array('Symfony/Component/EventDispatcher', 'git://github.com/symfony/EventDispatcher.git', 'origin/master'),
    array('Symfony/Component/ClassLoader', 'git://github.com/symfony/ClassLoader.git', 'origin/master'),
    array('twig', 'git://github.com/fabpot/Twig.git', 'origin/master'),
    array('Silex', 'git://github.com/fabpot/Silex.git', 'origin/master'),
    array('pimple', 'git://github.com/fabpot/Pimple.git', 'origin/master'),
);

foreach ($deps as $dep) {
    list($name, $url, $rev) = $dep;

    echo "> Installing/Updating $name\n";

    $installDir = $vendorDir.'/'.$name;
    if (!is_dir($installDir)) {
        system(sprintf('git clone -q %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    }

    system(sprintf('cd %s && git fetch -q origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}
