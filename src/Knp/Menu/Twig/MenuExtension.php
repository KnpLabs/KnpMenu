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

    public function __construct(Helper $helper, MatcherInterface $matcher = null, MenuManipulator $menuManipulator = null)
    {
        $this->helper = $helper;
        $this->matcher = $matcher;
        $this->menuManipulator = $menuManipulator;
    }

    public function getFunctions(): array
    {
        return [
             new TwigFunction('knp_menu_get', [$this, 'get']),
             new TwigFunction('knp_menu_render', [$this, 'render'], ['is_safe' => ['html']]),
             new TwigFunction('knp_menu_get_breadcrumbs_array', [$this, 'getBreadcrumbsArray']),
             new TwigFunction('knp_menu_get_current_item', [$this, 'getCurrentItem']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('knp_menu_as_string', [$this, 'pathAsString']),
        ];
    }

    public function getTests(): array
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
    public function get($menu, array $path = [], array $options = []): ItemInterface
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
    public function render($menu, array $options = [], $renderer = null): string
    {
        return $this->helper->render($menu, $options, $renderer);
    }

    /**
     * Returns an array ready to be used for breadcrumbs.
     *
     * @param ItemInterface|array|string $menu
     * @param string|array|null          $subItem
     *
     * @return array
     */
    public function getBreadcrumbsArray($menu, $subItem = null): array
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
    public function getCurrentItem($menu): ItemInterface
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
     * @param ItemInterface $menu
     * @param string        $separator
     *
     * @return string
     */
    public function pathAsString(ItemInterface $menu, $separator = ' > '): string
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
     * @return bool
     */
    public function isCurrent(ItemInterface $item): bool
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
     * @param int|null      $depth The max depth to look for the item
     *
     * @return bool
     */
    public function isAncestor(ItemInterface $item, ?int $depth = null): bool
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isAncestor($item, $depth);
    }
}
