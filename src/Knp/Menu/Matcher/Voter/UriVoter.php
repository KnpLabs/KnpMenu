<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;

/**
 * Voter based on the uri
 */
class UriVoter implements VoterInterface
{
    private $uri;

    public function __construct($uri = null)
    {
        $this->uri = $uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
        trigger_error(sprintf('This method is deprecated. You really need this setter ? Please tell us in what conditions by creating an issue at http://github.com/KnpLabs/KnpMenu.'), E_USER_DEPRECATED);
    }

    public function matchItem(ItemInterface $item)
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
