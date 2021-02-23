<?php

namespace Knp\Menu\Loader;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;

class NodeLoader implements LoaderInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function load($data): ItemInterface
    {
        if (!$data instanceof NodeInterface) {
            throw new \InvalidArgumentException(\sprintf('Unsupported data. Expected Knp\Menu\NodeInterface but got %s', \is_object($data) ? \get_class($data) : \gettype($data)));
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
