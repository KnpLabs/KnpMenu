<?php

namespace Knp\Menu\Matcher;

use Knp\Menu\ItemInterface;

/**
 * Interface implemented by the item matcher
 */
interface MatcherInterface
{
    /**
     * Checks whether an item is current.
     *
     * @param ItemInterface $item
     *
     * @return bool
     */
    public function isCurrent(ItemInterface $item): bool;

    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param ItemInterface $item
     * @param int|null      $depth The max depth to look for the item
     *
     * @return bool
     */
    public function isAncestor(ItemInterface $item, ?int $depth = null): bool;

    /**
     * Clears the state of the matcher.
     */
    public function clear(): void;
}
