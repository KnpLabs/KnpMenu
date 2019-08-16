<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Util\MenuManipulator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class MenuExtension extends AbstractExtension
{
    private $helper;
    private $matcher;
    private $menuManipulator;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper, MatcherInterface $matcher = null, MenuManipulator $menuManipulator = null)
    {
        $this->helper = $helper;
        $this->matcher = $matcher;
        $this->menuManipulator = $menuManipulator;
    }

    public function getFunctions()
    {
        return [
             new TwigFunction('knp_menu_get', [$this, 'get']),
             new TwigFunction('knp_menu_render', [$this, 'render'], ['is_safe' => ['html']]),
             new TwigFunction('knp_menu_get_breadcrumbs_array', [$this, 'getBreadcrumbsArray']),
             new TwigFunction('knp_menu_get_current_item', [$this, 'getCurrentItem']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('knp_menu_as_string', [$this, 'pathAsString']),
        ];
    }

    public function getTests()
    {
        return [
            new TwigTest('knp_menu_current', [$this, 'isCurrent']),
            new TwigTest('knp_menu_ancestor', [$this, 'isAncestor']),
        ];
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
    public function get($menu, array $path = [], array $options = [])
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
    public function render($menu, array $options = [], $renderer = null)
    {
        return $this->helper->render($menu, $options, $renderer);
    }

    /**
     * Returns an array ready to be used for breadcrumbs.
     *
     * @param ItemInterface|array|string $item
     * @param string|array|null          $subItem
     *
     * @return array
     */
    public function getBreadcrumbsArray($menu, $subItem = null)
    {
        return $this->helper->getBreadcrumbsArray($menu, $subItem);
    }

    /**
     * Returns the current item of a menu.
     *
     * @param ItemInterface|string $menu
     *
     * @return ItemInterface
     */
    public function getCurrentItem($menu)
    {
        $rootItem = $this->get($menu);

        $currentItem = $this->helper->getCurrentItem($rootItem);

        if (null === $currentItem) {
            $currentItem = $rootItem;
        }

        return $currentItem;
    }

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     *
     * @param ItemInterface $item
     * @param string        $separator
     *
     * @return string
     */
    public function pathAsString(ItemInterface $menu, $separator = ' > ')
    {
        if (null === $this->menuManipulator) {
            throw new \BadMethodCallException('The menu manipulator must be set to get the breadcrumbs array');
        }

        return $this->menuManipulator->getPathAsString($menu, $separator);
    }

    /**
     * Checks whether an item is current.
     *
     * @param ItemInterface $item
     *
     * @return boolean
     */
    public function isCurrent(ItemInterface $item)
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isCurrent($item);
    }

    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param ItemInterface $item
     * @param integer       $depth The max depth to look for the item
     *
     * @return boolean
     */
    public function isAncestor(ItemInterface $item, $depth = null)
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isAncestor($item, $depth);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
