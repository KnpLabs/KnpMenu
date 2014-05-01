<?php

namespace Knp\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;

class TwigRenderer implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;
    private $matcher;
    private $defaultOptions;

    /**
     * @param \Twig_Environment $environment
     * @param string            $template
     * @param MatcherInterface  $matcher
     * @param array             $defaultOptions
     */
    public function __construct(\Twig_Environment $environment, $template, MatcherInterface $matcher, array $defaultOptions = array())
    {
        $this->environment = $environment;
        $this->matcher = $matcher;
        $this->defaultOptions = array_merge(array(
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
        ), $defaultOptions);
    }

    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->defaultOptions, $options);

        $html = $this->environment->render($options['template'], array('item' => $item, 'options' => $options, 'matcher' => $this->matcher));

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }
}
