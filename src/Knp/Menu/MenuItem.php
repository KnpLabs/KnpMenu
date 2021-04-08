<?php

namespace Knp\Menu;

/**
 * Default implementation of the ItemInterface
 */
class MenuItem implements ItemInterface
{
    /**
     * Name of this menu item (used for id by parent menu)
     *
     * @var string
     */
    protected $name;

    /**
     * Label to output, name is used by default
     *
     * @var string|null
     */
    protected $label;

    /**
     * Attributes for the item link
     *
     * @var array<string, string|bool|null>
     */
    protected $linkAttributes = [];

    /**
     * Attributes for the children list
     *
     * @var array<string, string|bool|null>
     */
    protected $childrenAttributes = [];

    /**
     * Attributes for the item text
     *
     * @var array<string, string|bool|null>
     */
    protected $labelAttributes = [];

    /**
     * Uri to use in the anchor tag
     *
     * @var string|null
     */
    protected $uri;

    /**
     * Attributes for the item
     *
     * @var array<string, string|bool|null>
     */
    protected $attributes = [];

    /**
     * Extra stuff associated to the item
     *
     * @var array<string, mixed>
     */
    protected $extras = [];

    /**
     * Whether the item is displayed
     *
     * @var bool
     */
    protected $display = true;

    /**
     * Whether the children of the item are displayed
     *
     * @var bool
     */
    protected $displayChildren = true;

    /**
     * Child items
     *
     * @var array<string, ItemInterface>
     */
    protected $children = [];

    /**
     * Parent item
     *
     * @var ItemInterface|null
     */
    protected $parent;

    /**
     * whether the item is current. null means unknown
     *
     * @var bool|null
     */
    protected $isCurrent;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Class constructor
     *
     * @param string $name The name of this menu, which is how its parent will
     *                     reference it. Also used as label if label not specified
     */
    public function __construct(string $name, FactoryInterface $factory)
    {
        $this->name = $name;
        $this->factory = $factory;
    }

