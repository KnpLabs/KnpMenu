<?php

namespace Knp\Menu\Matcher;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;

/**
 * A MatcherInterface implementation using a voter system
 */
class Matcher implements MatcherInterface
{
    private $cache;
    private $voters;

    /**
     * @param VoterInterface[]|iterable $voters
     */
    public function __construct($voters = [])
    {
        $this->voters = $voters;
        $this->cache = new \SplObjectStorage();
    }

    /**
     * Adds a voter in the matcher.
     *
     * If an iterator was used to provide voters in the constructor, it will be
     * converted to array when using this method, breaking any potential lazy-loading.
     *
     * @deprecated since 2.3. Pass voters in the constructor instead.
     *
     * @param VoterInterface $voter
     */
    public function addVoter(VoterInterface $voter)
    {
        @trigger_error(\sprintf('The %s() method is deprecated since version 2.3 and will be removed in 3.0. Pass voters in the constructor instead.', __METHOD__), E_USER_DEPRECATED);

        if ($this->voters instanceof \Traversable) {
            $this->voters = \iterator_to_array($this->voters);
        }

        $this->voters[] = $voter;
    }

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

        $current = (bool) $current;
        $this->cache[$item] = $current;

        return $current;
    }

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

    public function clear()
    {
        $this->cache = new \SplObjectStorage();
    }
}
