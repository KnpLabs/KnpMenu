<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class MenuExtension extends \Twig_Extension
{
    private $rendererProvider;
    private $menuProvider;

    /**
     * @param \Knp\Menu\Renderer\RendererProviderInterface $rendererProvider
     * @param \Knp\Menu\Provider\MenuProviderInterface|null $menuProvider
     */
    public function __construct(RendererProviderInterface $rendererProvider, MenuProviderInterface $menuProvider = null)
    {
        $this->rendererProvider = $rendererProvider;
        $this->menuProvider = $menuProvider;
    }

    public function getFunctions()
    {
        return array(
            'knp_menu_get' => new \Twig_Function_Method($this, 'get'),
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
     * @throws \BadMethodCallException when there is no menu provider
     */
    public function get($name)
    {
        if (null === $this->menuProvider) {
            throw new \BadMethodCallException('A menu provider must be set to retrieve a menu');
        }
        return $this->menuProvider->get($name);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param string $renderer
     * @param array $options
     * @return string
     */
    public function render($menu, $renderer, array $options = array())
    {
        if (!$menu instanceof ItemInterface) {
            $menu = $this->get($menu);
        }

        return $this->rendererProvider->get($renderer)->render($menu, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
