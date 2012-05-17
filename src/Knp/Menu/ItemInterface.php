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
     * @param  string $name
     * @return \Knp\Menu\ItemInterface
     */
    function setName($name);

    /**
     * @param  \Knp\Menu\FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
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
     * @param  string $uri The uri to set on this menu item
     * @return \Knp\Menu\ItemInterface
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
     * @param  string $label    The text to use when rendering this menu item
     * @return \Knp\Menu\ItemInterface
     */
    function setLabel($label);

    /**
     * @return array
     */
    function getAttributes();

    /**
     * Provides a fluent interface
     *
     * @param  array $attributes
     * @return \Knp\Menu\ItemInterface
     */
    function setAttributes(array $attributes);

    /**
     * @param  string $name     The name of the attribute to return
     * @param  mixed  $default  The value to return if the attribute doesn't exist
     * @return mixed
     */
    function getAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed $value
     * @return \Knp\Menu\ItemInterface
     */
    function setAttribute($name, $value);

    /**
     * @return array
     */
    function getLinkAttributes();

    /**
     * Provides a fluent interface
     *
     * @param  array $linkAttributes
     * @return \Knp\Menu\ItemInterface
     */
    function setLinkAttributes(array $linkAttributes);

    /**
     * @param  string $name     The name of the attribute to return
     * @param  mixed  $default  The value to return if the attribute doesn't exist
     * @return mixed
     */
    function getLinkAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param string $value
     * @return \Knp\Menu\ItemInterface
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
     * @return \Knp\Menu\ItemInterface
     */
    function setChildrenAttributes(array $childrenAttributes);

    /**
     * @param  string $name     The name of the attribute to return
     * @param  mixed  $default  The value to return if the attribute doesn't exist
     * @return mixed
     */
    function getChildrenAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param string $value
     * @return \Knp\Menu\ItemInterface
     */
    function setChildrenAttribute($name, $value);

    /**
     * @return array
     */
    function getLabelAttributes();

    /**
     * Provides a fluent interface
     *
     * @param  array $labelAttributes
     * @return \Knp\Menu\ItemInterface
     */
    function setLabelAttributes(array $labelAttributes);

    /**
     * @param  string $name     The name of the attribute to return
     * @param  mixed  $default  The value to return if the attribute doesn't exist
     * @return mixed
     */
    function getLabelAttribute($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed $value
     * @return \Knp\Menu\ItemInterface
     */
    function setLabelAttribute($name, $value);

    /**
     * @return array
     */
    function getExtras();

    /**
     * Provides a fluent interface
     *
     * @param  array $extras
     * @return \Knp\Menu\ItemInterface
     */
    function setExtras(array $extras);

    /**
     * @param  string $name     The name of the extra to return
     * @param  mixed  $default  The value to return if the extra doesn't exist
     * @return mixed
     */
    function getExtra($name, $default = null);

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed $value
     * @return \Knp\Menu\ItemInterface
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
     * @return \Knp\Menu\ItemInterface
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
     * @return \Knp\Menu\ItemInterface
     */
    function setDisplay($bool);

    /**
     * Add a child menu item to this menu
     *
     * Returns the child item
     *
     * @param mixed $child   An ItemInterface instance or the name of a new item to create
     * @param array $options If creating a new item, the options passed to the factory for the item
     * @return \Knp\Menu\ItemInterface
     */
    function addChild($child, array $options = array());

    /**
     * Returns the child menu identified by the given name
     *
     * @param  string $name  Then name of the child menu to return
     * @return \Knp\Menu\ItemInterface|null
     */
    function getChild($name);

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @param integer $position Position to move child to.
     * @return \Knp\Menu\ItemInterface
     */
    function moveToPosition($position);

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @param \Knp\Menu\ItemInterface $child Child to move.
     * @param integer $position Position to move child to.
     * @return \Knp\Menu\ItemInterface
     */
    function moveChildToPosition(ItemInterface $child, $position);

    /**
     * Moves child to first position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @return \Knp\Menu\ItemInterface
     */
    function moveToFirstPosition();

    /**
     * Moves child to last position. Rearange other children accordingly.
     *
     * Provides a fluent interface
     *
     * @return \Knp\Menu\ItemInterface
     */
    function moveToLastPosition();

    /**
     * Reorder children.
     *
     * Provides a fluent interface
     *
     * @param array $order New order of children.
     * @return \Knp\Menu\ItemInterface
     */
    function reorderChildren($order);

    /**
     * Makes a deep copy of menu tree. Every item is copied as another object.
     *
     * @return \Knp\Menu\ItemInterface
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
     * @return \Knp\Menu\ItemInterface
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
     * @return \Knp\Menu\ItemInterface
     */
    function getRoot();

    /**
     * Returns whether or not this menu item is the root menu item
     *
     * @return boolean
     */
    function isRoot();

    /**
     * @return MenuItem|null
     */
    function getParent();

    /**
     * Used internally when adding and removing children
     *
     * Provides a fluent interface
     *
     * @param \Knp\Menu\ItemInterface|null $parent
     * @return \Knp\Menu\ItemInterface
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
     * @param  array $children An array of ItemInterface objects
     * @return \Knp\Menu\ItemInterface
     */
    function setChildren(array $children);

    /**
     * Removes a child from this menu item
     *
     * Provides a fluent interface
     *
     * @param mixed $name The name of ItemInterface instance or the ItemInterface to remove
     * @return \Knp\Menu\ItemInterface
     */
    function removeChild($name);

    /**
     * @return \Knp\Menu\ItemInterface
     */
    function getFirstChild();

    /**
     * @return \Knp\Menu\ItemInterface
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
     * Renders an array of label => uri pairs ready to be used for breadcrumbs.
     *
     * The subItem can be one of the following forms
     *   * 'subItem'
     *   * array('subItem' => '@homepage')
     *   * array('subItem1', 'subItem2')
     *
     * @example
     * // drill down to the Documentation menu item, then add "Chapter 1" to the breadcrumb
     * $arr = $menu['Documentation']->getBreadcrumbsArray('Chapter 1');
     * foreach ($arr as $name => $url)
     * {
     *
     * }
     *
     * @param  mixed $subItem A string or array to append onto the end of the array
     * @return array
     */
    function getBreadcrumbsArray($subItem = null);

    /**
     * Returns the current menu item if it is a child of this menu item
     *
     * @return \Knp\Menu\ItemInterface|null
     * @deprecated this method is flawed and will be removed in 2.0
     * @see \Knp\Menu\Iterator\CurrentItemFilterIterator
     */
    function getCurrentItem();

    /**
     * Set whether or not this menu item is "current"
     *
     * Provides a fluent interface
     *
     * @param boolean $bool Specify that this menu item is current
     * @return \Knp\Menu\ItemInterface
     */
    function setCurrent($bool);

    /**
     * Get whether or not this menu item is "current"
     *
     * @return boolean
     */
    function isCurrent();

    /**
     * Returns whether or not this menu is an ancestor of the current menu item
     *
     * @return boolean
     */
    function isCurrentAncestor();

    /**
     * Whether or not this menu item is last in its parent
     *
     * @return boolean
     */
    function isLast();

    /**
     * Whether or not this menu item is first in its parent
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
     * Returns the current uri, which is used for determining the current
     * menu item.
     *
     * If the uri isn't set, this asks the parent menu for its current uri.
     * This would recurse up the tree until the root is hit. Once the root
     * is hit, if it still doesn't know the currentUri, it gets it from the
     * request object.
     *
     * @return string
     */
    function getCurrentUri();

    /**
     * Sets the current uri, used when determining the current menu item
     *
     * This will set the current uri on the root menu item, which all other
     * menu items will use
     *
     * Provides a fluent interface
     *
     * @param string $uri
     * @return \Knp\Menu\ItemInterface
     */
    function setCurrentUri($uri);

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
     * @return \Knp\Menu\ItemInterface
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
