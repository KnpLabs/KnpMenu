<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;

class MenuExtension extends \Twig_Extension
{
    private $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function getFunctions()
    {
        return array(
            'knp_menu_get' => new \Twig_Function_Method($this, 'get'),
            'knp_menu_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
        );
    }

    /**
     * Retrieves an item following a path in the tree.
     *
     * @param ItemInterface|string $menu
     * @param array                $path
     * @param array                $options
     *
     * @return ItemInterface
     */
    public function get($menu, array $path = array(), array $options = array())
    {
        return $this->helper->get($menu, $path, $options);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param ItemInterface|string|array $menu
     * @param array                      $options
     * @param string                     $renderer
     *
     * @return string
     */
    public function render($menu, array $options = array(), $renderer = null)
    {
        return $this->helper->render($menu, $options, $renderer);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
