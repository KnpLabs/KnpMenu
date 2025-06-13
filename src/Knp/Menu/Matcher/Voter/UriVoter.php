<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;

/**
 * Voter based on the uri
 *
 * @final since 3.8.0
 */
class UriVoter implements VoterInterface
{
    public function __construct(private ?string $uri = null)
    {
    }

    public function matchItem(ItemInterface $item): ?bool
    {
        if (null === $this->uri || null === $item->getUri()) {
            return null;
        }

        if ($item->getUri() === $this->uri) {
            return true;
        }

        return null;
    }
}
