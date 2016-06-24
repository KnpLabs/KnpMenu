Silex 1 Integration
===================

KnpMenu provides an extension for the [Silex](http://silex-project.org/)
microframework version 1. **This extension is not compatible with Silex 2.** 
If you use Silex version 2, please do a pull request to provide the necessary
integration.

RouterAwareFactory
------------------

The `Knp\Menu\Silex\RouterAwareFactory` extends the default factory to add
the support of the url generator of the Symfony2 Routing component. You can
then pass 3 new options to the factory:

* `route`: The route name (the generator will be used if the name is not `null`)
* `routeParameters`: The parameters to generate the url (if omitted, an empty array is used)
* `routeAbsolute`: Whether the generated url should be absolute (default `false`)

>**NOTE**
>When you give both a route and an uri to the factory, the route will be used.

Using the KnpMenuExtension
--------------------------

### Registering the extension

```php
<?php

// registering the autoloader for the library.
$app['autoloader']->registerNamespace('Knp\Menu', __DIR__.'/vendor/KnpMenu/src');

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
