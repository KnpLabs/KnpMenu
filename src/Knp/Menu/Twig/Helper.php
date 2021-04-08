<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Util\MenuManipulator;

/**
 * Helper class containing logic to retrieve and render menus from templating engines
 */
class Helper
{
    /**
     * @var RendererProviderInterface
     */
    private $rendererProvider;

    /**
     * @var MenuProviderInterface|null
     */
    private $menuProvider;

    /**
     * @var MenuManipulator|null
     */
    private $menuManipulator;

    /**
     * @var MatcherInterface|null
     */
    private $matcher;

    public function __construct(
        RendererProviderInterface $rendererProvider,
        ?MenuProviderInterface $menuProvider = null,
        ?MenuManipulator $menuManipulator = null,
        ?MatcherInterface $matcher = null
    ) {
        $this->rendererProvider = $rendererProvider;
        $this->menuProvider = $menuProvider;
        $this->menuManipulator = $menuManipulator;
        $this->matcher = $matcher;
    }

    /**
     * Retrieves item in the menu, eventually using the menu provider.
     *
     * @param ItemInterface|string $menu
     * @param array<int, string>   $path
     * @param array<string, mixed> $options
     *
     * @throws \BadMethodCallException   when there is no menu provider and the menu is given by name
     * @throws \LogicException
     * @throws \InvalidArgumentException when the path is invalid
     */
    public function get($menu, array $path = [], array $options = []): ItemInterface
    {
        if (!$menu instanceof ItemInterface) {
            if (null === $this->menuProvider) {
                throw new \BadMethodCallException('A menu provider must be set to retrieve a menu');
            }

            $menuName = $menu;
            $menu = $this->menuProvider->get($menuName, $options);

            if (!$menu instanceof ItemInterface) {
                throw new \LogicException(\sprintf('The menu "%s" exists, but is not a valid menu item object. Check where you created the menu to be sure it returns an ItemInterface object.', $menuName));
            }
        }

        foreach ($path as $child) {
            $menu = $menu->getChild($child);
            if (null === $menu) {
                throw new \InvalidArgumentException(\sprintf('The menu has no child named "%s"', $child));
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
     * @param ItemInterface|string|array<ItemInterface|string> $menu
     * @param array<string, mixed>                             $options
     *
     * @throws \InvalidArgumentException
     */
    public function render($menu, array $options = [], ?string $renderer = null): string
    {
        $menu = $this->castMenu($menu);

        return $this->rendererProvider->get($renderer)->render($menu, $options);
    }

    /**
     * Renders an array ready to be used for breadcrumbs.
     *
     * Each element in the array will be an array with 3 keys:
     * - `label` containing the label of the item
     * - `url` containing the url of the item (may be `null`)
     * - `item` containing the original item (may be `null` for the extra items)
     *
     * The subItem can be one of the following forms
     *   * 'subItem'
     *   * ItemInterface object
     *   * ['subItem' => '@homepage']
     *   * ['subItem1', 'subItem2']
     *   * [['label' => 'subItem1', 'url' => '@homepage'], ['label' => 'subItem2']]
     *
     * @param mixed $menu
     * @param mixed $subItem A string or array to append onto the end of the array
     * @phpstan-param string|ItemInterface|array<int|string, string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface>|\Traversable<string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface> $subItem
     *
     * @return array<int, array<string, mixed>>
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     */
    public function getBreadcrumbsArray($menu, $subItem = null): array
    {
        if (null === $this->menuManipulator) {
            throw new \BadMethodCallException('The menu manipulator must be set to get the breadcrumbs array');
        }

        $menu = $this->castMenu($menu);

        return $this->menuManipulator->getBreadcrumbsArray($menu, $subItem);
    }

    /**
     * Returns the current item of a menu.
     *
     * @param ItemInterface|string|array<ItemInterface|string> $menu
     */
    public function getCurrentItem($menu): ?ItemInterface
    {
        $menu = $this->castMenu($menu);

        return $this->retrieveCurrentItem($menu);
    }

    /**
     * @param ItemInterface|string|array<ItemInterface|string> $menu
     */
    private function castMenu($menu): ItemInterface
    {
        if (!$menu instanceof ItemInterface) {
            $path = [];
            if (\is_array($menu)) {
                if (empty($menu)) {
                    throw new \InvalidArgumentException('The array cannot be empty');
                }
                $path = $menu;
                $menu = \array_shift($path);
            }

            return $this->get($menu, $path);
        }

        return $menu;
    }

    private function retrieveCurrentItem(ItemInterface $item): ?ItemInterface
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the current item of a menu');
        }

        if ($this->matcher->isCurrent($item)) {
            return $item;
        }

        if ($this->matcher->isAncestor($item)) {
            foreach ($item->getChildren() as $child) {
                $currentItem = $this->retrieveCurrentItem($child);

                if (null !== $currentItem) {
                    return $currentItem;
                }
            }
        }

        return null;
    }
}
