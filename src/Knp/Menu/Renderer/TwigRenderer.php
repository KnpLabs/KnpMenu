<?php

namespace Knp\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Twig\Environment;

class TwigRenderer implements RendererInterface
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var MatcherInterface
     */
    private $matcher;

    /**
     * @var array<string, mixed>
     */
    private $defaultOptions;

    /**
     * @param array<string, mixed> $defaultOptions
     */
    public function __construct(
        Environment $environment,
        string $template,
        MatcherInterface $matcher,
        array $defaultOptions = []
    ) {
        $this->environment = $environment;
        $this->matcher = $matcher;
        $this->defaultOptions = \array_merge([
            'depth' => null,
            'matchingDepth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestor_class' => 'current_ancestor',
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

        // Avoid duplication of current_ancestor class. Overwrite value in old config to new one
        if (isset($options['ancestorClass'])) {
           $options['ancestor_class'] = $options['ancestorClass']; 
           unset($options['ancestorClass']);
           trigger_deprecation('knplabs/knp-menu', '3.3', 'Using "%s" option is deprecated, use "%s" instead.', 'ancestorClass', 'ancestor_class');
        }

        $html = $this->environment->render($options['template'], ['item' => $item, 'options' => $options, 'matcher' => $this->matcher]);

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }
}
