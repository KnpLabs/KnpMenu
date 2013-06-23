<?php

namespace Knp\Menu;

use Knp\Menu\Loader\ArrayLoader;
use Knp\Menu\Loader\NodeLoader;

/**
 * Factory to create a menu from a tree
 */
class MenuFactory implements FactoryInterface
{
    public function createItem($name, array $options = array())
    {
        $item = new MenuItem($name, $this);

        $options = $this->buildOptions($options);
        $this->configureItem($item, $options);

        return $item;
    }

    /**
     * Builds the full option array used to configure the item.
     *
     * @param array $options
     *
     * @return array
     */
    protected function buildOptions(array $options)
    {
        return array_merge(
            array(
                'uri' => null,
                'label' => null,
                'attributes' => array(),
                'linkAttributes' => array(),
                'childrenAttributes' => array(),
                'labelAttributes' => array(),
                'extras' => array(),
                'display' => true,
                'displayChildren' => true,
            ),
            $options
        );
    }

    /**
     * Configures the newly created item with the passed options
     *
     * @param ItemInterface $item
     * @param array         $options
     */
    protected function configureItem(ItemInterface $item, array $options)
    {
        $item
            ->setUri($options['uri'])
            ->setLabel($options['label'])
            ->setAttributes($options['attributes'])
            ->setLinkAttributes($options['linkAttributes'])
            ->setChildrenAttributes($options['childrenAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setExtras($options['extras'])
            ->setDisplay($options['display'])
            ->setDisplayChildren($options['displayChildren'])
        ;
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
}
