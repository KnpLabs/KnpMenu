<?php

namespace Knp\Menu\Loader;

use Knp\Menu\FactoryInterface;
use Knp\Menu\NodeInterface;

/**
 * Class NodeLoader
 *
 * @package Knp\Menu\Loader
 */
class NodeLoader implements LoaderInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param mixed $data
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function load($data)
    {
        if (!$data instanceof NodeInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported data. Expected Knp\Menu\NodeInterface but got ',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        $item = $this->factory->createItem($data->getName(), $data->getOptions());

        foreach ($data->getChildren() as $childNode) {
            $item->addChild($this->load($childNode));
        }

        return $item;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data)
    {
        return $data instanceof NodeInterface;
    }
}
