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

    public function createFromNode(NodeInterface $node)
    {
        $item = $this->createItem($node->getName(), $node->getOptions());

        foreach ($node->getChildren() as $childNode) {
            $item->addChild($this->createFromNode($childNode));
        }

        return $item;
    }

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
