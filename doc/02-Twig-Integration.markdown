Twig Integration
================

KnpMenu provides 2 different (and complementary) integrations with Twig:

* [MenuExtension](#menu-extension): a Twig extension allowing you to easily render menus from within a Twig template

* [TwigRenderer](#twig-renderer): a Twig renderer that (behind the scenes) uses a Twig template to render the menu

<a name="menu-extension"></a>

Using the MenuExtension
-----------------------

The easiest (but not best) way to render a menu inside a Twig template is
to pass both the renderer and menu into a template:

```php
<?php

// bootstrap Twig
$twigLoader = new \Twig_Loader_Filesystem(array(
    // path to your templates
));
$twig = new \Twig_Environment($twigLoader); // you can add the charset (i.e. ISO-8859-1) in the array of options if you need 

$itemMatcher = new \Knp\Menu\Matcher\Matcher();
// setup some renderer
$renderer = new \Knp\Menu\Renderer\ListRenderer($itemMatcher);
//$menuRenderer = new \Knp\Menu\Renderer\TwigRenderer($twig, 'knp_menu.html.twig', $itemMatcher);

// render a template
$template = $twig->loadTemplate('menu.twig');
echo $template->display(array(
    'renderer' => $renderer,
    'menu' => $menu
));
```

To render the menu, your template would look like this:

```jinja
{{ renderer.render(menu) | raw }}
```

This is ok, but there is a better way. By configuring all of your renderers
and menus in a central location, you can use a convenient and shorthand syntax
to render your menus inside a Twig template.

<a name="renderer-provider"></a>

### Loading your renderers from a provider

To make life simpler, a good option is to centralize the setup of all of
your renderers. To do this, you'll need to create a "renderer provider", which
is some object - implementing `Knp\Menu\Renderer\RendererProviderInterface` -
which acts like a container for all of your renderers.

The default implementation of the renderer provider is based on [Pimple](http://pimple-project.org/)
so that your renderers can be lazy-loaded.

```php
<?php
// setup pimple, and assign the renderer to "menu_renderer"
$pimple = new \Pimple();
$itemMatcher = \Knp\Menu\Matcher\Matcher();
$pimple['list_renderer'] = function() {
    return new \Knp\Menu\Renderer\ListRenderer($itemMatcher);
};

$rendererProvider = new \Knp\Menu\Renderer\PimpleProvider(
    $pimple,
    // common name of the renderer used by default
    'main',
    // common name for the renderer => name of the renderer in pimple
    array('main' => 'list_renderer')
);
$helper = new \Knp\Menu\Twig\Helper($rendererProvider);
$menuExtension = new \Knp\Menu\Twig\MenuExtension($helper);
$twig->addExtension($menuExtension);
```

Now, the renderer is aliased to the name `main`. You can render the menu
with the default renderer simply via:

```jinja
{{ knp_menu_render(menu) }}
```

In this example, `menu` variable is the  `MenuItem` object you've passed
into your template.

You can also pass options when rendering the template:

```jinja
{{ knp_menu_render(menu, {'currentAsLink': false, 'compressed': true}) }}
```

You can also use another renderer than the default one by passing its name:

```jinja
{{ knp_menu_render(menu, {}, 'main') }}
```

<a name="get-item-by-path"></a>

### Retrieving an item by its path in the tree

The Twig extension allow you to retrieve an item in your menu tree by its
path (the name of the item is used in the path):

```jinja
{# The menu variable contains a Knp\Menu\ItemInterface object #}
{% set item = knp_menu_get(menu, ['Comment', 'My comments']) %}

{# The following could be used but would throw a Fatal Error for some invalid
paths instead of an exception: #}
{% set item = menu['Comment']['My comments'] %}

{# actually render the part of the menu #}
{{ knp_menu_render(item) }}
```

>**NOTE**
>An InvalidArgumentException will be thrown if the path is invalid.

Using the path is also supported when using the `knp_menu_render` function
by using an array:

```jinja
{# The menu variable contains a Knp\Menu\ItemInterface object #}
{{ knp_menu_render([menu, 'Comment', 'My comments']) }}
```

<a name="menu-provider"></a>

### Loading the menu from a provider

The MenuExtension also supports retrieving the menus from a provider implementing
`Knp\Menu\Provider\MenuProviderInterface` which works the same way as the
`RendererProviderInterface`. The default implementation is also based on
Pimple.

```php
<?php
$factory = new MenuFactory();

$pimple = new \Pimple();
// setup the renderer(s) in Pimple

$pimple['menu_main'] = function() use ($factory) {
    $menu = $factory->createItem('My menu');
    // setup the menu

    return $menu;
};
$pimple['menu_sidebar'] = ... //

// $rendererProvider = ...
$menuProvider = new \Knp\Menu\Provider\PimpleProvider(
    $pimple,
    array('main' => 'menu_main', 'sidebar' => 'menu_sidebar')
);
$helper = new \Knp\Menu\Twig\Helper($rendererProvider, $menuProvider);
$menuExtension = new \Knp\Menu\Twig\MenuExtension($helper);
```

You can now retrieve the menu by its name in the template:

```jinja
{% set menu = knp_menu_get('sidebar') %}
{# The menu variable now contains a Knp\Menu\ItemInterface object #}
```

When a menu provider is set, you can also use the menu name instead of the
menu object in the other functions:

```jinja
{{ knp_menu_render('main', {'depth': 1}) }}

{{ knp_menu_render(['main', 'Comments', 'My comments'], {'depth': 2}) }}

{% set item = knp_menu_get('sidebar', ['First section']) %}

{% set breadcrumbs_array = knp_menu_get_breadcrumbs_array('main') %}

{% set current_item = knp_menu_get_current_item('main') %}
```

In some cases, you may want to build the menu differently according to the
place it is used. As of KnpMenu 1.1, the ``knp_menu_get`` function supports
passing an array of options for the menu provider.

To be able to use these options in the Pimple provide, you should register
the menu as a factory closure through ``$pimple->protect()``. It will then
be called with the array of options as first argument and the pimple instance
as second argument:

```php
<?php
$factory = new MenuFactory();

$pimple = new \Pimple();
// setup the renderer(s) in Pimple

$pimple['menu_main'] = $pimple->protect(function(array $options, $c) use ($factory) {
    $menu = $factory->createItem('My menu');
    // setup the menu
    // you can use the options you passed to the provider
    // and access the pimple container for this.

    return $menu;
});
```

<a name="twig-renderer"></a>

Using the TwigRenderer
----------------------

### Registering the renderer

To use the TwigRenderer, you need to add the path of the template in the loader
when bootstrapping Twig.

```php
<?php

$twigLoader = new \Twig_Loader_Filesystem(array(
    __DIR__.'/vendor/KnpMenu/src/Knp/Menu/Resources/views',
    // your own paths
));
$twig = new \Twig_Environment($twigLoader);
$itemMatcher = \Knp\Menu\Matcher\Matcher();
$menuRenderer = new \Knp\Menu\Renderer\TwigRenderer($twig, 'knp_menu.html.twig', $itemMatcher);
```

This works just like any other renderer, and will output an un-ordered list
of menu items (e.g. `ul` and `li` elements):

```php
<?php
echo $menuRenderer->render($menu);
```

Behind the scenes, the renderer is using a Twig template to render the menu.
This template can be customized by you.

>**NOTE**
>A second template named `knp_menu_ordered.html.twig` can be used if you
>want to render the menu using an ordered list. This template extends the
>previous one which must be available using the `knp_menu.html.twig` name
>(which is the case when configuring the loader like previously).

### Using a custom template

If you need to customize how the template is rendered - beyond all of the
options given to you by modifying the menu items themselves - you can customize
the Twig template that renders the menu.

You can change the template used to render the menu in two different ways:

1) Globally: Change the second argument passed to the constructor of the renderer.

2) Locally: Pass a `template` option when rendering the menu

```php
<?php
echo $menuRenderer->render($menu, array('template' => 'my_menu.html.twig'));
```

The template needs to contain 2 blocks: `root` and `compressed_root` which
are used to display the root of the menu. The easiest way to customize the
rendering is to extend the built-in template and to replace the block you
want.

>**NOTE**
>The built-in templates contains some additional blocks to make it easier
>to customize it when using the inheritance.

Twig integration reference
--------------------------

### Functions

* `knp_menu_get('menuName' [, ['Path', 'To', 'Item'], ['options']])`: retrieve an item of the menu
* `knp_menu_render('menuName' [, ['options'], 'rendererName'])`: render a menu
* `knp_menu_get_breadcrumbs_array('menuName' [, 'subItem'])`: get an array that represent the breadcrumbs of the current page (according to the menu)
* `knp_menu_get_current_item('menuName')`: retrieve the current item (according to the menu)

You can easily generate a breadcrumb of the current page by combining the
`knp_menu_get_breadcrumbs_array` and `knp_menu_get_current_item` functions:

```jinja
<ol class="breadcrumb">
{% for breadcrumb_item in knp_menu_get_breadcrumbs_array(knp_menu_get_current_item('main')) %}
    {% if not loop.last %}
    <li><a href="{{ breadcrumb_item.uri }}">{{ breadcrumb_item.label }}</a></li>
    {% else %}
    <li class="active">{{ breadcrumb_item.label }}</li>
    {% endif %}
{% endfor %}
</ol>
```

### Filters

* `knp_menu_as_string(menuNode [, $separator])`: generate a string representation of the menu item

### Tests

* `knp_menu_current`: check if the item is the current one
* `knp_menu_ancestor`: check if the item is the ancestor of a current item
