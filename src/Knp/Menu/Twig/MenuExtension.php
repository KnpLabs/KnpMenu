<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class MenuExtension extends \Twig_Extension
{
    private $helper;

    /**
     * @param \Knp\Menu\Twig\Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function getFunctions()
    {
        return array(
            'knp_menu_get' => new \Twig_Function_Method($this, 'get'),
            'knp_menu_getByPath' => new \Twig_Function_Method($this, 'getByPath'),
        );
    }

    public function getFilters()
    {
        return array(
            'knp_menu_render' => new \Twig_Filter_Method($this, 'render', array('is_safe' => array('html'))),
        );
    }

    /**
     * Retrieves a menu from the menu provider.
     *
     * @param string $name
     * @return \Knp\Menu\ItemInterface
     */
    public function get($name)
    {
        return $this->helper->get($name);
    }

    /**
     * Retrieves an item following a path in the tree.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param array $path
     * @return \Knp\Menu\ItemInterface
     */
    public function getByPath($menu, array $path)
    {
        return $this->helper->getByPath($menu, $path);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param \Knp\Menu\ItemInterface|string|array $menu
     * @param string $renderer
     * @param array $options
     * @return string
     */
    public function render($menu, $renderer, array $options = array())
    {
        return $this->helper->render($menu, $renderer, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
