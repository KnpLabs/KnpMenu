<?php

namespace Knp\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;

/**
 * Renders MenuItem tree as unordered list
 */
class ListRenderer extends Renderer implements RendererInterface
{
    /**
     * @var MatcherInterface
     */
    protected $matcher;

    /**
     * @var array<string, mixed>
     */
    protected $defaultOptions;

    /**
     * @param array<string, mixed> $defaultOptions
     */
    public function __construct(MatcherInterface $matcher, array $defaultOptions = [], ?string $charset = null)
    {
        $this->matcher = $matcher;
        $this->defaultOptions = \array_merge([
            'depth' => null,
            'matchingDepth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'compressed' => false,
            'allow_safe_labels' => false,
            'clear_matcher' => true,
            'leaf_class' => null,
            'branch_class' => null,
        ], $defaultOptions);

        parent::__construct($charset);
    }

    public function render(ItemInterface $item, array $options = []): string
    {
        $options = \array_merge($this->defaultOptions, $options);

        $html = $this->renderList($item, $item->getChildrenAttributes(), $options);

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }

    /**
     * @param array<string, string|bool|null> $attributes
     * @param array<string, mixed>            $options
     */
    protected function renderList(ItemInterface $item, array $attributes, array $options): string
    {
        /*
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (0 === $options['depth'] || !$item->hasChildren() || !$item->getDisplayChildren()) {
            return '';
        }

        $html = $this->format('<ul'.$this->renderHtmlAttributes($attributes).'>', 'ul', $item->getLevel(), $options);
        $html .= $this->renderChildren($item, $options);
        $html .= $this->format('</ul>', 'ul', $item->getLevel(), $options);

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
     * @param array<string, mixed> $options the options to render the item
     */
    protected function renderChildren(ItemInterface $item, array $options): string
    {
        // render children with a depth - 1
        if (null !== $options['depth']) {
            --$options['depth'];
        }

        if (null !== $options['matchingDepth'] && $options['matchingDepth'] > 0) {
            --$options['matchingDepth'];
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
     * @param array<string, mixed> $options The options to render the item
     */
    protected function renderItem(ItemInterface $item, array $options): string
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->isDisplayed()) {
            return '';
        }

        // create an array than can be imploded as a class list
        $class = (array) $item->getAttribute('class');

        if ($this->matcher->isCurrent($item)) {
            $class[] = $options['currentClass'];
        } elseif ($this->matcher->isAncestor($item, $options['matchingDepth'])) {
            $class[] = $options['ancestorClass'];
        }

        if ($item->actsLikeFirst()) {
            $class[] = $options['firstClass'];
        }
        if ($item->actsLikeLast()) {
            $class[] = $options['lastClass'];
        }

        if (0 !== $options['depth'] && $item->hasChildren()) {
            if (null !== $options['branch_class'] && $item->getDisplayChildren()) {
                $class[] = $options['branch_class'];
            }
        } elseif (null !== $options['leaf_class']) {
            $class[] = $options['leaf_class'];
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = \implode(' ', $class);
        }

        // opening li tag
        $html = $this->format('<li'.$this->renderHtmlAttributes($attributes).'>', 'li', $item->getLevel(), $options);

        // render the text/link inside the li tag
        //$html .= $this->format($item->getUri() ? $item->renderLink() : $item->renderLabel(), 'link', $item->getLevel());
        $html .= $this->renderLink($item, $options);

        // renders the embedded ul
        $childrenClass = (array) $item->getChildrenAttribute('class');
        $childrenClass[] = 'menu_level_'.$item->getLevel();

        $childrenAttributes = $item->getChildrenAttributes();
        $childrenAttributes['class'] = \implode(' ', $childrenClass);

        $html .= $this->renderList($item, $childrenAttributes, $options);

        // closing li tag
        $html .= $this->format('</li>', 'li', $item->getLevel(), $options);

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
     * @param ItemInterface        $item    The item to render the link or label for
     * @param array<string, mixed> $options The options to render the item
     */
    protected function renderLink(ItemInterface $item, array $options = []): string
    {
        if (null !== $item->getUri() && (!$this->matcher->isCurrent($item) || $options['currentAsLink'])) {
            $text = $this->renderLinkElement($item, $options);
        } else {
            $text = $this->renderSpanElement($item, $options);
        }

        return $this->format($text, 'link', $item->getLevel(), $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function renderLinkElement(ItemInterface $item, array $options): string
    {
        \assert(null !== $item->getUri());

        return \sprintf('<a href="%s"%s>%s</a>', $this->escape($item->getUri()), $this->renderHtmlAttributes($item->getLinkAttributes()), $this->renderLabel($item, $options));
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function renderSpanElement(ItemInterface $item, array $options): string
    {
        return \sprintf('<span%s>%s</span>', $this->renderHtmlAttributes($item->getLabelAttributes()), $this->renderLabel($item, $options));
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function renderLabel(ItemInterface $item, array $options): string
    {
        if ($options['allow_safe_labels'] && $item->getExtra('safe_label', false)) {
            return $item->getLabel();
        }

        return $this->escape($item->getLabel());
    }

    /**
     * If $this->renderCompressed is on, this will apply the necessary
     * spacing and line-breaking so that the particular thing being rendered
     * makes up its part in a fully-rendered and spaced menu.
     *
     * @param string               $html    The html to render in an (un)formatted way
     * @param string               $type    The type [ul,link,li] of thing being rendered
     * @param array<string, mixed> $options
     */
    protected function format(string $html, string $type, int $level, array $options): string
    {
        if ($options['compressed']) {
            return $html;
        }

        $spacing = 0;

        switch ($type) {
            case 'ul':
            case 'link':
                $spacing = $level * 4;
                break;

            case 'li':
                $spacing = $level * 4 - 2;
        }

        return \str_repeat(' ', $spacing).$html."\n";
    }
}
