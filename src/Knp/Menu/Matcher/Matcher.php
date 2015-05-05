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

    /**
     * @var array[]
     */
    private $voters = [];

    /**
     * @var VoterInterface[]
     */
    private $sortedVoters = [];

    public function __construct()
    {
        $this->cache = new \SplObjectStorage();
    }

    /**
     * Adds a voter in the matcher.
     *
     * @param VoterInterface $voter
     * @param integer        $priority
     */
    public function addVoter(VoterInterface $voter, $priority = 0)
    {
        $this->voters[$priority][] = $voter;
        //reset sorted voters
        $this->sortedVoters = null;
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

        foreach ($this->getVoters() as $voter) {
            $current = $voter->matchItem($item);
            if (null !== $current) {
                break;
            }
        }

        $current = (boolean) $current;
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

    /**
     * Sorts the internal list of voters by priority.
     *
     * @return VoterInterface[]
     */
    private function getVoters()
    {
        if (null === $this->sortedVoters) {
            krsort($this->voters);
            $this->sortedVoters = ! empty($this->voters) ? call_user_func_array('array_merge', $this->voters) : [];
        }

        return $this->sortedVoters;
    }
}
