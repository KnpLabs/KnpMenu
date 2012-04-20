<?php

namespace Knp\Menu\Matcher;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;

/**
 * Interface implemented by the item matcher
 */
class Matcher implements MatcherInterface
{
    private $cache;

    /**
     * @var \Knp\Menu\Matcher\Voter\VoterInterface[]
     */
    private $voters = array();

    public function __construct()
    {
        $this->cache = new \SplObjectStorage();
    }

    public function addVoter(VoterInterface $voter)
    {
        $this->voters[] = $voter;
    }

    /**
     * Checks whether an item is current.
     *
     * @param \Knp\Menu\ItemInterface $item
     * @return boolean
     */
    public function isCurrent(ItemInterface $item)
    {
        $current = $item->isCurrent();
        if (null !== $current) {
            return $current;
        }

        if ($this->cache->contains($item)) {
            return $this->cache[$item];
        }

        foreach ($this->voters as $voter) {
            $current = $voter->matchItem($item);
            if (null !== $current) {
                break;
            }
        }

        $current = (boolean) $current;
        $this->cache[$item] = $current;

        return $current;
    }

    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param \Knp\Menu\ItemInterface $item
     * @param integer $depth The max depth to look for the item
     * @return boolean
     */
    public function isAncestor(ItemInterface $item, $depth = null)
    {
        if (0 === $depth) {
            return false;
        }

        $childDepth = null === $depth ? null : $depth - 1;
        foreach ($item->getChildren() as $child) {
            if ($this->isCurrent($child) || $this->isAncestor($child, $childDepth)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clears the state of the matcher.
     */
    public function clear()
    {
        $this->cache = new \SplObjectStorage();
    }
}
