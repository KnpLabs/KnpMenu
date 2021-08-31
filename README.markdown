KnpMenu
=======

The KnpMenu library provides object oriented menus for PHP.
It is used by the [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) for Symfony
but can now be used stand-alone.

[![Build Status](https://github.com/KnpLabs/KnpMenu/workflows/build/badge.svg)](https://github.com/KnpLabs/KnpMenu/actions)
[![Latest Stable Version](https://poser.pugx.org/knplabs/knp-menu/v/stable.svg)](https://packagist.org/packages/knplabs/knp-menu)
[![Latest Unstable Version](https://poser.pugx.org/knplabs/knp-menu/v/unstable.svg)](https://packagist.org/packages/knplabs/knp-menu)
[![Gitter chat](https://badges.gitter.im/KnpLabs/KnpMenu.svg)](https://gitter.im/KnpLabs/KnpMenu)

## Installation

KnpMenu uses Composer, please checkout the [composer website](http://getcomposer.org) for more information.

The simple following command will install `knp-menu` into your project. It also add a new
entry in your `composer.json` and update the `composer.lock` as well.

```bash
$ composer require knplabs/knp-menu
```

> KnpMenu follows the PSR-4 convention names for its classes, which means you can easily integrate `knp-menu` classes loading in your own autoloader.

## Getting Started

```php
<?php

// Include dependencies installed with composer
require 'vendor/autoload.php';

use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

$factory = new MenuFactory();
$menu = $factory->createItem('My menu');
$menu->addChild('Home', ['uri' => '/']);
$menu->addChild('Comments', ['uri' => '#comments']);
$menu->addChild('Symfony', ['uri' => 'http://symfony.com/']);
$menu->addChild('Happy Awesome Developers');

$renderer = new ListRenderer(new \Knp\Menu\Matcher\Matcher());
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
    <a href="http://symfony.com/">Symfony</a>
  </li>
  <li class="last">
    <span>Happy Awesome Developers</span>
  </li>
</ul>
```

This way you can finally avoid writing an ugly template to show the selected item,
the first and last items, submenus, ...

> The bulk of the documentation can be found in the `doc` directory.

## What now?

Follow the tutorial in [`doc/01-Basic-Menus.md`][0] and [`doc/02-Twig-Integration.md`][1]
to discover how KnpMenu will rock your world!

Find all available documentation at [`doc/`][2].

## Maintainers

This library is maintained by the following people (alphabetically sorted) :

- [@derrabus][3]
- [@garak][4]
- [@stof][5]

## Credits

This bundle was originally ported from [ioMenuPlugin](http://github.com/weaverryan/ioMenuPlugin),
a menu plugin for symfony1. It has since been developed by [KnpLabs](http://www.knplabs.com) and
the [Symfony community](https://github.com/KnpLabs/KnpMenu/graphs/contributors).

[0]: doc/01-Basic-Menus.md
[1]: doc/02-Twig-Integration.md
[2]: doc/
[3]: https://github.com/derrabus
[4]: https://github.com/garak
[5]: https://github.com/stof
