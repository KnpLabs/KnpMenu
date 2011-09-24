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
    private $defaultTemplate;
    private $renderCompressed = false;

    /**
     * @param \Twig_Environment $environment
     * @param string $template
     * @param boolean $renderCompressed
     */
    public function __construct(\Twig_Environment $environment, $template, $renderCompressed = false)
    {
        $this->environment = $environment;
        $this->defaultTemplate = $template;
        $this->renderCompressed = $renderCompressed;
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
        $options = array_merge($this->getDefaultOptions(), $options);

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

    private function getDefaultOptions()
    {
        return array(
            'depth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'template' => $this->defaultTemplate,
            'compressed' => $this->renderCompressed,
        );
    }
}
