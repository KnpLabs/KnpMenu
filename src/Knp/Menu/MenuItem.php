<?php

namespace Knp\Menu;

/**
 * Default implementation of the ItemInterface
 */
class MenuItem implements ItemInterface
{
    /**
     * Name of this menu item (used for id by parent menu)
     * @var string
     */
    protected $name = null;
    /**
     * Label to output, name is used by default
     * @var string
     */
    protected $label = null;
    /**
     * Attributes for the item link
     * @var array
     */
    protected $linkAttributes = array();
    /**
     * Attributes for the children list
     * @var array
     */
    protected $childrenAttributes = array();
    /**
     * Attributes for the item text
     * @var array
     */
    protected $labelAttributes = array();
    /**
     * Uri to use in the anchor tag
     * @var string
     */
    protected $uri = null;
    /**
     * Attributes for the item
     * @var array
     */
    protected $attributes = array();
    /**
     * Extra stuff associated to the item
     * @var array
     */
    protected $extras = array();

    /**
     * Whether the item is displayed
     * @var boolean
     */
    protected $display = true;
    /**
     * Whether the children of the item are displayed
     * @var boolean
     */
    protected $displayChildren = true;

    /**
     * Child items
     * @var ItemInterface[]
     */
    protected $children = array();
    /**
     * Parent item
     * @var ItemInterface|null
     */
    protected $parent = null;
    /**
     * whether the item is current. null means unknown
     * @var boolean|null
     */
    protected $isCurrent = null;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Class constructor
     *
     * @param string $name The name of this menu, which is how its parent will
     *                     reference it. Also used as label if label not specified
     * @param FactoryInterface $factory
     */
    public function __construct($name, FactoryInterface $factory)
    {
        $this->name = (string) $name;
        $this->factory = $factory;
    }

    /**
     * @param FactoryInterface $factory
     * @return ItemInterface
     */
    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @throws \InvalidArgumentException if the name is already used by a sibling
     * @return ItemInterface
     */
    public function setName($name)
    {
        if ($this->name == $name) {
            return $this;
        }

        $parent = $this->getParent();
        if (null !== $parent && isset($parent[$name])) {
            throw new \InvalidArgumentException('Cannot rename item, name is already used by sibling.');
        }

        $oldName = $this->name;
        $this->name = $name;

        if (null !== $parent) {
            $names = array_keys($parent->getChildren());
            $items = array_values($parent->getChildren());

            $offset = array_search($oldName, $names);
            $names[$offset] = $name;

            $parent->setChildren(array_combine($names, $items));
        }

        return $this;
    }

    /**
     * Get the uri for a menu item
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set the uri for a menu item
     *
     * @param string $uri The uri to set on this menu item
     * @return ItemInterface
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Returns the label that will be used to render this menu item
     *
     * Defaults to the name of no label was specified
     *
     * @return string
     */
    public function getLabel()
    {
        return ($this->label !== null) ? $this->label : $this->name;
    }

