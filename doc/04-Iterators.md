Iterating over Menus
====================

The simplest way to iterate over your item object is to iterate over its
direct children, thanks to the `IteratorAggregate` implementation. But the
PHP iterators allow much more powerful options to deal with our tree structure.

All the examples in this chapter will use the following tree:

```
         A
       /   \
     /       \
   B          C
 / | \      /   \
D  E  F    G     H
|          |
I          J
```

Iterating recursively
---------------------

The `Knp\Menu\Iterator\RecursiveItemIterator` allows you to iterate recursively
over a tree of items. Using it is really easy: give it an item, and you will
have a recursive iterator on this item.

```php
<?php

$menu = /* get your root item from somewhere */;

// create the iterator
$itemIterator = new \Knp\Menu\Iterator\RecursiveItemIterator($menu);

// iterate recursively on the iterator
$iterator = new \RecursiveIteratorIterator($itemIterator, \RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $item) {
    echo $item->getName() . " ";
}
```

The output will be:

```
B D I E F C G J H
```

Changing the second argument to `\RecursiveIteratorIterator::CHILD_FIRST`
allows you to visit children before their parent and will produce the following
order:

```
I D E F B J G H C
```

>**NOTE**
>If you iterate over the `RecursiveItemIterator` without wrapping it in the
>`\RecursiveIteratorIterator`, it will simply give you the direct children
>like when using the iterator of the item.

As you can see, the final iterator does not contain the root item (``A``).
The reason is simple: the recursive iteration started only on its children.
Fortunately, the `RecursiveItemIterator` accept any iterator over menu items,
not only items themselves. This allows you to add the root item in the final
iterator by changing the iterator wrapped in the `RecursiveItemIterator`:

```php
<?php

$menu = /* get your root item from somewhere */;

// create an iterator containing only the root item
$rootIterator = new \ArrayIterator(array($menu));

// create the item iterator
$itemIterator = new \Knp\Menu\Iterator\RecursiveItemIterator($rootIterator);

// iterate recursively on the iterator
$iterator = new \RecursiveIteratorIterator($itemIterator, \RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $item) {
    echo $item->getName() . " ";
}
```

The output will now contain the root item:

```
A B D I E F C G J H
```

Filtering only current items
----------------------------

Getting the current items is easy with the `Knp\Menu\Iterator\CurrentItemFilterIterator`.
It is a filter iterator applied on another iterator.

```php
<?php

$root = /* get your root item from somewhere */;
$menu = $root['B'];

$itemMatcher = new \Knp\Menu\Matcher\Matcher();

// create the iterator
$iterator = new \Knp\Menu\Iterator\CurrentItemFilterIterator($menu->getIterator(), $itemMatcher);

foreach ($iterator as $item) {
    echo $item->getName() . " ";
}
```

Assuming that D and F are current whereas E is not, this will output ``D F``.

Getting the current item of the whole tree is simply done by applying the
filter on the previous recursive iterator:

```php
<?php

$menu = /* get your root item from somewhere */;

$itemMatcher = new \Knp\Menu\Matcher\Matcher();

$treeIterator = new \RecursiveIteratorIterator(
    new \Knp\Menu\Iterator\RecursiveItemIterator(
        new \ArrayIterator(array($menu))
    ),
    \RecursiveIteratorIterator::SELF_FIRST
);

$iterator = new \Knp\Menu\Iterator\CurrentItemFilterIterator($treeIterator, $itemMatcher);

foreach ($iterator as $item) {
    echo $item->getName() . " ";
}
```

Filtering only displayed items
------------------------------

The `Knp\Menu\Iterator\DisplayedItemFilterIterator` allows you to filter
items to keep only displayed ones. As hiding an item also hides its children,
this filter is a recursive filter iterator and is applied on the recursive
iterator, not on the flattened iterator.

```php
<?php

$menu = /* get your root item from somewhere */;

// create an iterator containing only the root item
$rootIterator = new \ArrayIterator(array($menu));

// create the item iterator
$itemIterator = new \Knp\Menu\Iterator\RecursiveItemIterator($rootIterator);

// wrap the iterator in the filter iterator
$filteredIterator = new \Knp\Menu\Iterator\DisplayedItemFilterIterator($itemIterator);

// iterate recursively on the iterator
$iterator = new \RecursiveIteratorIterator($filteredIterator, \RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $item) {
    echo $item->getName() . " ";
}
```

Assuming that E and F are hidden, and that C is displayed but hides its children,
the output will be:

```
A B D I C
```
