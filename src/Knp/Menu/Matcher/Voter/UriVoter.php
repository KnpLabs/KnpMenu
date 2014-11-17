<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;

/**
 * Voter based on the uri
 *
 * @package Knp\Menu\Matcher\Voter
 */
class UriVoter implements VoterInterface
{
    /**
     * @var null
     */
    private $uri;

    /**
     * @param null $uri
     */
    public function __construct($uri = null)
    {
        $this->uri = $uri;
    }

    /**
     * @param ItemInterface $item
     *
     * @return bool|null
     */
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
