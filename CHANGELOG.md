## 2.1.1 (2016-01-08)

* Made compatible with Symfony 3

## 2.1.0 (2015-09-20)

* Added a new function to twig: `knp_menu_get_breadcrumbs_array`
* Added a new filter to twig: `knp_menu_as_string`
* Added 2 new tests to twig: `knp_menu_current`, `knp_menu_ancestor`
* Made the templates compatible with Twig 2
* Add menu and renderer providers supporting any ArrayAccess implementations. The
  Pimple-based providers (supporting only Pimple 1) are dperecated in favor of these
  new providers.

## 2.0.1 (2014-08-01)

* Fixed voter conventions on RouteVoter

## 2.0.0 (2014-07-18)

* [BC break] Clean code and removed the BC layer

## 2.0.0 beta 1 (2014-06-19)

* [BC break] Added the new `Integration` namespace and removed the `Silex` one.
* Added a new Voter based on regular expression: `Knp\Menu\Matcher\Voter\RegexVoter`

## 2.0.0 alpha 2 (2014-05-01)

* [BC break] Changed the TwigRenderer to accept a menu template only as a string
* [BC break] Refactored the way of rendering twig templates. Every template should extends
  the `knp_menu.html.twig` template.
* Introduced extension points in the MenuFactory through `Knp\Menu\Factory\ExtensionInterface`
* [BC break compared to 2.0 alpha 1] The inheritance extension points introduced in alpha1 are
  deprecated in favor of extensions and will be removed before the stable release.
* `Knp\Menu\Silex\RouterAwareFactory` is deprecated in favor of `Knp\Menu\Silex\RoutingExtension`.
* [BC break] Deprecated the methods `createFromArray` and `createFromNode` in the MenuFactory and
  removed them from `Knp\Menu\FactoryInterface`. Use `Knp\Menu\Loader\ArrayLoader` and
  `Knp\Menu\Loader\NodeLoader` instead.
* [BC break] Deprecated the methods `moveToPosition`, `moveToFirstPosition`, `moveToLastPosition`,
  `moveChildToPosition`, `callRecursively`, `toArray`, `getPathAsString` and `getBreadcrumbsArray`
  in the MenuItem and removed them from `Knp\Menu\ItemInterface`. Use `Knp\Menu\Util\MenuManipulator`
  instead.
* Made the RouterVoter compatible with SensioFrameworkExtraBundle param converters
* Added the possibility to match routes using a regex on their name in the RouterVoter
* [BC break compared to 2.0 alpha 1] Refactored the RouterVoter to make it more flexible
    The way to pass routes in the item extras has changed.

    Before:

    ```php
    'extras' => array(
        'routes' => array('foo', 'bar'),
        'routeParameters' => array('foo' => array('id' => 4)),
    )
    ```

    After:

    ```php
    'extras' => array(
        'routes' => array(
             array('route' => 'foo', 'parameters' => array('id' => 4)),
            'bar',
        )
    )
    ```

    The old syntax is kept until the final release, but using it will trigger a E_USER_DEPRECATED error.

## 2.0.0 alpha 1 (2013-06-23)

* Added protected methods `buildOptions` and `configureItem` in the MenuFactory as extension point by inheritance
* [BC break] Refactored the way to mark items as current
  ``setCurrentUri``, ``getCurrentUri`` and ``getCurrentItem`` have been removed from the ItemInterface.
  Determining the current items is now delegated to a matcher, and the default implementation
  uses voters to apply the matching. Getting the current items can be done thanks to the CurrentItemFilterIterator.
* [BC break] The signature of the CurrentItemFilterIterator constructor changed to accept the item matcher
* [BC break] Changed the format of the breadcrumb array
  Instead of storing the elements with the label as key and the uri as value
  the array now stores an array of array elements with 3 keys: `label`, `uri` and `item`.

## 1.1.2 (2012-06-10)

* Updated the Silex service provider for the change in the interface

## 1.1.1 (2012-05-17)

* Added the children attributes and the extras in the array export

## 1.1.0 (2012-05-17)

* Marked `Knp\Menu\ItemInterface::getCurrentItem` as deprecated
* Added a recursive filter iterator keeping only displayed items
* Added a filter iterator keeping only current items
* Added a recursive iterator for the item
* Fixed building an array of breadcrumbs when a label has only digits
* Added a way to mark a label as safe
* Refactored the ListRenderer to be consistent with the TwigRenderer and provide the same extension points
* Added a way to attach extra data to an item
* Removed unnecessary optimization in the TwigRenderer
* Added some whitespace control in the Twig template to ensure an empty rendering is really empty
* [BC break] Use the childrenAttributes for the root instead of the attributes
* Made the default options configurable for the TwigRenderer
* Added the support for menu registered as factory in PimpleProvider
* Added a way to use the options in `knp_menu_get()` in Twig templates
* Added an array of options for the MenuProviderInterface
* Added a template to render an ordered list
* Refactored the template a bit to make it easier to use an ordered list
* Allow omitting the name of the child in `fromArray` (the key is used instead)

## 1.0.0 (2011-12-03)

* Add composer.json file
* Added more flexible list element blocks
* Add support for attributes on the children collection.
* Added a default renderer
* Added a ChainProvider for the menus.
* Added the Silex extension
* Added a RouterAwareFactory
* Added an helper to be able to reuse the logic more easily for other templating engines
* Added a way to retrieve an item using a path in a menu tree
* Changed the toArray method to use a depth instead of simply using a boolean flag
* Refactored the export to array and the creation from an array
* Added better support for encoding problems when escaping a string in the ListRenderer
* Added a Twig renderer
* Added missing escaping in the ListRenderer
* Renamed some methods in the ItemInterface
* Removed the configuration of the current item as link from the item
* Refactored the ListRenderer to use options
* Changed the interface of callRecursively
* Refactored the NodeInterface to be consistent
* Moved the creation of the item to the factory
* Added a Twig extension to render the menu easily
* Changed the menu provider interface with a pimple-based implementation
* Added a renderer provider to get a renderer by name and a Pimple-based implementation
* Removed the renderer from the menu
* Removed the num in the item by refactoring isLast and isFirst
* Changed the RendererInterface to accept an array of options to be more flexible
* Added an ItemInterface
* Initial import of KnpMenuBundle decoupled classes with a new namespace
