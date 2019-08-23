<?php

namespace Knp\Menu;

use Knp\Menu\Factory\CoreExtension;
use Knp\Menu\Factory\ExtensionInterface;

/**
 * Factory to create a menu from a tree
 */
class MenuFactory implements FactoryInterface
{
    /**
     * @var array[]
     */
    private $extensions = [];

    /**
     * @var ExtensionInterface[]|null
     */
    private $sorted;

    public function __construct()
    {
        $this->addExtension(new CoreExtension(), -10);
    }

    public function createItem(string $name, array $options = []): ItemInterface
    {
        foreach ($this->getExtensions() as $extension) {
            $options = $extension->buildOptions($options);
        }

        $item = new MenuItem($name, $this);

        foreach ($this->getExtensions() as $extension) {
            $extension->buildItem($item, $options);
        }

        return $item;
    }

    /**
     * Adds a factory extension
     *
     * @param ExtensionInterface $extension
     * @param int                $priority
     */
    public function addExtension(ExtensionInterface $extension, int $priority = 0): void
    {
        $this->extensions[$priority][] = $extension;
        $this->sorted = null;
    }

    /**
     * Sorts the internal list of extensions by priority.
     *
     * @return ExtensionInterface[]|null
     */
    private function getExtensions(): ?array
    {
        if (null === $this->sorted) {
            \krsort($this->extensions);
            $this->sorted = !empty($this->extensions) ? \call_user_func_array('array_merge', $this->extensions) : [];
        }

        return $this->sorted;
    }
}
