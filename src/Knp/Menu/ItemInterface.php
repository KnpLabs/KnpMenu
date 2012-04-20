<?php

namespace Knp\Menu;

/**
 * Interface implemented by a menu item.
 *
 * It roughly represents a single <li> tag and is what you should interact with
 * most of the time by default.
 * Originally taken from ioMenuPlugin (http://github.com/weaverryan/ioMenuPlugin)
 */
interface ItemInterface extends  \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @return string
     */
    function getName();

    /**
     * Renames the item.
     *
     * This method must also update the key in the parent.
     *
     * Provides a fluent interface
     *
     * @param string $name
     * @return ItemInterface
     */
    function setName($name);

    /**
     * @param FactoryInterface $factory
     * @return ItemInterface
     */
    function setFactory(FactoryInterface $factory);

    /**
     * Get the uri for a menu item
     *
     * @return string
     */
    function getUri();

    /**
     * Set the uri for a menu item
     *
     * Provides a fluent interface
     *
     * @param string $uri The uri to set on this menu item
     * @return ItemInterface
     */
    function setUri($uri);

    /**
     * Returns the label that will be used to render this menu item
     *
     * Defaults to the name of no label was specified
     *
     * @return string
     */
    function getLabel();

    /**
     * Provides a fluent interface
     *
     * @param string $label The text to use when rendering this menu item
     * @return ItemInterface
     */
    function setLabel($label);

    /**
     * @return array
     */
    function getAttributes();

    /**
     * Provides a fluent interface
     *
     * @param array $attributes
     * @return ItemInterface
     */
    function setAttributes(array $attributes);

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     * @return mixed
     */
    function getAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed $value
     * @return ItemInterface
     */
    function setAttribute($name, $value);

    /**
     * @return array
     */
    function getLinkAttributes();

    /**
     * Provides a fluent interface
     *
     * @param array $linkAttributes
     * @return ItemInterface
     */
    function setLinkAttributes(array $linkAttributes);

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     * @return mixed
     */
    function getLinkAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param string $value
     * @return ItemInterface
     */
    function setLinkAttribute($name, $value);

    /**
     * @return array
     */
    function getChildrenAttributes();

    /**
     * Provides a fluent interface
     *
     * @param  array $childrenAttributes
     * @return ItemInterface
     */
    function setChildrenAttributes(array $childrenAttributes);

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     * @return mixed
     */
    function getChildrenAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param string $value
     * @return ItemInterface
     */
    function setChildrenAttribute($name, $value);

    /**
     * @return array
     */
    function getLabelAttributes();

    /**
     * Provides a fluent interface
     *
     * @param array $labelAttributes
     * @return ItemInterface
     */
    function setLabelAttributes(array $labelAttributes);

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     * @return mixed
     */
    function getLabelAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed $value
     * @return ItemInterface
     */
    function setLabelAttribute($name, $value);

    /**
     * @return array
     */
    function getExtras();

    /**
     * Provides a fluent interface
     *
     * @param array $extras
     * @return ItemInterface
     */
    function setExtras(array $extras);

    /**
     * @param string $name    The name of the extra to return
     * @param mixed  $default The value to return if the extra doesn't exist
     * @return mixed
     */
    function getExtra($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed $value
     * @return ItemInterface
     */
    function setExtra($name, $value);

    /**
     * Whether or not this menu item should show its children.
     *
     * @return boolean
     */
    function getDisplayChildren();

    /**
     * Set whether or not this menu item should show its children
     *
     * Provides a fluent interface
     *
     * @param boolean $bool
     * @return ItemInterface
     */
    function setDisplayChildren($bool);

    /**
     * Whether or not to display this menu item
     *
     * @return boolean
     */
    function isDisplayed();

    /**
     * Set whether or not this menu should be displayed
     *
     * Provides a fluent interface
     *
     * @param boolean $bool
     * @return ItemInterface
     */
    function setDisplay($bool);

    /**
     * Add a child menu item to this menu
     *
     * Returns the child item
     *
     * @param mixed $child   An ItemInterface instance or the name of a new item to create
     * @param array $options If creating a new item, the options passed to the factory for the item
     * @return ItemInterface
     */
    function addChild($child, array $options = array());

    /**
     * Returns the child menu identified by the given name
     *
     * @param string $name Then name of the child menu to return
     * @return ItemInterface|null
     */
    function getChild($name);

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @param integer $position Position to move child to.
     * @return ItemInterface
     */
    function moveToPosition($position);

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @param ItemInterface $child    Child to move.
     * @param integer       $position Position to move child to.
     * @return ItemInterface
     */
    function moveChildToPosition(ItemInterface $child, $position);

    /**
     * Moves child to first position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @return ItemInterface
     */
    function moveToFirstPosition();

    /**
     * Moves child to last position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @return ItemInterface
     */
    function moveToLastPosition();

    /**
     * Reorder children.
     *
     * Provides a fluent interface
     *
     * @param array $order New order of children.
     * @return ItemInterface
     */
    function reorderChildren($order);

    /**
     * Makes a deep copy of menu tree. Every item is copied as another object.
     *
     * @return ItemInterface
     */
    function copy();

    /**
     * Get slice of menu as another menu.
     *
     * If offset and/or length are numeric, it works like in array_slice function:
     *
     *   If offset is non-negative, slice will start at the offset.
     *   If offset is negative, slice will start that far from the end.
     *
     *   If length is null, slice will have all elements.
     *   If length is positive, slice will have that many elements.
     *   If length is negative, slice will stop that far from the end.
     *
     * It's possible to mix names/object/numeric, for example:
     *   slice("child1", 2);
     *   slice(3, $child5);
     * Note: when using a child as limit, it will not be included in the returned menu.
     * the slice is done before this menu.
     *
     * @param mixed $offset Name of child, child object, or numeric offset.
     * @param mixed $length Name of child, child object, or numeric length.
     * @return ItemInterface
     */
    function slice($offset, $length = 0);

    /**
     * Split menu into two distinct menus.
     *
     * @param mixed $length Name of child, child object, or numeric length.
     * @return array Array with two menus, with "primary" and "secondary" key
     */
    function split($length);

    /**
     * Returns the level of this menu item
     *
     * The root menu item is 0, followed by 1, 2, etc
     *
     * @return integer
     */
    function getLevel();

    /**
     * Returns the root ItemInterface of this menu tree
     *
     * @return ItemInterface
     */
    function getRoot();

    /**
     * Returns whether or not this menu item is the root menu item
     *
     * @return boolean
     */
    function isRoot();

    /**
     * @return ItemInterface|null
     */
    function getParent();

    /**
     * Used internally when adding and removing children
     *
     * Provides a fluent interface
     *
     * @param ItemInterface|null $parent
     * @return ItemInterface
     */
    function setParent(ItemInterface $parent = null);

    /**
     * Return the children as an array of ItemInterface objects
     *
     * @return array
     */
    function getChildren();

    /**
     * Provides a fluent interface
     *
     * @param array $children An array of ItemInterface objects
     * @return ItemInterface
     */
    function setChildren(array $children);

    /**
     * Removes a child from this menu item
     *
     * Provides a fluent interface
     *
     * @param ItemInterface|string $name The name of ItemInterface instance or the ItemInterface to remove
     * @return ItemInterface
     */
    function removeChild($name);

    /**
     * @return ItemInterface
     */
    function getFirstChild();

    /**
     * @return ItemInterface
     */
    function getLastChild();

    /**
     * Returns whether or not this menu items has viewable children
     *
     * This menu MAY have children, but this will return false if the current
     * user does not have access to view any of those items
     *
     * @return boolean
     */
    function hasChildren();

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     *
     * @param string $separator
     * @return string
     */
    function getPathAsString($separator = ' > ');

    /**
     * Renders an array ready to be used for breadcrumbs.
     *
     * Each element in the array will be an array with 3 keys:
     * - `label` containing the label of the item
     * - `url` containing the url of the item (may be `null`)
     * - `item` containing the original item (may be `null` for the extra items)
     *
     * The subItem can be one of the following forms
     *   * 'subItem'
     *   * ItemInterface object
     *   * array('subItem' => '@homepage')
     *   * array('subItem1', 'subItem2')
     *   * array(array('label' => 'subItem1', 'url' => '@homepage'), array('label' => 'subItem2'))
     *
     * @param mixed $subItem A string or array to append onto the end of the array
     * @return array
     */
    function getBreadcrumbsArray($subItem = null);

    /**
     * Returns the current menu item if it is a child of this menu item
     *
     * @return ItemInterface|null
     * @deprecated this method is flawed and will be removed in 2.0
     * @see \Knp\Menu\Iterator\CurrentItemFilterIterator
     */
    function getCurrentItem();

    /**
     * Sets whether or not this menu item is "current".
     *
     * If the state is unknown, use null.
     *
     * Provides a fluent interface
     *
     * @param boolean|null $bool Specify that this menu item is current
     * @return ItemInterface
     */
    function setCurrent($bool);

    /**
     * Gets whether or not this menu item is "current".
     *
     * @return boolean|null
     */
    function isCurrent();

    /**
     * Whether this menu item is last in its parent
     *
     * @return boolean
     */
    function isLast();

    /**
     * Whether this menu item is first in its parent
     *
     * @return boolean
     */
    function isFirst();

    /**
     * Whereas isFirst() returns if this is the first child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the first child that would be rendered
     * for the current user
     *
     * @return boolean
     */
    function actsLikeFirst();

    /**
     * Whereas isLast() returns if this is the last child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the last child that would be rendered
     * for the current user
     *
     * @return boolean
     */
    function actsLikeLast();

    /**
     * Calls a method recursively on all of the children of this item
     *
     * @example
     * $menu->callRecursively('setShowChildren', array(false));
     *
     * Provides a fluent interface
     *
     * @param string $method
     * @param array $arguments
     * @return ItemInterface
     */
    function callRecursively($method, $arguments = array());

    /**
     * Exports this menu item to an array
     *
     * The children are exported until the specified depth:
     *      null: no limit
     *      0: no children
     *      1: only direct children
     *      ...
     *
     * @param integer $depth
     * @return array
     */
    function toArray($depth = null);
}