    public function setFactory(FactoryInterface $factory): ItemInterface
    {
        $this->factory = $factory;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ItemInterface
    {
        if ($this->name === $name) {
            return $this;
        }

        $parent = $this->getParent();
        if (null !== $parent && isset($parent[$name])) {
            throw new \InvalidArgumentException('Cannot rename item, name is already used by sibling.');
        }

        $oldName = $this->name;
        $this->name = $name;

        if (null !== $parent) {
            $names = \array_keys($parent->getChildren());
            $items = \array_values($parent->getChildren());

            $offset = \array_search($oldName, $names);
            $names[$offset] = $name;

            if (false === $children = \array_combine($names, $items)) {
                throw new \InvalidArgumentException('Number of elements is not matching.');
            }

            $parent->setChildren($children);
        }

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): ItemInterface
    {
        $this->uri = $uri;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->name;
    }

    public function setLabel(?string $label): ItemInterface
    {
        $this->label = $label;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): ItemInterface
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    public function setAttribute(string $name, $value): ItemInterface
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getLinkAttributes(): array
    {
        return $this->linkAttributes;
    }

    public function setLinkAttributes(array $linkAttributes): ItemInterface
    {
        $this->linkAttributes = $linkAttributes;

        return $this;
    }

    public function getLinkAttribute(string $name, $default = null)
    {
        return $this->linkAttributes[$name] ?? $default;
    }

    public function setLinkAttribute(string $name, $value): ItemInterface
    {
        $this->linkAttributes[$name] = $value;

        return $this;
    }

    public function getChildrenAttributes(): array
    {
        return $this->childrenAttributes;
    }

    public function setChildrenAttributes(array $childrenAttributes): ItemInterface
    {
        $this->childrenAttributes = $childrenAttributes;

        return $this;
    }

    public function getChildrenAttribute(string $name, $default = null)
    {
        return $this->childrenAttributes[$name] ?? $default;
    }

    public function setChildrenAttribute(string $name, $value): ItemInterface
    {
        $this->childrenAttributes[$name] = $value;

        return $this;
    }

    public function getLabelAttributes(): array
    {
        return $this->labelAttributes;
    }

    public function setLabelAttributes(array $labelAttributes): ItemInterface
    {
        $this->labelAttributes = $labelAttributes;

        return $this;
    }

    public function getLabelAttribute(string $name, $default = null)
    {
        return $this->labelAttributes[$name] ?? $default;
    }

    public function setLabelAttribute(string $name, $value): ItemInterface
    {
        $this->labelAttributes[$name] = $value;

        return $this;
    }

    public function getExtras(): array
    {
        return $this->extras;
    }

    public function setExtras(array $extras): ItemInterface
    {
        $this->extras = $extras;

        return $this;
    }

    public function getExtra(string $name, $default = null)
    {
        return $this->extras[$name] ?? $default;
    }

    public function setExtra(string $name, $value): ItemInterface
    {
        $this->extras[$name] = $value;

        return $this;
    }

    public function getDisplayChildren(): bool
    {
        return $this->displayChildren;
    }

    public function setDisplayChildren(bool $bool): ItemInterface
    {
        $this->displayChildren = $bool;

        return $this;
    }

    public function isDisplayed(): bool
    {
        return $this->display;
    }

    public function setDisplay(bool $bool): ItemInterface
    {
        $this->display = $bool;

        return $this;
    }

    public function addChild($child, array $options = []): ItemInterface
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

    public function getChild(string $name): ?ItemInterface
    {
        return $this->children[$name] ?? null;
    }

    public function reorderChildren(array $order): ItemInterface
    {
        if (\count($order) !== $this->count()) {
            throw new \InvalidArgumentException('Cannot reorder children, order does not contain all children.');
        }

        $newChildren = [];

        foreach ($order as $name) {
            if (!isset($this->children[$name])) {
                throw new \InvalidArgumentException('Cannot find children named '.$name);
            }

            $child = $this->children[$name];
            $newChildren[$name] = $child;
        }

        $this->setChildren($newChildren);

        return $this;
    }

    public function copy(): ItemInterface
    {
        $newMenu = clone $this;
        $newMenu->setChildren([]);
        $newMenu->setParent();
        foreach ($this->getChildren() as $child) {
            $newMenu->addChild($child->copy());
        }

        return $newMenu;
    }

    public function getLevel(): int
    {
        return $this->parent ? $this->parent->getLevel() + 1 : 0;
    }

    public function getRoot(): ItemInterface
    {
        $obj = $this;
        do {
            $found = $obj;
        } while ($obj = $obj->getParent());

        return $found;
    }

    public function isRoot(): bool
    {
        return null === $this->parent;
    }

    public function getParent(): ?ItemInterface
    {
        return $this->parent;
    }

    public function setParent(?ItemInterface $parent = null): ItemInterface
    {
        if ($parent === $this) {
            throw new \InvalidArgumentException('Item cannot be a child of itself');
        }

        $this->parent = $parent;

        return $this;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): ItemInterface
    {
        $this->children = $children;

        return $this;
    }

    public function removeChild($name): ItemInterface
    {
        $name = $name instanceof ItemInterface ? $name->getName() : $name;

        if (isset($this->children[$name])) {
            // unset the child and reset it so it looks independent
            $this->children[$name]->setParent(null);
            unset($this->children[$name]);
        }

        return $this;
    }

    public function getFirstChild(): ItemInterface
    {
        if (empty($this->children)) {
            throw new \LogicException('Cannot get first child: there are no children.');
        }

        return \reset($this->children);
    }

    public function getLastChild(): ItemInterface
    {
        if (empty($this->children)) {
            throw new \LogicException('Cannot get last child: there are no children.');
        }

        return \end($this->children);
    }

    public function hasChildren(): bool
    {
        foreach ($this->children as $child) {
            if ($child->isDisplayed()) {
                return true;
            }
        }

        return false;
    }

    public function setCurrent(?bool $bool): ItemInterface
    {
        $this->isCurrent = $bool;

        return $this;
    }

    public function isCurrent(): ?bool
    {
        return $this->isCurrent;
    }

    public function isLast(): bool
    {
        // if this is root, then return false
        if (null === $this->parent) {
            return false;
        }

        return $this->parent->getLastChild() === $this;
    }

    public function isFirst(): bool
    {
        // if this is root, then return false
        if (null === $this->parent) {
            return false;
        }

        return $this->parent->getFirstChild() === $this;
    }

    public function actsLikeFirst(): bool
    {
        // root items are never "marked" as first
        if (null === $this->parent) {
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

        $children = $this->parent->getChildren();
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->isDisplayed()) {
                return $child->getName() === $this->getName();
            }
        }

        return false;
    }

    public function actsLikeLast(): bool
    {
        // root items are never "marked" as last
        if (null === $this->parent) {
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

        $children = \array_reverse($this->parent->getChildren());
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
     */
    public function count(): int
    {
        return \count($this->children);
    }

    /**
     * Implements IteratorAggregate
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * Implements ArrayAccess
     *
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->children[$offset]);
    }

    /**
     * Implements ArrayAccess
     *
     * @param string $offset
     *
     * @return ItemInterface|null
     */
    public function offsetGet($offset)
    {
        return $this->getChild($offset);
    }

    /**
     * Implements ArrayAccess
     *
     * @param string      $offset
     * @param string|null $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->addChild($offset)->setLabel($value);
    }

    /**
     * Implements ArrayAccess
     *
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
        $this->removeChild($offset);
    }
}
