<?php

namespace Knp\Menu\Provider;

class ChainProvider implements MenuProviderInterface
{
    /**
     * @var MenuProviderInterface[]
     */
    private $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function get($name)
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($name)) {
                return $provider->get($name);
            }
        }

        throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
    }

    public function has($name)
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($name)) {
                return true;
            }
        }

        return false;
    }
}
