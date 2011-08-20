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
                'display' => true,
                'displayChildren' => true,
            ),
            $options
        );

        $item
            ->setUri($options['uri'])
            ->setLabel($options['label'])
            ->setAttributes($options['attributes'])
            ->setLinkAttributes($options['linkAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setDisplay($options['display'])
            ->setDisplayChildren($options['displayChildren'])
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
        $item = $this->createItem($node->getName(), $node->getOptions());

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
}
