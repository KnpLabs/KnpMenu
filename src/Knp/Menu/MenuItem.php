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

    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

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

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function getLabel()
    {
        return ($this->label !== null) ? $this->label : $this->name;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

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

    public function getLinkAttributes()
    {
        return $this->linkAttributes;
    }

    public function setLinkAttributes(array $linkAttributes)
    {
        $this->linkAttributes = $linkAttributes;

        return $this;
    }

    public function getLinkAttribute($name, $default = null)
    {
        if (isset($this->linkAttributes[$name])) {
            return $this->linkAttributes[$name];
        }

        return $default;
    }

    public function setLinkAttribute($name, $value)
    {
        $this->linkAttributes[$name] = $value;

        return $this;
    }

    public function getChildrenAttributes()
    {
        return $this->childrenAttributes;
    }

    public function setChildrenAttributes(array $childrenAttributes)
    {
        $this->childrenAttributes = $childrenAttributes;

        return $this;
    }

    public function getChildrenAttribute($name, $default = null)
    {
        if (isset($this->childrenAttributes[$name])) {
            return $this->childrenAttributes[$name];
        }

        return $default;
    }

    public function setChildrenAttribute($name, $value)
    {
        $this->childrenAttributes[$name] = $value;

        return $this;
    }

    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

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

    public function getExtras()
    {
        return $this->extras;
    }

    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    public function getExtra($name, $default = null)
    {
        if (isset($this->extras[$name])) {
            return $this->extras[$name];
        }

        return $default;
    }

    public function setExtra($name, $value)
    {
        $this->extras[$name] = $value;

        return $this;
    }

    public function getDisplayChildren()
    {
        return $this->displayChildren;
    }

    public function setDisplayChildren($bool)
    {
        $this->displayChildren = (bool) $bool;

        return $this;
    }

    public function isDisplayed()
    {
        return $this->display;
    }

    public function setDisplay($bool)
    {
        $this->display = (bool) $bool;

        return $this;
    }

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

    public function getChild($name)
    {
        return isset($this->children[$name]) ? $this->children[$name] : null;
    }

    public function moveToPosition($position)
    {
        $this->getParent()->moveChildToPosition($this, $position);

        return $this;
    }

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

    public function moveToFirstPosition()
    {
        return $this->moveToPosition(0);
    }

    public function moveToLastPosition()
    {
        return $this->moveToPosition($this->getParent()->count());
    }

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

    public function split($length)
    {
        $ret = array();
        $ret['primary'] = $this->slice(0, $length);
        $ret['secondary'] = $this->slice($length);

        return $ret;
    }

    public function getLevel()
    {
        return $this->parent ? $this->parent->getLevel() + 1 : 0;
    }

    public function getRoot()
    {
        $obj = $this;
        do {
            $found = $obj;
        } while ($obj = $obj->getParent());

        return $found;
    }

    public function isRoot()
    {
        return null === $this->parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(ItemInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren(array $children)
    {
        $this->children = $children;

        return $this;
    }

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

    public function getFirstChild()
    {
        return reset($this->children);
    }

    public function getLastChild()
    {
        return end($this->children);
    }

    public function hasChildren()
    {
        foreach ($this->children as $child) {
            if ($child->isDisplayed()) {
                return true;
            }
        }

        return false;
    }

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
     * {@inheritDoc}
     *
     * @param  mixed                     $subItem A string or array to append onto the end of the array
     * @param  boolean                   $strict  Internal flag to optimize the lookup in parent nodes
     *
     * @return array
     * @throws \InvalidArgumentException if an element of the subItem is invalid
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

    public function setCurrent($bool)
    {
        $this->isCurrent = $bool;

        return $this;
    }

    public function isCurrent()
    {
        return $this->isCurrent;
    }

    public function isLast()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getLastChild() === $this;
    }

    public function isFirst()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getParent()->getFirstChild() === $this;
    }

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

    public function callRecursively($method, $arguments = array())
    {
        call_user_func_array(array($this, $method), $arguments);

        foreach ($this->children as $child) {
            $child->callRecursively($method, $arguments);
        }

        return $this;
    }

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
