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
     * @param string $template
     * @param MatcherInterface $matcher
     * @param array $defaultOptions
     */
    public function __construct(\Twig_Environment $environment, $template, MatcherInterface $matcher, array $defaultOptions = array())
    {
        $this->environment = $environment;
        $this->matcher = $matcher;
        $this->defaultOptions = array_merge(array(
            'depth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'template' => $template,
            'compressed' => false,
            'allow_safe_labels' => false,
            'clear_matcher' => true,
        ), $defaultOptions);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param ItemInterface $item
     * @param array $options
     * @return string
     */
    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->defaultOptions, $options);

        $template = $options['template'];
        if (!$template instanceof \Twig_Template) {
            $template = $this->environment->loadTemplate($template);
        }

        $block = $options['compressed'] ? 'compressed_root' : 'root';

        $html = $template->renderBlock($block, array('item' => $item, 'options' => $options, 'matcher' => $this->matcher));

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }
}
