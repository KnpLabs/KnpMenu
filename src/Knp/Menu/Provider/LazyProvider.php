<?php

namespace Knp\Menu\Provider;

use Knp\Menu\ItemInterface;

/**
 * A menu provider building menus lazily thanks to builder callables.
 *
 * Builders can either be callables or a factory for an object callable
 * represented as [Closure, method], where the Closure gets called
 * to instantiate the object.
 */
class LazyProvider implements MenuProviderInterface
{
    /**
     * @var array<string, mixed>
     */
    private $builders;

    /**
     * @phpstan-param array<string, callable|array{\Closure, string}> $builders
     */
    public function __construct(array $builders)
    {
        $this->builders = $builders;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        if (!isset($this->builders[$name])) {
            throw new \InvalidArgumentException(\sprintf('The menu "%s" is not defined.', $name));
        }

        $builder = $this->builders[$name];

        if (\is_array($builder) && isset($builder[0]) && $builder[0] instanceof \Closure) {
            $builder[0] = $builder[0]();
        }

        if (!\is_callable($builder)) {
            throw new \LogicException(\sprintf('Invalid menu builder for "%s". A callable or a factory for an object callable are expected.', $name));
        }

        return $builder($options);
    }

    public function has(string $name, array $options = []): bool
    {
        return isset($this->builders[$name]);
    }
}
