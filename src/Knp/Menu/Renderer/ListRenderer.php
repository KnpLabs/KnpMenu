<?php

namespace Knp\Menu\Renderer;

use \Knp\Menu\ItemInterface;

/**
 * Renders MenuItem tree as unordered list
 */
class ListRenderer extends Renderer implements RendererInterface
{
    /**
     * @see RendererInterface::render
     */
    public function render(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || 0 === $options['depth'] || !$item->getDisplayChildren()) {
            return '';
        }

        $html = $this->format('<ul'.$this->renderHtmlAttributes($item->getAttributes()).'>', 'ul', $item->getLevel());
        $html .= $this->renderChildren($item, $options);
        $html .= $this->format('</ul>', 'ul', $item->getLevel());

        return $html;
    }

    /**
     * Renders all of the children of this menu.
     *
     * This calls ->renderItem() on each menu item, which instructs each
     * menu item to render themselves as an <li> tag (with nested ul if it
     * has children).
     * This method updates the depth for the children.
     *
     * @param \Knp\Menu\ItemInterface $item
     * @param array $options The options to render the item.
     * @return string
     */
    public function renderChildren(ItemInterface $item, array $options)
    {
        // render children with a depth - 1
        if (null !== $options['depth']) {
            $options['depth'] = $options['depth'] - 1;
        }

        $html = '';
        foreach ($item->getChildren() as $child) {
            $html .= $this->renderItem($child, $options);
        }

        return $html;
    }

    /**
     * Called by the parent menu item to render this menu.
     *
     * This renders the li tag to fit into the parent ul as well as its
     * own nested ul tag if this menu item has children
     *
     * @param \Knp\Menu\ItemInterface $item
     * @param array $options The options to render the item
     * @return string
     */
    public function renderItem(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        // if we don't have access or this item is marked to not be shown
        if (!$item->isDisplayed()) {
            return '';
        }

        // explode the class string into an array of classes
        $class = ($item->getAttribute('class')) ? explode(' ', $item->getAttribute('class')) : array();

        if ($item->isCurrent()) {
            $class[] = $options['currentClass'];
        } elseif ($item->isCurrentAncestor()) {
            $class[] = $options['ancestorClass'];
        }

        if ($item->actsLikeFirst()) {
            $class[] = $options['firstClass'];
        }
        if ($item->actsLikeLast()) {
            $class[] = $options['lastClass'];
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        // opening li tag
        $html = $this->format('<li'.$this->renderHtmlAttributes($attributes).'>', 'li', $item->getLevel());

        // render the text/link inside the li tag
        //$html .= $this->format($item->getUri() ? $item->renderLink() : $item->renderLabel(), 'link', $item->getLevel());
        $html .= $this->renderLink($item, $options);

        // renders the embedded ul if there are visible children
        if ($item->hasChildren() && 0 !== $options['depth'] && $item->getDisplayChildren()) {

            $childrenClass = ($item->getChildrenAttribute('class')) ? explode(' ', $item->getChildrenAttribute('class')) : array();
            $childrenClass[] = 'menu_level_'.$item->getLevel();

            $childrenAttributes = $item->getChildrenAttributes();
            $childrenAttributes['class'] = implode(' ', $childrenClass);

            $html .= $this->format('<ul'.$this->renderHtmlAttributes($childrenAttributes).'>', 'ul', $item->getLevel());
            $html .= $this->renderChildren($item, $options);
            $html .= $this->format('</ul>', 'ul', $item->getLevel());
        }

        // closing li tag
        $html .= $this->format('</li>', 'li', $item->getLevel());

        return $html;
    }

    /**
     * Renders the link in a a tag with link attributes or
     * the label in a span tag with label attributes
     *
     * Tests if item has a an uri and if not tests if it's
     * the current item and if the text has to be rendered
     * as a link or not.
     *
     * @param \Knp\Menu\ItemInterface $item The item to render the link or label for
     * @param array $options The options to render the item
     * @return string
     */
    public function renderLink(ItemInterface $item, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        if ($item->getUri() && (!$item->isCurrent() || $options['currentAsLink'])) {
            $text = sprintf('<a href="%s"%s>%s</a>', $this->escape($item->getUri()), $this->renderHtmlAttributes($item->getLinkAttributes()), $this->escape($item->getLabel()));
        } else {
            $text = sprintf('<span%s>%s</span>', $this->renderHtmlAttributes($item->getLabelAttributes()), $this->escape($item->getLabel()));
        }

        return $this->format($text, 'link', $item->getLevel());
    }

    /**
     * If $this->renderCompressed is on, this will apply the necessary
     * spacing and line-breaking so that the particular thing being rendered
     * makes up its part in a fully-rendered and spaced menu.
     *
     * @param  string $html The html to render in an (un)formatted way
     * @param  string $type The type [ul,link,li] of thing being rendered
     * @param integer $level
     * @return string
     */
    protected function format($html, $type, $level)
    {
        if ($this->renderCompressed) {
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

    protected function getDefaultOptions()
    {
        return array(
            'depth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
        );
    }
}
