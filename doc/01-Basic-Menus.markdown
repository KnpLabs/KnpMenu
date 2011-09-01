Creating Menus: The Basics
==========================

Let's face it, creating menus sucks. Menus - a common aspect of any
site - can range from being simple and mundane to giant monsters that
become a headache to code and maintain.

This bundle solves the issue by giving you a small, yet powerful and flexible
framework for handling your menus. While most of the examples shown here
are simple, the menus can grow arbitrarily large and deep.

Creating a menu
---------------

The menu framework centers around one main interface: `Knp\Menu\ItemInterface`.
Items are created by a factory implementing `Knp\Menu\FactoryInterface`.
It's best to think of each `ItemInterface` object as an `<li>` tag that can
hold children objects (`<li>` tags that are wrapped in a `<ul>` tag).
For example:

```php
<?php

use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

$factory = new MenuFactory();
$menu = $factory->createItem('My menu');
$menu->addChild('Home', array('uri' => '/'));
$menu->addChild('Comments', array('uri' => '#comments'));
$menu->addChild('Symfony2', array('uri' => 'http://symfony-reloaded.org/'));

$renderer = new ListRenderer()
echo $renderer->render($menu);
```

The above would render the following html code:

```html
<ul>
  <li class="first">
    <a href="/">Home</a>
  </li>
  <li class="current">
    <a href="#comments">Comments</a>
  </li>
  <li class="last">
    <a href="http://symfony-reloaded.org/">Symfony2</a>
  </li>
</ul>
```

>**NOTE**
>The menu framework automatically adds `first` and `last` classes to each
>`<li>` tag at each level for easy styling. Notice also that a `current`
>class is added to the "current" menu item by uri and `current_ancestor`
>to its ancestors (the classes are configurable) The above example assumes
>the menu is being rendered on the `/comments` page, making the Comments
>menu the "current" item.

>**NOTE**
>When the menu is rendered, it's actually spaced correctly so that it appears
>as shown in the source html. This is to allow for easier debugging and can
>be turned off by passing the `compressed` option to the renderer.

Working with your menu tree
---------------------------

Your menu tree works and acts like a multi-dimensional array. Specifically,
it implements ArrayAccess, Countable and Iterator:

```php
<?php

use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

$factory = new MenuFactory();
$menu = $factory->createItem('My menu');
$menu->addChild('Home', array('uri' => '/'));
$menu->addChild('Comments');

// ArrayAccess
$menu['Comments']->setUri('#comments');
$menu['Comments']->addChild('My comments', array('uri' => '/my_comments'));

// Countable
echo count($menu); // returns 2

// Iterator
foreach ($menu as $child) {
  echo $menu->getLabel();
}
```

As you can see, the name you give your menu item (e.g. overview, comments)
when creating it is the name you'll use when accessing it. By default,
the name is also used when displaying the menu, but that can be overridden
by setting the menu item's label (see below).

Customizing each menu item
--------------------------

There are many ways to customize the output of each menu item. Each property
can be customized in two ways: either by passing an option to the factory
when creating the item, either by using the setter of the existing item.

### The label

By default, a menu item uses its name when rendering. You can easily
change this without changing the name of your menu item by setting its label:

```php
<?php
// Setting the label when creating the item
$menu->addChild('Home', array('uri' => '/', 'label' => 'Back to homepage'));
// Changing the label of an existing item
$menu->addChild('Home', array('uri' => '/'));
$menu['Home']->setLabel('Back to homepage');
```

### The uri

If an item isn't given a url, then text will be output instead of a link:

```php
<?php
$menu->addChild('Not a link');
$menu->addChild('Home', '/');
$menu->addChild('Symfony', 'http://www.symfony-reloaded.org');
```

You can also specify the uri after creation via the `setUri()` method:

```php
<?php
$menu['Home']->setUri('/');
```

>**NOTE**
>If you want to remove the uri of an item, set it to `null`.

### Menu attributes

In fact, you can add any attribute to the `<li>` tag of a menu item. This
can be done when creating a menu item or via the `setAttribute()` and `setAttributes()`
methods:

```php
<?php
$menu->addChild('Home', array('attributes' => array('id' => 'back_to_homepage')));
$menu['Home']->setAttribute('id', 'back_to_homepage');
```

>**NOTE**
>For the root element, the attributes are displayed on the `<ul>` element.

>**NOTE**
>`setAttributes()` will overwrite all existing attributes.

>**NOTE**
>To remove an existing attribute, set it to `null`. It will not be rendered.

You can also add link attributes (displayed on the `<a>` element) or label
attributes (displayed on the `<span>` element when it is not a link).

### Rendering only part of a menu

If you need to render only part of your menu, the menu framework gives
you unlimited control to do so:

```php
<?php
// render only 2 levels deep (root, parents, children)
$renderer->render($menu, array('depth' => 2));

// rendering everything except for the children of the Home branch
$menu['Home']->setDisplayChildren(false);
$renderer->render($menu);

// render everything except for Home AND its children
$menu['Home']->setDisplay(false);
$renderer->render($menu);
```

Using the above controls, you can specify exactly which part of your menu
you need to render at any given time.

Creating a Menu from a Tree structure
-------------------------------------

You can create a menu easily from a Tree structure (a nested set for example) by
making it implement ``Knp\Menu\NodeInterface``. You will then be able
to create the menu easily (assuming ``$node`` is the root node of your structure):

```php
<?php

$factory = new \Knp\Menu\MenuFactory();
$menu = $factory->createFromNode($node);
```
