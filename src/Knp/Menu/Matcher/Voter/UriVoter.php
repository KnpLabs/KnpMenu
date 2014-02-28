<?php

namespace Knp\Menu\Matcher\Voter;

use Knp\Menu\ItemInterface;

/**
 * Voter based on the uri
 */
class UriVoter implements VoterInterface
{
    /**
     * @var string Uri the voter should match on
     */
    private $uri;

    /**
     * @var bool Setting to know if we should match uri that are prefix to the uri we test
     */
    private $matchPrefix;

    /**
     * @param null $uri
     * @param bool $matchPrefix
     */
    public function __construct($uri = null, $matchPrefix=false)
    {
        $this->uri = $uri;
        $this->matchPrefix = $matchPrefix;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param bool $matchPrefix
     */
    public function setMatchPrefix($matchPrefix)
    {
        $this->matchPrefix = $matchPrefix;
    }

    /**
     * @param ItemInterface $item
     * @return true|null
     */
    public function matchItem(ItemInterface $item)
    {
        if (null === $this->uri || null === $item->getUri()) {
            return null;
        }

        if ($item->getUri() === $this->uri) {
            return true;
        }

        if ($item->getUri() === '/') {
            return null;
        }

        if ($this->matchPrefix && strpos($this->uri, $item->getUri()) === 0) {
            return true;
        }

        return null;
    }
}
