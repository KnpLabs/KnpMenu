<?php

namespace Knp\Menu\Tests\Twig;

use Knp\Menu\Twig\MenuExtension;

class MenuExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not available');
        }
    }

    public function testRenderMenu()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('render'));
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, array(), null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu) }}', $helper)->render(array('menu' => $menu)));
    }

    public function testRenderMenuWithOptions()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('render'));
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, array('firstClass' => 'test'), null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu, {"firstClass": "test"}) }}', $helper)->render(array('menu' => $menu)));
    }

    public function testRenderMenuWithRenderer()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('render'));
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, array(), 'custom')
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu, {}, "custom") }}', $helper)->render(array('menu' => $menu)));
    }

    public function testRenderMenuByName()
    {
        $helper = $this->getHelperMock(array('render'));
        $helper->expects($this->once())
            ->method('render')
            ->with('default', array(), null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu) }}', $helper)->render(array('menu' => 'default')));
    }

    public function testRetrieveMenuByName()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('get', 'render'));
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, array(), null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;
        $helper->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(knp_menu_get("default")) }}', $helper)->render(array()));
    }

    public function testGetBreadcrumbsArray()
    {
        $helper = $this->getHelperMock(array('getBreadcrumbsArray'));
        $helper->expects($this->any())
            ->method('getBreadcrumbsArray')
            ->with('default')
            ->will($this->returnValue(array('A', 'B')))
        ;

        $this->assertEquals('A, B', $this->getTemplate('{{ knp_menu_get_breadcrumbs_array("default")|join(", ") }}', $helper)->render(array()));
    }

    public function testPathAsString()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('get'));
        $manipulator = $this->getManipulatorMock(array('getPathAsString'));
        $helper->expects($this->any())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu));
        $manipulator->expects($this->any())
            ->method('getPathAsString')
            ->with($menu)
            ->will($this->returnValue('A > B'))
        ;

        $this->assertEquals('A &gt; B', $this->getTemplate('{{ knp_menu_get("default")|knp_menu_as_string }}', $helper, null, $manipulator)->render(array()));
    }

    public function testIsCurrent()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array());
        $matcher = $this->getMatcherMock();
        $matcher->expects($this->any())
            ->method('isCurrent')
            ->with($menu)
            ->will($this->returnValue(true))
        ;

        $this->assertEquals('current', $this->getTemplate('{{ menu is knp_menu_current ? "current" : "not current" }}', $helper, $matcher)->render(array('menu' => $menu)));
    }

    public function testIsAncestor()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array());
        $matcher = $this->getMatcherMock();
        $matcher->expects($this->any())
            ->method('isAncestor')
            ->with($menu)
            ->will($this->returnValue(false))
        ;

        $this->assertEquals('not ancestor', $this->getTemplate('{{ menu is knp_menu_ancestor ? "ancestor" : "not ancestor" }}', $helper, $matcher)->render(array('menu' => $menu)));
    }

    private function getHelperMock(array $methods)
    {
        return $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock()
        ;
    }

    private function getManipulatorMock(array $methods)
    {
        return $this->getMockBuilder('Knp\Menu\Util\MenuManipulator')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock()
        ;
    }

    private function getMatcherMock()
    {
        return $this->getMock('Knp\Menu\Matcher\MatcherInterface');
    }

    /**
     * @param string                $template
     * @param \Knp\Menu\Twig\Helper $helper
     *
     * @return \Twig_Template
     */
    private function getTemplate($template, $helper, $matcher = null, $menuManipulator = null)
    {
        $loader = new \Twig_Loader_Array(array('index' => $template));
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new MenuExtension($helper, $matcher, $menuManipulator));

        return $twig->loadTemplate('index');
    }
}