    /**
     * @param string $label The text to use when rendering this menu item
     * @return ItemInterface
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return ItemInterface
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    /**
     * @param array $linkAttributes
     * @return ItemInterface
     */
    public function setLinkAttributes(array $linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;

        return $this;
    }

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     * @return mixed
     */
    public function getLinkAttribute($name, $default = null)
    {
        if (isset($this->linkAttributes[$name])) {
            return $this->linkAttributes[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param string $value
     * @return ItemInterface
     */
    public function setLinkAttribute($name, $value)
    {
        $this->linkAttributes[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    /**
     * @param array $childrenAttributes
     * @return ItemInterface
     */
    public function setChildrenAttributes(array $childrenAttributes)
    {
        $this->childrenAttributes = $childrenAttributes;

        return $this;
    }

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     * @return mixed
     */
    public function getChildrenAttribute($name, $default = null)
    {
        if (isset($this->childrenAttributes[$name])) {
            return $this->childrenAttributes[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param string $value
     * @return ItemInterface
     */
    public function setChildrenAttribute($name, $value)
    {
        $this->childrenAttributes[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * @param array $labelAttributes
     * @return ItemInterface
     */
    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    /**
     * @param string $name    The name of the attribute to return
     * @param mixed  $default The value to return if the attribute doesn't exist
     * @return mixed
     */
    public function getLabelAttribute($name, $default = null)
    {
        if (isset($this->labelAttributes[$name])) {
            return $this->labelAttributes[$name];
        }

        return $default;
    }

    public function setLabelAttribute($name, $value)
    {
        $this->labelAttributes[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Provides a fluent interface
     *
     * @param array $extras
     * @return ItemInterface
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * @param string $name    The name of the extra to return
     * @param mixed  $default The value to return if the extra doesn't exist
     * @return mixed
     */
    public function getExtra($name, $default = null)
    {
        if (isset($this->extras[$name])) {
            return $this->extras[$name];
        }

        return $default;
    }

    /**
     * Provides a fluent interface
     *
     * @param string $name
     * @param mixed $value
     * @return ItemInterface
     */
    public function setExtra($name, $value)
    {
        $this->extras[$name] = $value;

        return $this;
    }

    /**
     * Whether or not this menu item should show its children.
     *
     * @return boolean
     */
    public function getDisplayChildren()
    {
        return $this->displayChildren;
    }

    /**
     * Set whether or not this menu item should show its children
     *
     * @param boolean $bool
     * @return ItemInterface
     */
    public function setDisplayChildren($bool)
    {
        $this->displayChildren = (bool) $bool;

        return $this;
    }

    /**
     * Whether or not to display this menu item
     *
     * @return boolean
     */
    public function isDisplayed()
    {
        return $this->display;
    }

    /**
     * Set whether or not this menu to show this menu item
     *
     * @param boolean $bool
     * @return ItemInterface
     */
    public function setDisplay($bool)
    {
        $this->display = (bool) $bool;

        return $this;
    }

    /**
     * Add a child menu item to this menu
     *
     * Returns the child item
     *
     * @param mixed $child   An ItemInterface instance or the name of a new item to create
     * @param array $options If creating a new item, the options passed to the factory for the item
     * @throws \InvalidArgumentException if the item is already in a tree
     * @return ItemInterface
     */
    public function addChild($child, array $options = array())
    {
        if (!$child instanceof ItemInterface) {
            $child = $this->factory->createItem($child, $options);
        } elseif (null !== $child->getParent()) {
            throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
        }

        $child->setParent($this);

        $this->children[$child->getName()] = $child;

        return $child;
    }

    /**
     * Returns the child menu identified by the given name
     *
     * @param string $name Then name of the child menu to return
     * @return ItemInterface|null
     */
    public function getChild($name)
    {
        return isset($this->children[$name]) ? $this->children[$name] : null;
    }

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * @param integer $position Position to move child to.
     * @return ItemInterface
     */
    public function moveToPosition($position)
    {
        $this->getParent()->moveChildToPosition($this, $position);

        return $this;
    }

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * @param ItemInterface $child    Child to move.
     * @param integer       $position Position to move child to.
     * @return ItemInterface
     */
    public function moveChildToPosition(ItemInterface $child, $position)
    {
        $name = $child->getName();
        $order = array_keys($this->children);

        $oldPosition = array_search($name, $order);
        unset($order[$oldPosition]);

        $order = array_values($order);

        array_splice($order, $position, 0, $name);
        $this->reorderChildren($order);

        return $this;
    }

    /**
     * Moves child to first position. Rearange other children accordingly.
     *
     * @return ItemInterface
     */
    public function moveToFirstPosition()
    {
        return $this->moveToPosition(0);
    }

    /**
     * Moves child to last position. Rearange other children accordingly.
     *
     * @return ItemInterface
     */
    public function moveToLastPosition()
    {
        return $this->moveToPosition($this->getParent()->count());
    }

    /**
     * Reorder children.
     *
     * @param array $order New order of children.
     * @throws \InvalidArgumentException
     * @return ItemInterface
     */
    public function reorderChildren($order)
    {
        if (count($order) != $this->count()) {
            throw new \InvalidArgumentException('Cannot reorder children, order does not contain all children.');
        }

        $newChildren = array();

        foreach ($order as $name) {
            if (!isset($this->children[$name])) {
                throw new \InvalidArgumentException('Cannot find children named ' . $name);
            }

            $child = $this->children[$name];
            $newChildren[$name] = $child;
        }

        $this->children = $newChildren;

        return $this;
    }

    /**
     * Makes a deep copy of menu tree. Every item is copied as another object.
     *
     * @return ItemInterface
     */
    public function copy()
    {
        $newMenu = clone $this;
        $newMenu->children = array();
        $newMenu->setParent(null);
        foreach ($this->getChildren() as $child) {
            $newMenu->addChild($child->copy());
        }

        return $newMenu;
    }

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
    public function slice($offset, $length = null)
    {
        $names = array_keys($this->getChildren());
        if ($offset instanceof ItemInterface) {
            $offset = $offset->getName();
        }
        if (!is_numeric($offset)) {
            $offset = array_search($offset, $names);
        }

        if (null !== $length) {
            if ($length instanceof ItemInterface) {
                $length = $length->getName();
            }
            if (!is_numeric($length)) {
                $index = array_search($length, $names);
                $length = ($index < $offset) ? 0 : $index - $offset;
            }
        }
        $item = $this->copy();
        $children =  array_slice($item->getChildren(), $offset, $length);
        $item->setChildren($children);

        return $item;
    }

    /**
     * Split menu into two distinct menus.
     *
     * @param mixed $length Name of child, child object, or numeric length.
     * @return array Array with two menus, with "primary" and "secondary" key
     */
    public function split($length)
    {
        $ret = array();
        $ret['primary'] = $this->slice(0, $length);
        $ret['secondary'] = $this->slice($length);

        return $ret;
    }

    /**
     * Returns the level of this menu item
     *
     * The root menu item is 0, followed by 1, 2, etc
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->parent ? $this->parent->getLevel() + 1 : 0;
    }

    /**
     * Returns the root MenuItem of this menu tree
     *
     * @return ItemInterface
     */
    public function getRoot()
    {
        $obj = $this;
        do {
            $found = $obj;
        } while ($obj = $obj->getParent());

        return $found;
    }

    /**
     * Returns whether or not this menu item is the root menu item
     *
     * @return boolean
     */
    public function isRoot()
    {
        return null === $this->parent;
    }

    /**
     * @return ItemInterface|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Used internally when adding and removing children
     *
     * @param ItemInterface $parent
     * @return ItemInterface
     */
    public function setParent(ItemInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return ItemInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param array $children An array of ItemInterface objects
     * @return ItemInterface
     */
    public function setChildren(array $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Removes a child from this menu item
     *
     * @param ItemInterface|string $name The name of ItemInterface instance or the ItemInterface to remove
     * @return ItemInterface
     */
    public function removeChild($name)
    {
        $name = $name instanceof ItemInterface ? $name->getName() : $name;

        if (isset($this->children[$name])) {
            // unset the child and reset it so it looks independent
            $this->children[$name]->setParent(null);
            unset($this->children[$name]);
        }

        return $this;
    }

    /**
     * @return ItemInterface
     */
    public function getFirstChild()
    {
        return reset($this->children);
    }

    /**
     * @return ItemInterface
     */
    public function getLastChild()
    {
        return end($this->children);
    }

    /**
     * Returns whether or not this menu items has viewable children
     *
     * This menu MAY have children, but this will return false if the current
     * user does not have access to view any of those items
     *
     * @return boolean
     */
    public function hasChildren()
    {
        foreach ($this->children as $child) {
            if ($child->isDisplayed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     *
     * @param string $separator
     * @return string
     */
    public function getPathAsString($separator = ' > ')
    {
        $children = array();
        $obj = $this;

        do {
            $children[] = $obj->getLabel();
        } while ($obj = $obj->getParent());

        return implode($separator, array_reverse($children));
    }

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
     *   * Knp\Menu\ItemInterface object
     *   * array('subItem' => '@homepage')
     *   * array('subItem1', 'subItem2')
     *   * array(array('label' => 'subItem1', 'url' => '@homepage'), array('label' => 'subItem2'))
     *
     * @param mixed   $subItem A string or array to append onto the end of the array
     * @param boolean $strict  Internal flag to optimize the lookup in parent nodes
     * @throws \InvalidArgumentException if an element of the subItem is invalid
     * @return array
     */
    public function getBreadcrumbsArray($subItem = null, $strict = false)
    {
        $breadcrumbs = array();

        if ($strict) {
            $breadcrumbs = $subItem;
        } else {
            if ($subItem instanceof ItemInterface) {
                $subItem = array(array(
                    'label' => $subItem->getLabel(),
                    'uri' => $subItem->getUri(),
                    'item' => $subItem,
                ));
            }
            if (null === $subItem) {
                $subItem = array();
            }
            if (!is_array($subItem) && !$subItem instanceof \Traversable) {
                $subItem = array($subItem);
            }

            foreach ($subItem as $key => $value) {
                switch (true) {
                    case $value instanceof ItemInterface:
                        $value = array(
                            'label' => $value->getLabel(),
                            'uri' => $value->getUri(),
                            'item' => $value,
                        );
                        break;
                    case is_array($value):
                        // Assume we already have the appropriate array format for the element
                        break;
                    case is_integer($key) && is_string($value):
                        $value = array(
                            'label' => (string) $value,
                            'uri' => null,
                            'item' => null,
                        );
                        break;
                    case is_scalar($value):
                        $value = array(
                            'label' => (string) $key,
                            'uri' => (string) $value,
                            'item' => null,
                        );
                        break;
                    case null === $value:
                        $value = array(
                            'label' => (string) $key,
                            'uri' => null,
                            'item' => null,
                        );
                        break;
                    default:
                        throw new \InvalidArgumentException(sprintf('Invalid value supplied for the key "%s". It should be an item, an array or a scalar', $key));
                }
                $breadcrumbs[] = $value;
            }
        }

        array_unshift($breadcrumbs, array(
            'label' => $this->getLabel(),
            'uri' => $this->getUri(),
            'item' => $this,
        ));

        if ($this->isRoot()) {
            return $breadcrumbs;
        }

        return $this->getParent()->getBreadcrumbsArray($breadcrumbs, true);
    }

    /**
     * Set whether or not this menu item is "current".
     *
     * If the state is unknown, use null.
     *
     * @param boolean|null $bool Specify that this menu item is current
     * @return ItemInterface
     */
    public function setCurrent($bool)
    {
        $this->isCurrent = $bool;

        return $this;
    }

    /**
     * Get whether or not this menu item is "current"
     *
     * @return boolean|null
     */
    public function isCurrent()
    {
        return $this->isCurrent;
    }

    /**
     * Whether this menu item is last in its parent
     *
     * @return boolean
     */
    public function isLast()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getLastChild() === $this;
    }

    /**
     * Whether this menu item is first in its parent
     *
     * @return boolean
     */
    public function isFirst()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getFirstChild() === $this;
    }

    /**
     * Whereas isFirst() returns if this is the first child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the first child that would be rendered
     * for the current user
     *
     * @return boolean
     */
    public function actsLikeFirst()
    {
        // root items are never "marked" as first
        if ($this->isRoot()) {
            return false;
        }

        // A menu acts like first only if it is displayed
        if (!$this->isDisplayed()) {
            return false;
        }

        // if we're first and visible, we're first, period.
        if ($this->isFirst()) {
            return true;
        }

        $children = $this->getParent()->getChildren();
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->isDisplayed()) {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

    /**
     * Whereas isLast() returns if this is the last child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the last child that would be rendered
     * for the current user
     *
     * @return boolean
     */
    public function actsLikeLast()
    {
        // root items are never "marked" as last
        if ($this->isRoot()) {
            return false;
        }

        // A menu acts like last only if it is displayed
        if (!$this->isDisplayed()) {
            return false;
        }

        // if we're last and visible, we're last, period.
        if ($this->isLast()) {
            return true;
        }

        $children = array_reverse($this->getParent()->getChildren());
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->isDisplayed()) {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

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
    public function callRecursively($method, $arguments = array())
    {
        call_user_func_array(array($this, $method), $arguments);

        foreach ($this->children as $child) {
            $child->callRecursively($method, $arguments);
        }

        return $this;
    }

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
    public function toArray($depth = null)
    {
        $array = array(
            'name' => $this->name,
            'label' => $this->label,
            'uri' => $this->uri,
            'attributes' => $this->attributes,
            'labelAttributes' => $this->labelAttributes,
            'linkAttributes' => $this->linkAttributes,
            'childrenAttributes' => $this->childrenAttributes,
            'extras' => $this->extras,
            'display' => $this->display,
            'displayChildren' => $this->displayChildren,
        );

        // export the children as well, unless explicitly disabled
        if (0 !== $depth) {
            $childDepth = (null === $depth) ? null : $depth - 1;
            $array['children'] = array();
            foreach ($this->children as $key => $child) {
                $array['children'][$key] = $child->toArray($childDepth);
            }
        }

        return $array;
    }

    /**
     * Implements Countable
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Implements IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetGet($name)
    {
        return $this->getChild($name);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetSet($name, $value)
    {
        return $this->addChild($name)->setLabel($value);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetUnset($name)
    {
        $this->removeChild($name);
    }
}
