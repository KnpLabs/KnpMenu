KnpMenu
=======

The KnpMenu library provides object oriented menus for PHP.
It is used by the [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) for Symfony
but can now be used stand-alone.

[![Build Status](https://secure.travis-ci.org/KnpLabs/KnpMenu.svg)](http://travis-ci.org/KnpLabs/KnpMenu)
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

Find all available documentation at [`doc/`][2] or the next table of contents:

Documentation
=================

<!--ts-->
   * [Creating Menus: The Basics](doc/01-Basic-Menus.md#creating-menus-the-basics)
      * [Creating a menu](doc/01-Basic-Menus.md#creating-a-menu)
      * [Working with your menu tree](doc/01-Basic-Menus.md#working-with-your-menu-tree)
      * [Customizing each menu item](doc/01-Basic-Menus.md#customizing-each-menu-item)
      * [The Current Menu Item](doc/01-Basic-Menus.md#the-current-menu-item)
      * [Creating a Menu from a Tree structure](doc/01-Basic-Menus.md#creating-a-menu-from-a-tree-structure)
   * [Advanced Menu](doc/01a-Advanced-Menu.md#advanced-menu)
   * [Twig Integration](doc/02-Twig-Integration.md#twig-integration#twig-integration)
      * [Using the MenuExtension](doc/02-Twig-Integration.md#using-the-menuextension)
      * [Loading your renderers from a provider](doc/02-Twig-Integration.md#loading-your-renderers-from-a-provider)
      * [Loading the menu from a provider](doc/02-Twig-Integration.md#loading-the-menu-from-a-provider)
      * [Using the TwigRenderer](doc/02-Twig-Integration.md#using-the-twigrenderer)
      * [Twig integration reference](doc/02-Twig-Integration.md#twig-integration-reference)
   * [Silex Integration (deprecated since 2.3)(abandoned)](doc/03-Silex-Integration.md#silex-1-integration)
   * [Iterating over Menus](doc/04-Iterators.md#iterating-over-menus)
      * [Iterating recursively](doc/04-Iterators.md#iterating-recursively)
      * [Filtering only current items](doc/04-Iterators.md#filtering-only-current-items)
      * [Filtering only displayed items](doc/04-Iterators.md#filtering-only-displayed-items)
   * [Matcher to determine the current page](doc/05-Matcher.md#docker)
      * [Basics](doc/05-Matcher.md#basics)
      * [Available voters](doc/05-Matcher.md#basics)
      * [Create your own voters](doc/05-Matcher.md#available-voters#create-your-own-voters)
   * [FAQ](doc/06-FAQ.md#faq)
     * [How to apply the active class to a item and all ancestors](doc/examples/01_apply_active_class_to_whole_tree.md#how-to-apply-the-active-class-to-a-item-and-all-ancestors)
<!--te-->

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
