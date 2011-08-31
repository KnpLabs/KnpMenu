<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Provider\MenuProviderInterface;

/**
 * Helper class containing logic to retrieve and render menus from templating engines
 *
 */
class Helper
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

    /**
     * Retrieves item in the menu, eventually using the menu provider.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param array $path
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException when the path is invalid
     * @throws \BadMethodCallException when there is no menu provider and the menu is given by name
     */
    public function get($menu, array $path = array())
    {
        if (!$menu instanceof ItemInterface) {
            if (null === $this->menuProvider) {
                throw new \BadMethodCallException('A menu provider must be set to retrieve a menu');
            }
            $menu = $this->menuProvider->get($menu);
        }

        foreach ($path as $child) {
            $menu = $menu->getChild($child);
            if (null === $menu) {
                throw new \InvalidArgumentException(sprintf('The menu has no child named "%s"', $child));
            }
        }

        return $menu;
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * If the argument is an array, it will follow the path in the tree to
     * get the needed item. The first element of the array is the whole menu.
     * If the menu is a string instead of an ItemInterface, the provider
     * will be used.
     *
     * @throws \InvalidArgumentException
     * @param \Knp\Menu\ItemInterface|string|array $menu
     * @param string $renderer
     * @param array $options
     * @return string
     */
    public function render($menu, $renderer, array $options = array())
    {
        if (!$menu instanceof ItemInterface) {
            $path = array();
            if (is_array($menu)) {
                if (empty($menu)) {
                    throw new \InvalidArgumentException('The array cannot be empty');
                }
                $path = $menu;
                $menu = array_shift($path);
            }

            $menu = $this->get($menu, $path);
        }

        return $this->rendererProvider->get($renderer)->render($menu, $options);
    }
}
