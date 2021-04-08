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
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var MatcherInterface|null
     */
    private $matcher;

    /**
     * @var MenuManipulator|null
     */
    private $menuManipulator;

    public function __construct(Helper $helper, ?MatcherInterface $matcher = null, ?MenuManipulator $menuManipulator = null)
    {
        $this->helper = $helper;
        $this->matcher = $matcher;
        $this->menuManipulator = $menuManipulator;
    }

    /**
     * @return array<int, TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
             new TwigFunction('knp_menu_get', [$this, 'get']),
             new TwigFunction('knp_menu_render', [$this, 'render'], ['is_safe' => ['html']]),
             new TwigFunction('knp_menu_get_breadcrumbs_array', [$this, 'getBreadcrumbsArray']),
             new TwigFunction('knp_menu_get_current_item', [$this, 'getCurrentItem']),
        ];
    }

    /**
     * @return array<int, TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('knp_menu_as_string', [$this, 'pathAsString']),
        ];
    }

    /**
     * @return array<int, TwigTest>
     */
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
     * @param array<int, string>   $path
     * @param array<string, mixed> $options
     */
    public function get($menu, array $path = [], array $options = []): ItemInterface
    {
        return $this->helper->get($menu, $path, $options);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param ItemInterface|string|array<ItemInterface|string> $menu
     * @param array<string, mixed>                             $options
     */
    public function render($menu, array $options = [], ?string $renderer = null): string
    {
        return $this->helper->render($menu, $options, $renderer);
    }

    /**
     * Returns an array ready to be used for breadcrumbs.
     *
     * @param ItemInterface|string|array<ItemInterface|string> $menu
     * @param string|array<string|null>|null                   $subItem
     * @phpstan-param string|ItemInterface|array<int|string, string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface>|\Traversable<string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface> $subItem
     *
     * @return array<int, array<string, mixed>>
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     */
    public function getBreadcrumbsArray($menu, $subItem = null): array
    {
        return $this->helper->getBreadcrumbsArray($menu, $subItem);
    }

    /**
     * Returns the current item of a menu.
     *
     * @param ItemInterface|string $menu
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
     */
    public function pathAsString(ItemInterface $menu, string $separator = ' > '): string
    {
        if (null === $this->menuManipulator) {
            throw new \BadMethodCallException('The menu manipulator must be set to get the breadcrumbs array');
        }

        return $this->menuManipulator->getPathAsString($menu, $separator);
    }

    /**
     * Checks whether an item is current.
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
     * @param int|null $depth The max depth to look for the item
     */
    public function isAncestor(ItemInterface $item, ?int $depth = null): bool
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isAncestor($item, $depth);
    }
}
