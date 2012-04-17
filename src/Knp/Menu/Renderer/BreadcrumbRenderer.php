<?php

namespace Knp\Menu\Renderer;

use \Knp\Menu\ItemInterface;

/**
 * Renders MenuItem tree as a breadcrumb
 */
class BreadcrumbRenderer extends Renderer implements RendererInterface
{
    private $defaultOptions;

    /**
     * @param array $defaultOptions
     * @param string $charset
     */
    public function __construct(array $defaultOptions = array(), $charset = null)
    {
        $this->defaultOptions = array_merge(array(
            'additional_path' => null,
            'compressed' => false,
            'root_attributes' => array(),
        ), $defaultOptions);

        parent::__construct($charset);
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

        $breadcrumb = $item->getBreadcrumbsArray($options['additional_path']);

        if (empty($breadcrumb)) {
            return '';
        }

        return $this->renderBreadcrumb($breadcrumb, $options);
    }

    /**
     * Renders the breadcrumb
     *
     * @param array $breadcrumb
     * @param array $options
     * @return string
     */
    protected function renderBreadcrumb(array $breadcrumb, array $options)
    {
        $html = $this->format('<ul'.$this->renderHtmlAttributes($options['root_attributes']).'>', 'ul', 0, $options);
        $html .= $this->renderList($breadcrumb, $options);
        $html .= $this->format('</ul>', 'ul', 0, $options);

        return $html;
    }

    /**
     * Renders the breadcrumb list
     *
     * @param array $breadcrumb
     * @param array $options
     * @return string
     */
    protected function renderList(array $breadcrumb, array $options)
    {
        $html = '';
        foreach ($breadcrumb as $label => $uri) {
            $html .= $this->renderItem($label, $uri, $options);
        }

        return $html;
    }

    /**
     * @param string $label
     * @param string $uri
     * @param array $options
     * @return string
     */
    protected function renderItem($label, $uri, array $options)
    {
        // opening li tag
        $html = $this->format('<li>', 'li', 1, $options);

        // render the text/link inside the li tag
        if (null === $uri) {
            $content = $this->renderLabel($label, $options);
        } else {
            $content = sprintf('<a href="%s">%s</a>', $this->escape($uri), $this->renderLabel($label, $options));
        }
        $html .= $this->format($content, 'link', 1, $options);

        // closing li tag
        $html .= $this->format('</li>', 'li', 1, $options);

        return $html;
    }

    protected function renderLabel($label, array $options)
    {
        return $this->escape($label);
    }

    /**
     * If $this->renderCompressed is on, this will apply the necessary
     * spacing and line-breaking so that the particular thing being rendered
     * makes up its part in a fully-rendered and spaced menu.
     *
     * @param  string $html The html to render in an (un)formatted way
     * @param  string $type The type [ul,link,li] of thing being rendered
     * @param integer $level
     * @param array $options
     * @return string
     */
    protected function format($html, $type, $level, array $options)
    {
        if ($options['compressed']) {
            return $html;
        }

        switch ($type) {
            case 'ul':
            case 'link':
                $spacing = $level * 4;
                break;

            case 'li':
                $spacing = $level * 4 - 2;
                break;
        }

        return str_repeat(' ', $spacing).$html."\n";
    }
}
