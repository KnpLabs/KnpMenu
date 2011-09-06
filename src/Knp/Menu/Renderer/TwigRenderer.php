<?php

namespace Knp\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class TwigRenderer extends \Twig_Extension implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;
    private $defaultTemplate;
    private $renderCompressed = false;

    /**
     * @param string $template
     * @param boolean $renderCompressed
     */
    public function __construct($template, $renderCompressed = false)
    {
        $this->defaultTemplate = $template;
        $this->renderCompressed = $renderCompressed;
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'knp_menu_twig_item' => new \Twig_Function_Method($this, 'renderItem', array('is_safe' => array('html'))),
        );
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
        return $this->renderBlock('root', $item, $options);
    }

    /**
     * Renders a menu item.
     *
     * Used internally in the template to render children.
     *
     * @param \Knp\Menu\ItemInterface $item
     * @param array $options
     * @return string
     */
    public function renderItem(ItemInterface $item, array $options = array())
    {
        return $this->renderBlock('item', $item, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu_renderer';
    }

    private function renderBlock($block, ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        $template = $options['template'];
        if (!$template instanceof \Twig_Template) {
            $template = $this->environment->loadTemplate($template);
        }

        if ($options['compressed']) {
            $block = 'compressed_'.$block;
        }

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
            'labelEscape' => true,
        );
    }
}
