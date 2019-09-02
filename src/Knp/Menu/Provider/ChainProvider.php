<?php

namespace Knp\Menu\Provider;

class ChainProvider implements MenuProviderInterface
{
    private $providers;

    /**
     * @param MenuProviderInterface[]|iterable $providers
     */
    public function __construct($providers)
    {
        $this->providers = $providers;
    }

    public function get($name, array $options = [])
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($name, $options)) {
                return $provider->get($name, $options);
            }
        }

        throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
    }

    public function has($name, array $options = [])
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($name, $options)) {
                return true;
            }
        }

        return false;
    }
}
