Control the sort order of your menu items
=========================================

There are 2 ways to control the order of your menu items:

1. Reorder items by giving a sorted list of item names
2. Sort the items when adding them to the menu

Reorder items by giving a sorted list of item names
-----------------------

```php
    $factory = new MenuFactory();
    $menu = new MenuItem('root', $factory);
    $menu->addChild('c1');
    $menu->addChild('c2');
    $menu->addChild('c3');
    $menu->addChild('c4');

    $menu->reorderChildren(['c4', 'c3', 'c2', 'c1']);
    $menu->getChildren() //'c4', 'c3', 'c2', 'c1'
```

Sort the items when adding them to the menu
-------------------------------------------

The items can be added in a sorted manner by using the `sortOrder` options.

Caution: The order will be lost when using `ItemInterface->reorderChildren()`!

```php
    $factory = new MenuFactory();
        $menu = new MenuItem('root', $factory);
        $menu->addChild('c1', ['sortOrder' => 2]);
        $menu->addChild('c2', ['sortOrder' => 4]);
        $menu->addChild('c3', ['sortOrder' => 1]);
        $menu->addChild('c4', ['sortOrder' => 3]);

        $menu->getChildren() //'c1', 'c2', 'c3', 'c4'
```

Items without the `sortOrder` option will be just appended after the items with `sortOrder` in the order they're added.