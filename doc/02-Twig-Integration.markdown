Twig Integration
================

KnpMenu provides 2 different (and complementary) integrations with Twig:
a Twig renderer using Twig to render the menu, and an extension allowing
you to renderer easily your menu in a Twig template with the renderer you
want.

Using the TwigRenderer
-----------------------

### Registering the renderer

You need to register the renderer as a Twig extension and to add the path
of the template in the loader when bootstrapping Twig.

```php
<?php

$twigLoader = new \Twig_Loader_Filesystem(array(
    __DIR__.'/vendor/KnpMenu/src/Knp/Menu/Resources/views',
    // your own paths
));
$twig = new \Twig_Environment($twigLoader);
$menuRenderer = new \Knp\Menu\Renderer\TwigRenderer('knp_menu.html.twig');
$twig->addExtension($menuRenderer);
```

You can now use the renderer to render your menu.

```php
<?php
echo $menuRenderer->render($menu);
```

### Using a custom template

You can change the template used by default by changing the argument passed
to the constructor of the renderer.
The TwigRenderer also allows changing the template used to render a menu by
passing the `template` option:

```php
<?php
echo $menuRenderer->render($menu, array('template' => 'my_menu.html.twig'));
```

The template needs to contain 4 blocks: `root` and `compressed_root` which
are used to display the root of the menu, and `item` and `compressed_item`
which are used to render an item. The easiest way to customize the rendering
is to extend the built-in template and to replace the block you want.

Using the MenuExtension
-----------------------

### Rendering a template

To allow choosing the renderer from the template, the MenuExtension uses
a `Knp\Menu\Renderer\RendererProviderInterface` instance.
The default implementation is based on [Pimple](http://pimple-project.org/)
to allow keeping them lazy-loaded. The second argument is a map giving the
correspondance between a name (used in the template to identify the renderer)
and the id in the Pimple container.

```php
<?php

$rendererProvider = new \Knp\Menu\Renderer\PimpleProvider(
    $pimple,
    array('list' => 'knp_menu.list_renderer', 'twig' => 'knp_menu.twig_renderer')
);
$helper = new \Knp\Menu\Twig\Helper($rendererProvider);
$menuExtension = new \Knp\Menu\Twig\MenuExtension($helper);
$twig->addExtension($menuExtension);
```

You can now render a menu in your template:

```jinja
{# The menu variable contains a Knp\Menu\ItemInterface object #}
{{ menu|knp_menu_render('list') }}

{# You can also pass some options #}
{{ menu|knp_menu_render('list', {'currentAsLink': false, 'compressed': true}) }}
```

### Retrieving an item by its path in the tree

The Twig extension allow you to retrieve an item in your menu tree by its
path (the name of the item is used in the path):

```jinja
{# The menu variable contains a Knp\Menu\ItemInterface object #}
{% set item = knp_menu_get(menu, ['Comment', 'My comments']) %}

{# The following could be used but would throw a Fatal Error for some invalid
paths instead of an exception: #}
{% set item = menu['Comment']['My comments'] %}
```

>**NOTE**
>An InvalidArgumentException will be thrown if the path is invalid.

Using the path is also supported when using the `knp_menu_render` filter
by using an array:

```jinja
{# The menu variable contains a Knp\Menu\ItemInterface object #}
{{ [menu, 'Comment', 'My comments']|knp_menu_render('list') }}
```

### Loading the menu from a provider

The MenuExtension also supports retrieving the menu from a provider implementing
`Knp\Menu\Provider\MenuProviderInterface` which works the same way than the
`RendererProviderInterface`. The default implementation is also based on
Pimple.

```php
<?php

// $rendererProvider = ...
$menuProvider = new \Knp\Menu\Provider\PimpleProvider(
    $pimple,
    array('main' => 'main_menu', 'sidebar' => 'menu.sidebar')
);
$helper = new \Knp\Menu\Twig\Helper($rendererProvider, $menuProvider);
$menuExtension = new \Knp\Menu\Twig\MenuExtension($helper);
```

You can then retrieve the menu by its name in the template:

```jinja
{% set menu = knp_menu_get('sidebar') %}
{# The menu variable now contains a Knp\Menu\ItemInterface object #}
```

When a menu provider is set, you can also use the menu name instead of the
menu object in the other functions:

```jinja
{{ 'main'|knp_menu_render('twig', {'depth': 1}) }}

{{ ['main', 'Comments', 'My comments']|knp_menu_render('twig', {'depth': 2}) }}

{% set item = knp_menu_get('sidebar', ['First section']) %}
```
