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
    private $matcher;
    private $defaultOptions;

    /**
     * @param Environment      $environment
     * @param string           $template
     * @param MatcherInterface $matcher
     * @param array            $defaultOptions
     */
    public function __construct(Environment $environment, $template, MatcherInterface $matcher, array $defaultOptions = [])
    {
        $this->environment = $environment;
        $this->matcher = $matcher;
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

    public function render(ItemInterface $item, array $options = [])
    {
        $options = \array_merge($this->defaultOptions, $options);

        $html = $this->environment->render($options['template'], ['item' => $item, 'options' => $options, 'matcher' => $this->matcher]);

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }
}
