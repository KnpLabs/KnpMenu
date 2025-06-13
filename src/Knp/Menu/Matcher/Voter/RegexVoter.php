<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;

/**
 * Implements the VoterInterface which can be used as voter for "current" class
 * `matchItem` will return true if the pattern you're searching for is found in the URI of the item
 *
 * @final since 3.8.0
 */
class RegexVoter implements VoterInterface
{
    public function __construct(private ?string $regexp)
    {
    }

    public function matchItem(ItemInterface $item): ?bool
    {
        if (null === $this->regexp || null === $item->getUri()) {
            return null;
        }

        if (\preg_match($this->regexp, $item->getUri())) {
            return true;
        }

        return null;
    }
}
