<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;

/**
 * Voter based on the uri
 */
class UriVoter implements VoterInterface
{
    private $uri;

    public function __construct(?string $uri = null)
    {
        $this->uri = $uri;
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
