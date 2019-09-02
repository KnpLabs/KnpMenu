<?php

namespace Knp\Menu\Tests\Twig;

use Knp\Menu\Twig\MenuExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Template;

final class MenuExtensionTest extends TestCase
{
    protected function setUp()
    {
        if (!\class_exists(Environment::class)) {
            $this->markTestSkipped('Twig is not available');
        }
    }

    public function testRenderMenu()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $helper = $this->getHelperMock(['render']);
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, [], null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu) }}', $helper)->render(['menu' => $menu]));
    }

    public function testRenderMenuWithOptions()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $helper = $this->getHelperMock(['render']);
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, ['firstClass' => 'test'], null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu, {"firstClass": "test"}) }}', $helper)->render(['menu' => $menu]));
    }

    public function testRenderMenuWithRenderer()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $helper = $this->getHelperMock(['render']);
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, [], 'custom')
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu, {}, "custom") }}', $helper)->render(['menu' => $menu]));
    }

    public function testRenderMenuByName()
    {
        $helper = $this->getHelperMock(['render']);
        $helper->expects($this->once())
            ->method('render')
            ->with('default', [], null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu) }}', $helper)->render(['menu' => 'default']));
    }

    public function testRetrieveMenuByName()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $helper = $this->getHelperMock(['get', 'render']);
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, [], null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;
        $helper->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(knp_menu_get("default")) }}', $helper)->render([]));
    }

    public function testGetBreadcrumbsArray()
    {
        $helper = $this->getHelperMock(['getBreadcrumbsArray']);
        $helper->expects($this->any())
            ->method('getBreadcrumbsArray')
            ->with('default')
            ->will($this->returnValue(['A', 'B']))
        ;

        $this->assertEquals('A, B', $this->getTemplate('{{ knp_menu_get_breadcrumbs_array("default")|join(", ") }}', $helper)->render([]));
    }

    public function testPathAsString()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $helper = $this->getHelperMock(['get']);
        $manipulator = $this->getManipulatorMock(['getPathAsString']);
        $helper->expects($this->any())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu));
        $manipulator->expects($this->any())
            ->method('getPathAsString')
            ->with($menu)
            ->will($this->returnValue('A > B'))
        ;

        $this->assertEquals('A &gt; B', $this->getTemplate('{{ knp_menu_get("default")|knp_menu_as_string }}', $helper, null, $manipulator)->render([]));
    }

    public function testIsCurrent()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $helper = $this->getHelperMock([]);
        $matcher = $this->getMatcherMock();
        $matcher->expects($this->any())
            ->method('isCurrent')
            ->with($menu)
            ->will($this->returnValue(true))
        ;

        $this->assertEquals('current', $this->getTemplate('{{ menu is knp_menu_current ? "current" : "not current" }}', $helper, $matcher)->render(['menu' => $menu]));
    }

    public function testIsAncestor()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $helper = $this->getHelperMock([]);
        $matcher = $this->getMatcherMock();
        $matcher->expects($this->any())
            ->method('isAncestor')
            ->with($menu)
            ->will($this->returnValue(false))
        ;

        $this->assertEquals('not ancestor', $this->getTemplate('{{ menu is knp_menu_ancestor ? "ancestor" : "not ancestor" }}', $helper, $matcher)->render(['menu' => $menu]));
    }

    public function testGetCurrentItem()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $helper = $this->getHelperMock(['get', 'getCurrentItem']);
        $helper->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;
        $matcher = $this->getMatcherMock();
        $matcher->expects($this->any())
            ->method('isCurrent')
            ->with($menu)
            ->will($this->returnValue(true))
        ;

        $this->assertEquals('current', $this->getTemplate('{{ knp_menu_get_current_item("default") is knp_menu_current ? "current" : "not current" }}', $helper, $matcher)->render([]));
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
        return $this->getMockBuilder('Knp\Menu\Matcher\MatcherInterface')->getMock();
    }

    /**
     * @param string                                  $template
     * @param \Knp\Menu\Twig\Helper                   $helper
     * @param \Knp\Menu\Matcher\MatcherInterface|null $matcher
     * @param \Knp\Menu\Util\MenuManipulator|null     $menuManipulator
     *
     * @return Template
     */
    private function getTemplate($template, $helper, $matcher = null, $menuManipulator = null)
    {
        $loader = new ArrayLoader(['index' => $template]);
        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension(new MenuExtension($helper, $matcher, $menuManipulator));

        return $twig->loadTemplate('index');
    }
}
