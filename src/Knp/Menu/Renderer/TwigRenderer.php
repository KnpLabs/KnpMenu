<?php

namespace Knp\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class TwigRenderer implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;
    private $defaultOptions;

    /**
     * @param \Twig_Environment $environment
     * @param string $template
     * @param array $defaultOptions
     */
    public function __construct(\Twig_Environment $environment, $template, array $defaultOptions = array())
    {
        $this->environment = $environment;
        $this->defaultOptions = array_merge(array(
            'depth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'template' => $template,
            'compressed' => false,
        ), $defaultOptions);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param \Knp\Menu\ItemInterface $item
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

        // we do not call renderBlock here to avoid too many nested level calls (XDebug limits the level to 100 by default)
        ob_start();
        $template->displayBlock($block, array('item' => $item, 'options' => $options));
        $html = ob_get_clean();

        return $html;
    }
}
