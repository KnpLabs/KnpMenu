<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Util\MenuManipulator;
use Knp\Menu\Matcher\MatcherInterface;

class MenuExtension extends \Twig_Extension
{
    private $helper;
    private $matcher;
    private $menuManipulator;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper, MatcherInterface $matcher = null, MenuManipulator $menuManipulator = null)
    {
        $this->helper = $helper;
        $this->matcher = $matcher;
        $this->menuManipulator = $menuManipulator;
    }

    public function getFunctions()
    {
        return array(
             new \Twig_SimpleFunction('knp_menu_get', array($this, 'get')),
             new \Twig_SimpleFunction('knp_menu_render', array($this, 'render'), array('is_safe' => array('html'))),
             new \Twig_SimpleFunction('knp_menu_get_breadcrumbs_array', array($this, 'getBreadcrumbsArray')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('knp_menu_as_string', array($this, 'pathAsString')),
        );
    }

    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('knp_menu_current', array($this, 'isCurrent')),
            new \Twig_SimpleTest('knp_menu_ancestor', array($this, 'isAncestor')),
        );
    }
    
    /**
     * Retrieves an item following a path in the tree.
     *
     * @param ItemInterface|string $menu
     * @param array                $path
     * @param array                $options
     *
     * @return ItemInterface
     */
    public function get($menu, array $path = array(), array $options = array())
    {
        return $this->helper->get($menu, $path, $options);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param ItemInterface|string|array $menu
     * @param array                      $options
     * @param string                     $renderer
     *
     * @return string
     */
    public function render($menu, array $options = array(), $renderer = null)
    {
        return $this->helper->render($menu, $options, $renderer);
    }

    /**
     * Returns an array ready to be used for breadcrumbs.
     *
     * @param ItemInterface|array|string $item
     * @param string|array|null          $subItem
     *
     * @return array
     */
    public function getBreadcrumbsArray($menu, $subItem = null)
    {
        return $this->helper->getBreadcrumbsArray($menu, $subItem);
    }

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     *
     * @param ItemInterface $item
     * @param string        $separator
     *
     * @return string
     */
    public function pathAsString(ItemInterface $menu, $separator = ' > ')
    {
        if (null === $this->menuManipulator) {
            throw new \BadMethodCallException('The menu manipulator must be set to get the breadcrumbs array');
        }

        return $this->menuManipulator->getPathAsString($menu, $separator);
    }

    /**
     * Checks whether an item is current.
     *
     * @param ItemInterface $item
     *
     * @return boolean
     */
    public function isCurrent(ItemInterface $item)
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isCurrent($item);
    }

    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param ItemInterface $item
     * @param integer       $depth The max depth to look for the item
     *
     * @return boolean
     */
    public function isAncestor(ItemInterface $item, $depth = null)
    {
        if (null === $this->matcher) {
            throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');
        }

        return $this->matcher->isAncestor($item);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
