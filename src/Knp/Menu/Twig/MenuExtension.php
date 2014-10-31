<?php

namespace Knp\Menu\Twig;

use Knp\Menu\ItemInterface;

class MenuExtension extends \Twig_Extension
{
    private $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('knp_menu_get', array($this->helper, 'get')),
            new \Twig_SimpleFunction('knp_menu_render', array($this->helper, 'render'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
