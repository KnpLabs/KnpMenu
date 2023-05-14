<?php

namespace Knp\Menu\Loader;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;

class NodeLoader implements LoaderInterface
{
    public function __construct(private FactoryInterface $factory)
    {
    }

    public function load($data): ItemInterface
    {
        if (!$data instanceof NodeInterface) {
            throw new \InvalidArgumentException(\sprintf('Unsupported data. Expected Knp\Menu\NodeInterface but got %s', \get_debug_type($data)));
        }

        $item = $this->factory->createItem($data->getName(), $data->getOptions());

        foreach ($data->getChildren() as $childNode) {
            $item->addChild($this->load($childNode));
        }

        return $item;
    }

    public function supports($data): bool
    {
        return $data instanceof NodeInterface;
    }
}
