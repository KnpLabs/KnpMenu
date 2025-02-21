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
    private ?MenuRuntimeExtension $runtimeExtension = null;

    public function __construct(
        ?Helper $helper = null,
        ?MatcherInterface $matcher = null,
        ?MenuManipulator $menuManipulator = null,
    ) {
        if (null !== $helper) {
            @trigger_error('Injecting dependencies is deprecated since v3.6 and will be removed in v4.', E_USER_DEPRECATED);
            $this->runtimeExtension = new MenuRuntimeExtension($helper, $matcher, $menuManipulator);
        }
    }

    public function getFunctions(): array
    {
        $legacy = null !== $this->runtimeExtension;

        return [
             new TwigFunction('knp_menu_get', $legacy ? [$this, 'get'] : [MenuRuntimeExtension::class, 'get']),
             new TwigFunction('knp_menu_render', $legacy ? [$this, 'render'] : [MenuRuntimeExtension::class, 'render'], ['is_safe' => ['html']]),
             new TwigFunction('knp_menu_get_breadcrumbs_array', $legacy ? [$this, 'getBreadcrumbsArray'] : [MenuRuntimeExtension::class, 'getBreadcrumbsArray']),
             new TwigFunction('knp_menu_get_current_item', $legacy ? [$this, 'getCurrentItem'] : [MenuRuntimeExtension::class, 'getCurrentItem']),
        ];
    }

    public function getFilters(): array
    {
        $legacy = null !== $this->runtimeExtension;

        return [
            new TwigFilter('knp_menu_as_string', $legacy ? [$this, 'pathAsString'] : [MenuRuntimeExtension::class, 'pathAsString']),
            new TwigFilter('knp_menu_spaceless', [self::class, 'spaceless'], ['is_safe' => ['html']]),
        ];
    }

    public function getTests(): array
    {
        $legacy = null !== $this->runtimeExtension;

        return [
            new TwigTest('knp_menu_current', $legacy ? [$this, 'isCurrent'] : [MenuRuntimeExtension::class, 'isCurrent']),
            new TwigTest('knp_menu_ancestor', $legacy ? [$this, 'isAncestor'] : [MenuRuntimeExtension::class, 'isAncestor']),
        ];
    }

    /**
     * @param array<int, string>   $path
     * @param array<string, mixed> $options
     */
    public function get(ItemInterface|string $menu, array $path = [], array $options = []): ItemInterface
    {
        assert(null !== $this->runtimeExtension);

        return $this->runtimeExtension->get($menu, $path, $options);
    }

    /**
     * @param string|ItemInterface|array<ItemInterface|string> $menu
     * @param array<string, mixed>                             $options
     */
    public function render(array|ItemInterface|string $menu, array $options = [], ?string $renderer = null): string
    {
        assert(null !== $this->runtimeExtension);

        return $this->runtimeExtension->render($menu, $options, $renderer);
    }

    /**
     * @param string|ItemInterface|array<ItemInterface|string> $menu
     * @param string|array<string|null>|null                   $subItem
     *
     * @phpstan-param string|ItemInterface|array<int|string, string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface>|\Traversable<string|int|float|null|array{label: string, url: string|null, item: ItemInterface|null}|ItemInterface> $subItem
     *
     * @return array<int, array<string, mixed>>
     * @phpstan-return list<array{label: string, uri: string|null, item: ItemInterface|null}>
     */
    public function getBreadcrumbsArray(array|ItemInterface|string $menu, array|string|null $subItem = null): array
    {
        assert(null !== $this->runtimeExtension);

        return $this->runtimeExtension->getBreadcrumbsArray($menu, $subItem);
    }

    public function getCurrentItem(ItemInterface|string $menu): ItemInterface
    {
        assert(null !== $this->runtimeExtension);

        return $this->runtimeExtension->getCurrentItem($menu);
    }

    public function pathAsString(ItemInterface $menu, string $separator = ' > '): string
    {
        assert(null !== $this->runtimeExtension);

        return $this->runtimeExtension->pathAsString($menu, $separator);
    }

    public function isCurrent(ItemInterface $item): bool
    {
        assert(null !== $this->runtimeExtension);

        return $this->runtimeExtension->isCurrent($item);
    }

    public function isAncestor(ItemInterface $item, ?int $depth = null): bool
    {
        assert(null !== $this->runtimeExtension);

        return $this->runtimeExtension->isAncestor($item, $depth);
    }

    /**
     * @internal
     */
    public static function spaceless(string $content): string
    {
        return trim((string) preg_replace('/>\s+</', '><', $content));
    }
}
