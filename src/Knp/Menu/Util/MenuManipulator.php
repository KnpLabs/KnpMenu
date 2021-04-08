<?php

namespace Knp\Menu\Util;

use Knp\Menu\ItemInterface;

class MenuManipulator
{
    /**
     * Moves item to specified position. Rearrange siblings accordingly.
     *
     * @param int $position position to move child to
     */
    public function moveToPosition(ItemInterface $item, int $position): void
    {
        if (null !== $parent = $item->getParent()) {
            $this->moveChildToPosition($parent, $item, $position);
        }
    }

    /**
     * Moves child to specified position. Rearrange other children accordingly.
     *
     * @param ItemInterface $child    Child to move
     * @param int           $position Position to move child to
     */
    public function moveChildToPosition(ItemInterface $item, ItemInterface $child, int $position): void
    {
        $name = $child->getName();
        $order = \array_keys($item->getChildren());

        $oldPosition = \array_search($name, $order);
        unset($order[$oldPosition]);

        $order = \array_values($order);

        \array_splice($order, $position, 0, $name);
        $item->reorderChildren($order);
    }

    /**
     * Moves item to first position. Rearrange siblings accordingly.
     */
    public function moveToFirstPosition(ItemInterface $item): void
    {
        $this->moveToPosition($item, 0);
    }

    /**
     * Moves item to last position. Rearrange siblings accordingly.
     */
    public function moveToLastPosition(ItemInterface $item): void
    {
        if (null !== $parent = $item->getParent()) {
            $this->moveToPosition($item, $parent->count());
        }
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
     * @param mixed                    $offset name of child, child object, or numeric offset
     * @param string|int|ItemInterface $length name of child, child object, or numeric length
     */
    public function slice(ItemInterface $item, $offset, $length = null): ItemInterface
    {
        $names = \array_keys($item->getChildren());
        if ($offset instanceof ItemInterface) {
            $offset = $offset->getName();
        }
        if (!\is_int($offset)) {
            $offset = \array_search($offset, $names, true);
            if (false === $offset) {
                throw new \InvalidArgumentException('Not found.');
            }
        }

        if (null !== $length) {
            if ($length instanceof ItemInterface) {
                $length = $length->getName();
            }
            if (!\is_int($length)) {
                $index = \array_search($length, $names, true);
                $length = ($index < $offset) ? 0 : $index - $offset;
            }
        }

        $slicedItem = $item->copy();
        $children = \array_slice($slicedItem->getChildren(), $offset, $length);
        $slicedItem->setChildren($children);

        return $slicedItem;
    }

    /**
     * Split menu into two distinct menus.
     *
     * @param string|int|ItemInterface $length name of child, child object, or numeric length
     *
     * @phpstan-return array{primary: ItemInterface, secondary: ItemInterface}
     *
     * @return array Array with two menus, with "primary" and "secondary" key
     */
    public function split(ItemInterface $item, $length): array
    {
        return [
            'primary' => $this->slice($item, 0, $length),
            'secondary' => $this->slice($item, $length),
        ];
    }

    /**
     * Calls a method recursively on all of the children of this item
     *
     * @example
     * $menu->callRecursively('setShowChildren', [false]);
     *
     * @param array<int|string, mixed> $arguments
     */
    public function callRecursively(ItemInterface $item, string $method, array $arguments = []): void
    {
        $item->$method(...$arguments);

        foreach ($item->getChildren() as $child) {
            $this->callRecursively($child, $method, $arguments);
        }
    }

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     */
    public function getPathAsString(ItemInterface $item, string $separator = ' > '): string
    {
        $children = [];
        $obj = $item;

        do {
            $children[] = $obj->getLabel();
        } while ($obj = $obj->getParent());

        return \implode($separator, \array_reverse($children));
    }

    /**
     * @param int|null $depth the depth until which children should be exported (null means unlimited)
     *
     * @return array<string, mixed>
     */
    public function toArray(ItemInterface $item, ?int $depth = null): array
    {
        $array = [
            'name' => $item->getName(),
            'label' => $item->getLabel(),
            'uri' => $item->getUri(),
            'attributes' => $item->getAttributes(),
            'labelAttributes' => $item->getLabelAttributes(),
            'linkAttributes' => $item->getLinkAttributes(),
            'childrenAttributes' => $item->getChildrenAttributes(),
            'extras' => $item->getExtras(),
            'display' => $item->isDisplayed(),
            'displayChildren' => $item->getDisplayChildren(),
            'current' => $item->isCurrent(),
        ];

        // export the children as well, unless explicitly disabled
        if (0 !== $depth) {
            $childDepth = null === $depth ? null : $depth - 1;
            $array['children'] = [];
            foreach ($item->getChildren() as $key => $child) {
                $array['children'][$key] = $this->toArray($child, $childDepth);
            }
        }

        return $array;
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
     *   * ItemInterface object
     *   * ['subItem' => '@homepage']
     *   * ['subItem1', 'subItem2']
     *   * [['label' => 'subItem1', 'url' => '@homepage'], ['label' => 'subItem2']]
     *
     * @param string|ItemInterface|array<int|string, mixed>|\Traversable<mixed> $subItem A string or array to append onto the end of the array
     * @phpstan-param string|ItemInterface|array<int|string, string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface>|\Traversable<string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface> $subItem
     *
     * @return array<int, array<string, mixed>>
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     *
     * @throws \InvalidArgumentException if an element of the subItem is invalid
     */
    public function getBreadcrumbsArray(ItemInterface $item, $subItem = null): array
    {
        $breadcrumbs = $this->buildBreadcrumbsArray($item);

        if (null === $subItem) {
            return $breadcrumbs;
        }

        if ($subItem instanceof ItemInterface) {
            $breadcrumbs[] = $this->getBreadcrumbsItem($subItem);

            return $breadcrumbs;
        }

        if (!\is_array($subItem) && !$subItem instanceof \Traversable) {
            $subItem = [$subItem];
        }

        foreach ($subItem as $key => $value) {
            switch (true) {
                case $value instanceof ItemInterface:
                    $value = $this->getBreadcrumbsItem($value);
                    break;

                case \is_array($value):
                    // Assume we already have the appropriate array format for the element
                    break;

                case \is_int($key) && \is_string($value):
                    $value = [
                        'label' => (string) $value,
                        'uri' => null,
                        'item' => null,
                    ];
                    break;

                case \is_scalar($value):
                    $value = [
                        'label' => (string) $key,
                        'uri' => (string) $value,
                        'item' => null,
                    ];
                    break;

                case null === $value:
                    $value = [
                        'label' => (string) $key,
                        'uri' => null,
                        'item' => null,
                    ];
                    break;

                default:
                    throw new \InvalidArgumentException(\sprintf('Invalid value supplied for the key "%s". It should be an item, an array or a scalar', $key));
            }

            $breadcrumbs[] = $value;
        }

        return $breadcrumbs;
    }

    /**
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     */
    private function buildBreadcrumbsArray(ItemInterface $item): array
    {
        $breadcrumb = [];

        do {
            $breadcrumb[] = $this->getBreadcrumbsItem($item);
        } while ($item = $item->getParent());

        return \array_reverse($breadcrumb);
    }

    /**
     * @phpstan-return array{label: string, uri: string|null, item: ItemInterface}
     */
    private function getBreadcrumbsItem(ItemInterface $item): array
    {
        return [
            'label' => $item->getLabel(),
            'uri' => $item->getUri(),
            'item' => $item,
        ];
    }
}
