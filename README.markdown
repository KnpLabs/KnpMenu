KnpMenu
=======

The KnpMenu library provides object oriented menus for PHP 5.3.
It is used by the [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) for Symfony2
but can now be used stand-alone.

[![Build Status](https://secure.travis-ci.org/KnpLabs/KnpMenu.png)](http://travis-ci.org/KnpLabs/KnpMenu)

```php
<?php

use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

$factory = new MenuFactory();
$menu = $factory->createItem('My menu');
$menu->addChild('Home', array('uri' => '/'));
$menu->addChild('Comments', array('uri' => '#comments'));
$menu->addChild('Symfony2', array('uri' => 'http://symfony-reloaded.org/'));
$menu->addChild('Coming soon');

$renderer = new ListRenderer();
echo $renderer->render($menu);
```

The above menu would render the following HTML:

```html
<ul>
  <li class="first">
    <a href="/">Home</a>
  </li>
  <li class="current">
    <a href="#comments">Comments</a>
  </li>
  <li>
    <a href="http://symfony-reloaded.org/">Symfony2</a>
  </li>
  <li class="last">
    <span>Coming soon</span>
  </li>
</ul>
```

This way you can finally avoid writing an ugly template to show the selected item,
the first and last items, submenus, ...

> The bulk of the documentation can be found in the `doc` directory.

## Installation

KnpMenu does not provide an autoloader but follow the PSR-0 convention. You
can use any compliant autoloader for the library, for instance the Symfony2
[ClassLoader component](https://github.com/symfony/ClassLoader).
Assuming you cloned the library in `vendor/KnpMenu`, it will be configured
this way:

```php
<?php
$loader->registerNamespaces(array(
    'Knp\Menu' => __DIR__.'/vendor/KnpMenu/src'
    // ...
));
```

## What now?

Follow the tutorial in `doc/01-Basics-Menus.markdown` and `doc/02-Twig-Integration.markdown`
to discover how `KnpMenu` will rock your world!

## Credits

This bundle was originally ported from [ioMenuPlugin](http://github.com/weaverryan/ioMenuPlugin),
a menu plugin for symfony1. It has since been developed by [knpLabs](http://www.knplabs.com) and
the Symfony community.
