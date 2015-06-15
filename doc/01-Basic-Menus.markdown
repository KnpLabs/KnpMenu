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

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

$factory = new MenuFactory();
$menu = $factory->createItem('My menu');
$menu->addChild('Home', array('uri' => '/'));
$menu->addChild('Comments', array('uri' => '#comments'));
$menu->addChild('Symfony2', array('uri' => 'http://symfony-reloaded.org/'));

$renderer = new ListRenderer(new Matcher());
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

When the menu is rendered, it's actually spaced correctly so that it appears
as shown in the source html. This is to allow for easier debugging and can
be turned off by passing the `true` as the second argument to the renderer.

```php
<?php

// ...

$renderer = new ListRenderer(new Matcher(), array('compressed' => true));
echo $renderer->render($menu);
```

You can also compress (or not compress) on a menu-by-menu basis by using the
`compressed` option:

```php
<?php

// ...

$renderer = new ListRenderer(new Matcher());
echo $renderer->render($menu, array('compressed' => true));
```

Working with your menu tree
---------------------------

Your menu tree works and acts like a multi-dimensional array. Specifically,
it implements ArrayAccess, Countable and Iterator:

```php
<?php

use Knp\Menu\MenuFactory;

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
  echo $child->getLabel();
}
```

As you can see, the name you give your menu item (e.g. overview, comments)
when creating it is the name you'll use when accessing it. By default,
the name is also used when displaying the menu, but that can be overridden
by setting the menu item's label (see below).

Customizing each menu item
--------------------------

There are many ways to customize the output of each menu item. Each property
can be customized in two ways: either by passing it as an option when creating
the item, or by using the setter of an existing item.

### The label

By default, a menu item uses its name when rendering. You can easily
change this without changing the name of your menu item by setting its label:

```php
<?php
// Setting the label when creating the item
$menu->addChild('Home', array('uri' => '/', 'label' => 'Back to homepage'));

// Changing the label of an existing item
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
>`setAttributes()` will overwrite all existing attributes.

>**NOTE**
>To remove an existing attribute, set it to `null`. It will not be rendered.

You can also add link attributes (displayed on the `<a>` element), label
attributes (displayed on the `<span>` element when it is not a link) or
children attributes (rendered on the `<ul>` containing the list of children):

```php
<?php
$menu->addChild('KnpLabs.com', array('uri' => 'http://knplabs.com'));
$menu['KnpLabs.com']->setLinkAttribute('class', 'external-link');

$menu->addChild('Not a link');
$menu['Not a link']->setLabelAttribute('class', 'no-link-span');

$menu->setChildrenAttribute('class', 'pull-left');
```

>**NOTE**
>For the root element, only the children attributes are used as only the
>`<ul>` element is displayed.

>**NOTE**
>In the 1.0 version of the library, the attributes were rendered on the root
>element instead of rendering the children attributes, which was inconsistent
>and has been changed for 1.1.

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

### Other rendering options

Most renderers also support several other options, which can be passed as
the second argument to the `render()` method:

* `depth`
* `matchingDepth`: The depth of the scan to determine whether an item
  is an ancestor of the current item.
* `currentAsLink` (default: `true`)
* `currentClass` (default: `current`)
* `ancestorClass` (default: `current_ancestor`)
* `firstClass` (default: `first`)
* `lastClass` (default:  `last`)
* `compressed` (default: `false`)
* `allow_safe_labels` (default: `false`)
* `clear_matcher` (default `true`): whether to clear the internal cache of the matcher after rendering
* `leaf_class` (default: `null`): class for leaf elements in your html tree
* `branch_class` (default: `null`): class for branch elements in your html tree

>**NOTE**
>When setting the `allow_safe_labels` option to `true`, you can specify that
>a label should not be escaped by the renderer by adding the `safe_label`
>extra in the item. Use it with caution as it can create some XSS holes in
>your application if the label is coming from the user.

The Current Menu Item
---------------------

If the menu item is matched as current, a `current` class will be added to
the `li` around that item, as well as a `current_ancestor` around any of
its parent `li` elements. This state can either be forced on the item by
setting it explicitly or matched using several voters.

```php
<?php

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

$factory = new MenuFactory();
$menu = $factory->createItem('My menu');

// set the current state explicitly
$menu['current_item']->setCurrent(true);
$menu['non_current_item']->setCurrent(false);

// Use the voter
$menu['other_item']->setCurrent(null); // default value for items

$matcher = new Matcher();
$matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));

$renderer = new ListRenderer($matcher);
```

The library provides 3 implementations of the VoterInterface:

 * `Knp\Menu\Matcher\Voter\UriVoter` matching against the uri of the item
 * `Knp\Menu\Matcher\Voter\RouteVoter` matching the `_route` attribute of a
   Symfony Request object against the `routes` extra of the item
 * `Knp\Menu\Matcher\Voter\RegexVoter` matching against the uri of the item using a regular expression

Here are some examples for instantiation of voters:

```php
<?php

$regexVoter = new \Knp\Menu\Matcher\Voter\RegexVoter('/^StartOfUri/');

$routeVoter = new \Knp\Menu\Silex\Voter\RouteVoter();
$routeVoter->setRequest($symfonyRequest);
```

Creating a Menu from a Tree structure
-------------------------------------

You can create a menu easily from a Tree structure (a nested set for example) by
making it implement `Knp\Menu\NodeInterface`. You will then be able
to create the menu easily (assuming `$node` is the root node of your structure):

```php
<?php

$factory = new \Knp\Menu\MenuFactory();
$menu = $factory->createFromNode($node);
```

Change the charset
------------------

```php
$renderer = new ListRenderer(new Matcher(), [], 'ISO-8859-1');
```