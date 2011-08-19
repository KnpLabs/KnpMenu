<?php

namespace Knp\Menu;

/**
 * Factory to create a menu from a tree
 */
class MenuFactory implements FactoryInterface
{
    public function createItem($name, array $options = array())
    {
        $item = new MenuItem($name, $this);

        $options = array_merge(
            array(
                'uri' => null,
                'label' => null,
                'attributes' => array(),
                'linkAttributes' => array(),
                'labelAttributes' => array(),
                'show' => true,
                'showChildren' => true,
            ),
            $options
        );

        $item
            ->setUri($options['uri'])
            ->setLabel($options['label'])
            ->setAttributes($options['attributes'])
            ->setLinkAttributes($options['linkAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setShow($options['show'])
            ->setShowChildren($options['showChildren'])
        ;

        return $item;
    }

    /**
     * Create a menu item from a NodeInterface
     *
     * @param NodeInterface $node
     * @return MenuItem
     */
    public function createFromNode(NodeInterface $node)
    {
        $item = new MenuItem($node->getName(), $this->getUriFromNode($node), $node->getAttributes());
        $item->setLabel($node->getLabel());

        foreach ($node->getChildren() as $childNode) {
            $item->addChild($this->createFromNode($childNode));
        }

        return $item;
    }

    /**
     * Creates a new menu item (and tree if $data['children'] is set).
     *
     * The source is an array of data that should match the output from MenuItem->toArray().
     *
     * @param  array $data The array of data to use as a source for the menu tree
     * @return MenuItem
     */
    public function createFromArray(array $data)
    {
        $class = isset($data['class']) ? $data['class'] : '\Knp\Menu\MenuItem';

        $name = isset($data['name']) ? $data['name'] : null;
        $menu = new $class($name);
        $menu->fromArray($data);

        return $menu;
    }

    /**
     * Get the uri for the given node
     *
     * @param NodeInterface $node
     * @return string
     */
    protected function getUriFromNode(NodeInterface $node)
    {
        return $node->getUri();
    }
}
