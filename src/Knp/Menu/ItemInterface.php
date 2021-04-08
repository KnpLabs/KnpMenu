<?php

namespace Knp\Menu;

/**
 * Interface implemented by a menu item.
 *
 * It roughly represents a single <li> tag and is what you should interact with
 * most of the time by default.
 * Originally taken from ioMenuPlugin (http://github.com/weaverryan/ioMenuPlugin)
 *
 * @extends \ArrayAccess<string, self|string>
 * @extends \IteratorAggregate<string, self>
 */
interface ItemInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    public function setFactory(FactoryInterface $factory): self;

    public function getName(): string;

    /**
     * Renames the item.
     *
     * This method must also update the key in the parent.
     *
     * Provides a fluent interface
     *
     * @throws \InvalidArgumentException if the name is already used by a sibling
     */
    public function setName(string $name): self;

    /**
     * Get the uri for a menu item
     */
    public function getUri(): ?string;

    /**
     * Set the uri for a menu item
     *
     * Provides a fluent interface
     *
     * @param string|null $uri The uri to set on this menu item
     */
    public function setUri(?string $uri): self;

    /**
     * Returns the label that will be used to render this menu item
     *
     * Defaults to the name of no label was specified
     */
    public function getLabel(): string;

    /**
     * Provides a fluent interface
     *
     * @param string|null $label The text to use when rendering this menu item
     */
    public function setLabel(?string $label): self;

    /**
     * @return array<string, string|bool|null>
     */
    public function getAttributes(): array;

    /**
     * @param array<string, string|bool|null> $attributes
     */
    public function setAttributes(array $attributes): self;

    /**
     * @param string           $name    The name of the attribute to return
     * @param string|bool|null $default The value to return if the attribute doesn't exist
     *
     * @return string|bool|null
     */
    public function getAttribute(string $name, $default = null);

    /**
     * @param string|bool|null $value
     */
    public function setAttribute(string $name, $value): self;

    /**
     * @return array<string, string|bool|null>
     */
    public function getLinkAttributes(): array;

    /**
     * @param array<string, string|bool|null> $linkAttributes
     */
    public function setLinkAttributes(array $linkAttributes): self;

    /**
     * @param string           $name    The name of the attribute to return
     * @param string|bool|null $default The value to return if the attribute doesn't exist
     *
     * @return string|bool|null
     */
    public function getLinkAttribute(string $name, $default = null);

    /**
     * @param string|bool|null $value
     */
    public function setLinkAttribute(string $name, $value): self;

    /**
     * @return array<string, string|bool|null>
     */
    public function getChildrenAttributes(): array;

    /**
     * @param array<string, string|bool|null> $childrenAttributes
     */
    public function setChildrenAttributes(array $childrenAttributes): self;

    /**
     * @param string           $name    The name of the attribute to return
     * @param string|bool|null $default The value to return if the attribute doesn't exist
     *
     * @return string|bool|null
     */
    public function getChildrenAttribute(string $name, $default = null);

    /**
     * @param string|bool|null $value
     */
    public function setChildrenAttribute(string $name, $value): self;

    /**
     * @return array<string, string|bool|null>
     */
    public function getLabelAttributes(): array;

    /**
     * @param array<string, string|bool|null> $labelAttributes
     */
    public function setLabelAttributes(array $labelAttributes): self;

    /**
     * @param string           $name    The name of the attribute to return
     * @param string|bool|null $default The value to return if the attribute doesn't exist
     *
     * @return string|bool|null
     */
    public function getLabelAttribute(string $name, $default = null);

    /**
     * @param string|bool|null $value
     */
    public function setLabelAttribute(string $name, $value): self;

    /**
     * @return array<string, mixed>
     */
    public function getExtras(): array;

    /**
     * @param array<string, mixed> $extras
     */
    public function setExtras(array $extras): self;

    /**
     * @param string $name    The name of the extra to return
     * @param mixed  $default The value to return if the extra doesn't exist
     *
     * @return mixed
     */
    public function getExtra(string $name, $default = null);

    /**
     * @param mixed $value
     */
    public function setExtra(string $name, $value): self;

    public function getDisplayChildren(): bool;

    /**
     * Set whether or not this menu item should show its children
     *
     * Provides a fluent interface
     */
    public function setDisplayChildren(bool $bool): self;

    /**
     * Whether or not to display this menu item
     */
    public function isDisplayed(): bool;

    /**
     * Set whether or not this menu should be displayed
     *
     * Provides a fluent interface
     */
    public function setDisplay(bool $bool): self;

    /**
     * Add a child menu item to this menu
     *
     * Returns the child item
     *
     * @param ItemInterface|string $child   An ItemInterface instance or the name of a new item to create
     * @param array<string, mixed> $options If creating a new item, the options passed to the factory for the item
     *
     * @throws \InvalidArgumentException if the item is already in a tree
     */
    public function addChild($child, array $options = []): self;

    /**
     * Returns the child menu identified by the given name
     *
     * @param string $name Then name of the child menu to return
     */
    public function getChild(string $name): ?self;

    /**
     * Reorder children.
     *
     * Provides a fluent interface
     *
     * @param array<int|string, string> $order new order of children
     */
    public function reorderChildren(array $order): self;

    /**
     * Makes a deep copy of menu tree. Every item is copied as another object.
     */
    public function copy(): self;

    /**
     * Returns the level of this menu item
     *
     * The root menu item is 0, followed by 1, 2, etc
     */
    public function getLevel(): int;

    /**
     * Returns the root ItemInterface of this menu tree
     */
    public function getRoot(): self;

    /**
     * Returns whether or not this menu item is the root menu item
     */
    public function isRoot(): bool;

    public function getParent(): ?self;

    /**
     * Used internally when adding and removing children
     *
     * Provides a fluent interface
     */
    public function setParent(?self $parent = null): self;

    /**
     * Return the children as an array of ItemInterface objects
     *
     * @return array<string, self>
     */
    public function getChildren(): array;

    /**
     * Provides a fluent interface
     *
     * @param array<string, self> $children An array of ItemInterface objects
     */
    public function setChildren(array $children): self;

    /**
     * Removes a child from this menu item
     *
     * Provides a fluent interface
     *
     * @param ItemInterface|string $name The name of ItemInterface instance or the ItemInterface to remove
     */
    public function removeChild($name): self;

    public function getFirstChild(): self;

    public function getLastChild(): self;

    /**
     * Returns whether or not this menu items has viewable children
     *
     * This menu MAY have children, but this will return false if the current
     * user does not have access to view any of those items
     */
    public function hasChildren(): bool;

    /**
     * Sets whether or not this menu item is "current".
     *
     * If the state is unknown, use null.
     *
     * Provides a fluent interface
     *
     * @param bool|null $bool Specify that this menu item is current
     */
    public function setCurrent(?bool $bool): self;

    /**
     * Gets whether or not this menu item is "current".
     */
    public function isCurrent(): ?bool;

    /**
     * Whether this menu item is last in its parent
     */
    public function isLast(): bool;

    /**
     * Whether this menu item is first in its parent
     */
    public function isFirst(): bool;

    /**
     * Whereas isFirst() returns if this is the first child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the first child that would be rendered
     * for the current user
     */
    public function actsLikeFirst(): bool;

    /**
     * Whereas isLast() returns if this is the last child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the last child that would be rendered
     * for the current user
     */
    public function actsLikeLast(): bool;
}
