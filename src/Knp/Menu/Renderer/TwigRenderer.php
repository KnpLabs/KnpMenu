<?php

namespace Knp\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Twig\Environment;

class TwigRenderer extends BaseRenderer
{
    /**
     * @var Environment
     */
    private $environment;
    private $matcher;

    /**
     * @param Environment      $environment
     * @param string           $template
     * @param MatcherInterface $matcher
     * @param array            $defaultOptions
     */
    public function __construct(Environment $environment, $template, MatcherInterface $matcher, array $defaultOptions = array())
    {
        $this->environment = $environment;
        $this->matcher = $matcher;

        $defaultOptions = array_merge(array(
            'template' => $template
        ), $defaultOptions);

        parent::__construct($defaultOptions);
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
