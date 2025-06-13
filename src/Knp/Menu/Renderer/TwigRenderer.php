<?php

namespace Knp\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Twig\Environment;

/**
 * @final since 3.8.0
 */
class TwigRenderer implements RendererInterface
{
    /**
     * @param array<string, mixed> $defaultOptions
     */
    public function __construct(
        private Environment $environment,
        string $template,
        private MatcherInterface $matcher,
        private array $defaultOptions = []
    ) {
        $this->defaultOptions = \array_merge([
            'depth' => null,
            'matchingDepth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'template' => $template,
            'compressed' => false,
            'allow_safe_labels' => false,
            'clear_matcher' => true,
            'leaf_class' => null,
            'branch_class' => null,
        ], $defaultOptions);
    }

    public function render(ItemInterface $item, array $options = []): string
    {
        $options = \array_merge($this->defaultOptions, $options);

        $html = $this->environment->render($options['template'], ['item' => $item, 'options' => $options, 'matcher' => $this->matcher]);

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }
}
