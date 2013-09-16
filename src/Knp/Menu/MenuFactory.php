<?php

namespace Knp\Menu;

use Knp\Menu\Factory\CoreExtension;
use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\Loader\ArrayLoader;
use Knp\Menu\Loader\NodeLoader;

/**
 * Factory to create a menu from a tree
 */
class MenuFactory implements FactoryInterface
{
    /**
     * @var array[]
     */
    private $extensions = array();

    /**
     * @var ExtensionInterface[]
     */
    private $sorted;

    public function __construct()
    {
        $this->addExtension(new CoreExtension(), -10);
    }

    public function createItem($name, array $options = array())
    {
        // TODO remove this BC layer before releasing 2.0
        $processedOptions = $this->buildOptions($options);
        if ($processedOptions !== $options) {
            trigger_error(sprintf('Overwriting Knp\Menu\MenuFactory::buildOptions is deprecated. Use a factory extension instead of %s.', get_class($this)), E_USER_DEPRECATED);

            $options = $processedOptions;
        }

        foreach ($this->getExtensions() as $extension) {
            $options = $extension->buildOptions($options);
        }

        $item = new MenuItem($name, $this);

        foreach ($this->getExtensions() as $extension) {
            $extension->buildItem($item, $options);
        }

        // TODO remove this BC layer before releasing 2.0
        if (method_exists($this, 'configureItem')) {
            trigger_error(sprintf('Overwriting Knp\Menu\MenuFactory::configureItem is deprecated. Use a factory extension instead of %s.', get_class($this)), E_USER_DEPRECATED);

            $this->configureItem($item, $options);
        }

        return $item;
    }

    /**
     * Adds a factory extension
     *
     * @param ExtensionInterface $extension
     * @param integer            $priority
     */
    public function addExtension(ExtensionInterface $extension, $priority = 0)
    {
        $this->extensions[$priority][] = $extension;
        $this->sorted = null;
    }

    /**
     * Builds the full option array used to configure the item.
     *
     * @deprecated Use a Knp\Menu\Factory\ExtensionInterface instead
     *
     * @param array $options
     *
     * @return array
     */
    protected function buildOptions(array $options)
    {
        return $options;
    }

    /**
     * Create a menu item from a NodeInterface
     *
     * @deprecated Use \Knp\Menu\Loader\NodeLoader
     *
     * @param NodeInterface $node
     *
     * @return ItemInterface
     */
    public function createFromNode(NodeInterface $node)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Loader\NodeLoader instead', E_USER_DEPRECATED);

        $loader = new NodeLoader($this);

        return $loader->load($node);
    }

    /**
     * Creates a new menu item (and tree if $data['children'] is set).
     *
     * The source is an array of data that should match the output from MenuManipulator->toArray().
     *
     * @deprecated Use \Knp\Menu\Loader\ArrayLoader
     *
     * @param array $data The array of data to use as a source for the menu tree
     *
     * @return ItemInterface
     */
    public function createFromArray(array $data)
    {
        trigger_error(__METHOD__ . ' is deprecated. Use Knp\Menu\Loader\ArrayLoader instead', E_USER_DEPRECATED);

        $loader = new ArrayLoader($this);

        return $loader->load($data);
    }

    /**
     * Sorts the internal list of extensions by priority.
     *
     * @return ExtensionInterface[]
     */
    private function getExtensions()
    {
        if (null === $this->sorted) {
            krsort($this->extensions);
            $this->sorted = !empty($this->extensions) ? call_user_func_array('array_merge', $this->extensions) : array();
        }

        return $this->sorted;
    }
}
