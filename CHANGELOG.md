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
