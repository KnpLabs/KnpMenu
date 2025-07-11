<?php

namespace Knp\Menu\Loader;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

/**
 * Loader importing a menu tree from an array.
 *
 * The array should match the output of MenuManipulator::toArray
 *
 * @final since 3.8.0
 */
class ArrayLoader implements LoaderInterface
{
    public function __construct(private FactoryInterface $factory)
    {
    }

    public function load($data): ItemInterface
    {
        if (!$this->supports($data)) {
            throw new \InvalidArgumentException(\sprintf('Unsupported data. Expected an array but got %s', \get_debug_type($data)));
        }

        return $this->fromArray($data);
    }

    public function supports($data): bool
    {
        return \is_array($data);
    }

    /**
     * @param array<string, mixed> $data
     * @param string|null          $name (the name of the item, used only if there is no name in the data themselves)
     */
    private function fromArray(array $data, ?string $name = null): ItemInterface
    {
        $name = $data['name'] ?? $name;

        if (isset($data['children'])) {
            $children = $data['children'];
            unset($data['children']);
        } else {
            $children = [];
        }

        $item = $this->factory->createItem($name, $data);

        foreach ($children as $childName => $child) {
            $item->addChild($this->fromArray($child, $childName));
        }

        return $item;
    }
}
