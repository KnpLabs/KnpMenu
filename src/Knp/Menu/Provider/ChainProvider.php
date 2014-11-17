<?php

namespace Knp\Menu\Provider;

/**
 * Class ChainProvider
 *
 * @package Knp\Menu\Provider
 */
class ChainProvider implements MenuProviderInterface
{
    /**
     * @var MenuProviderInterface[]
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \InvalidArgumentException
     */
    public function get($name, array $options = array())
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($name, $options)) {
                return $provider->get($name, $options);
            }
        }

        throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return bool
     */
    public function has($name, array $options = array())
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($name, $options)) {
                return true;
            }
        }

        return false;
    }
}
