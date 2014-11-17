<?php

namespace Knp\Menu;

/**
 * Default implementation of the ItemInterface
 *
 * @package Knp\Menu
 */
class MenuItem implements ItemInterface
{
    /**
     * Name of this menu item (used for id by parent menu)
     *
     * @var string
     */
    protected $name = null;

    /**
     * Label to output, name is used by default
     *
     * @var string
     */
    protected $label = null;

    /**
     * Attributes for the item link
     *
     * @var array
     */
    protected $linkAttributes = array();

    /**
     * Attributes for the children list
     *
     * @var array
     */
    protected $childrenAttributes = array();

    /**
     * Attributes for the item text
     *
     * @var array
     */
    protected $labelAttributes = array();

    /**
     * Uri to use in the anchor tag
     *
     * @var string
     */
    protected $uri = null;

    /**
     * Attributes for the item
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Extra stuff associated to the item
     *
     * @var array
     */
    protected $extras = array();


    /**
     * Whether the item is displayed
     *
     * @var boolean
     */
    protected $display = true;

    /**
     * Whether the children of the item are displayed
     *
     * @var boolean
     */
    protected $displayChildren = true;

    /**
     * Child items
     *
     * @var ItemInterface[]
     */
    protected $children = array();

    /**
     * Parent item
     *
     * @var ItemInterface|null
     */
    protected $parent = null;

    /**
     * whether the item is current. null means unknown
     *
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
     * @param string           $name    The name of this menu, which is how its parent will
     *                                  reference it. Also used as label if label not specified
     * @param FactoryInterface $factory
     */
    public function __construct($name, FactoryInterface $factory)
    {
        $this->name    = (string)$name;
        $this->factory = $factory;
    }

    /**
     * setFactory
     *
     * @param FactoryInterface $factory
     *
     * @return self
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
     *
     * @return MenuItem|ItemInterface
     *
     * @throw \InvalidArgumentException
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

        $oldName    = $this->name;
        $this->name = $name;

        if (null !== $parent) {
            $names = array_keys($parent->getChildren());
            $items = array_values($parent->getChildren());

            $offset         = array_search($oldName, $names);
            $names[$offset] = $name;

            $parent->setChildren(array_combine($names, $items));
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return MenuItem|ItemInterface
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return ($this->label !== null) ? $this->label : $this->name;
    }

    /**
     * @param string $label
     *
     * @return MenuItem|ItemInterface
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
     *
     * @return MenuItem|ItemInterface
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem|ItemInterface
     */
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
     *
     * @return MenuItem|ItemInterface
     */
    public function setLinkAttributes(array $linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;

        return $this;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed|null
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
     *
     * @return MenuItem|ItemInterface
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
     *
     * @return MenuItem
     */
    public function setChildrenAttributes(array $childrenAttributes)
    {
        $this->childrenAttributes = $childrenAttributes;

        return $this;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return null
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
     *
     * @return MenuItem
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
     *
     * @return MenuItem
     */
    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return null
     */
    public function getLabelAttribute($name, $default = null)
    {
        if (isset($this->labelAttributes[$name])) {
            return $this->labelAttributes[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
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
     * @param array $extras
     *
     * @return MenuItem
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return null
     */
    public function getExtra($name, $default = null)
    {
        if (isset($this->extras[$name])) {
            return $this->extras[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
    public function setExtra($name, $value)
    {
        $this->extras[$name] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDisplayChildren()
    {
        return $this->displayChildren;
    }

    /**
     * @param bool $bool
     *
     * @return MenuItem
     */
    public function setDisplayChildren($bool)
    {
        $this->displayChildren = (bool)$bool;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->display;
    }

    /**
     * @param bool $bool
     *
     * @return MenuItem
     */
    public function setDisplay($bool)
    {
        $this->display = (bool)$bool;

        return $this;
    }

    /**
     * @param ItemInterface|string $child
     * @param array                $options
     *
     * @return ItemInterface|string
     *
     * @throw \InvalidArgumentException
     */
    public function addChild($child, array $options = array())
    {
        if (!$child instanceof ItemInterface) {
            $child = $this->factory->createItem($child, $options);
        } elseif (null !== $child->getParent()) {
            throw new \InvalidArgumentException(
                'Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).'
            );
        }

        $child->setParent($this);

        $this->children[$child->getName()] = $child;

        return $child;
    }

    /**
     * @param string $name
     *
     * @return ItemInterface|null
     */
    public function getChild($name)
    {
        return isset($this->children[$name]) ? $this->children[$name] : null;
    }

    /**
     * @param array $order
     *
     * @return MenuItem
     *
     * @throw \InvalidArgumentException
     */
    public function reorderChildren($order)
    {
        if (count($order) != $this->count()) {
            throw new \InvalidArgumentException('Cannot reorder children, order does not contain all children.');
        }

        $newChildren = array();

        foreach ($order as $name) {
            if (!isset($this->children[$name])) {
                throw new \InvalidArgumentException('Cannot find children named '.$name);
            }

            $child              = $this->children[$name];
            $newChildren[$name] = $child;
        }

        $this->setChildren($newChildren);

        return $this;
    }

    /**
     * @return MenuItem
     */
    public function copy()
    {
        $newMenu = clone $this;
        $newMenu->setChildren(array());
        $newMenu->setParent(null);
        foreach ($this->getChildren() as $child) {
            $newMenu->addChild($child->copy());
        }

        return $newMenu;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->parent ? $this->parent->getLevel() + 1 : 0;
    }

    /**
     * @return MenuItem
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
     * @return bool
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
     * @param ItemInterface $parent
     *
     * @return MenuItem
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
     * @param array $children
     *
     * @return MenuItem
     */
    public function setChildren(array $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @param ItemInterface|string $name
     *
     * @return MenuItem
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
     * @return mixed
     */
    public function getFirstChild()
    {
        return reset($this->children);
    }

    /**
     * @return mixed
     */
    public function getLastChild()
    {
        return end($this->children);
    }

    /**
     * @return bool
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
     * @param bool|null $bool
     *
     * @return MenuItem
     */
    public function setCurrent($bool)
    {
        $this->isCurrent = $bool;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isCurrent()
    {
        return $this->isCurrent;
    }

    /**
     * @return bool
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
     * @return bool
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
     * @return bool
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

        /** @var ItemInterface[] $children */
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
     * @return bool
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

        /** @var ItemInterface[] $children */
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
     * Implements Countable
     *
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Implements IteratorAggregate
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * Implements ArrayAccess
     *
     * @param mixed $name
     *
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * Implements ArrayAccess
     *
     * @param mixed $name
     *
     * @return ItemInterface|mixed|null
     */
    public function offsetGet($name)
    {
        return $this->getChild($name);
    }

    /**
     * Implements ArrayAccess
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return ItemInterface|void
     */
    public function offsetSet($name, $value)
    {
        return $this->addChild($name)->setLabel($value);
    }

    /**
     * Implements ArrayAccess
     *
     * @param mixed $name
     */
    public function offsetUnset($name)
    {
        $this->removeChild($name);
    }
}
