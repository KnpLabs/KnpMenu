Silex Integration
=================

KnpMenu provides an extension for the [Silex](http://silex-project.org/)
microframework.

For version 1 use `\Knp\Menu\Integration\Silex\KnpMenuServiceProvider.php`.
For version 2 use `\Knp\Menu\Integration\Silex2\KnpMenuServiceProvider.php`.

Using the KnpMenuExtension
--------------------------

### Registering the extension

```php
<?php

// registering the extension
$app->register(new \Knp\Menu\Integration\Silex\KnpMenuServiceProvider());
```

>**WARNING**
>The Twig integration is available only when the KnpMenuServiceProvider is registered
>**after** the TwigServiceProvider in your application.

#### Parameters

* **knp_menu.menus** (optional): an array of ``alias => id`` pair for the
  [menu provider](02-Twig-Integration.markdown#menu-provider).
* **knp_menu.renderers** (optional): an array of ``alias => id`` pair for
  the [renderer provider](02-Twig-Integration.markdown#renderer-provider).
* **knp_menu.default_renderer** (optional): the alias of the default renderer (default to `'list'`)
* **knp_menu.template** (optional): The template used by default by the TwigRenderer.

#### Services

* **knp_menu.factory**: The menu factory (it is a router-aware one if the
  UrlGeneratorExtension is registered)
* **knp_menu.renderer.list**: The ListRenderer
* **knp_menu.renderer.twig**: The TwigRenderer (only when the Twig integration is available)

### Adding your menu to the menu provider

Making your menu available through the menu provider is really easy. Simply
create the menu as a service in the application and register it in the parameter:

```php
<?php

$app['my_main_menu'] = function($app) {
    $menu = $app['knp_menu.factory']->createItem('root');

    $menu->addChild('Home', array('route' => 'homepage'));
    // ... add more children

    return $menu;
};

$app['knp_menu.menus'] = array('main' => 'my_main_menu');
```

Your menu is now available in the [menu provider](02-Twig-Integration.markdown#menu-provider)
with the name `main`.
