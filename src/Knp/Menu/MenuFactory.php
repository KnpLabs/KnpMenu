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
                'childrenAttributes' => array(),
                'labelAttributes' => array(),
                'extras' => array(),
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
            ->setChildrenAttributes($options['childrenAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setExtras($options['extras'])
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
     * @param  string $name The name of the source (if not set in data['name'])
     * @return MenuItem
     */
    public function createFromArray(array $data, $name = null)
    {
        $name = isset($data['name']) ? $data['name'] : $name;
        if (isset($data['children'])) {
            $children = $data['children'];
            unset($data['children']);
        } else {
            $children = array();
        }

        $item = $this->createItem($name, $data);
        foreach ($children as $name => $child) {
            $item->addChild($this->createFromArray($child, $name));
        }

        return $item;
    }
}
