<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;

/**
 * Voter based on a callback
 */
final class CallbackVoter implements VoterInterface
{
    public function matchItem(ItemInterface $item): ?bool
    {
        $callback = $item->getExtra('match_callback');

        if (null === $callback) {
            return null;
        }

        if (!\is_callable($callback)) {
            throw new \InvalidArgumentException('Extra "match_callback" must be callable.');
        }

        return $callback();
    }
}
