<?php

namespace Knp\Menu\Factory;

use Knp\Menu\ItemInterface;

/**
 * core factory extension with the main logic
 */
class CoreExtension implements ExtensionInterface
{
    /**
     * Builds the full option array used to configure the item.
     *
     * @param array $options
     *
     * @return array
     */
    public function buildOptions(array $options)
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
                'current' => null,
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
    public function buildItem(ItemInterface $item, array $options)
    {
        $item
            ->setUri($options['uri'])
            ->setLabel($options['label'])
            ->setAttributes($options['attributes'])
            ->setLinkAttributes($options['linkAttributes'])
            ->setChildrenAttributes($options['childrenAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setExtras($options['extras'])
            ->setCurrent($options['current'])
            ->setDisplay($options['display'])
            ->setDisplayChildren($options['displayChildren'])
        ;
    }
}
