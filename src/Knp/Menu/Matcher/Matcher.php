<?php

namespace Knp\Menu\Matcher;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;

/**
 * A MatcherInterface implementation using a voter system
 */
class Matcher implements MatcherInterface
{
    /**
     * @var \SplObjectStorage<ItemInterface, bool>
     */
    private $cache;

    /**
     * @var iterable|VoterInterface[]
     */
    private $voters;

    /**
     * @param VoterInterface[]|iterable $voters
     */
    public function __construct($voters = [])
    {
        $this->voters = $voters;
        $this->cache = new \SplObjectStorage();
    }

    public function isCurrent(ItemInterface $item): bool
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

    public function isAncestor(ItemInterface $item, ?int $depth = null): bool
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

    public function clear(): void
    {
        $this->cache = new \SplObjectStorage();
    }
}
