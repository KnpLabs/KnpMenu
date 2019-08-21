<?php

namespace Knp\Menu\Tests\Twig;

use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Twig\MenuExtension;
use Knp\Menu\Util\MenuManipulator;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Template;

class MenuExtensionTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(Environment::class)) {
            $this->markTestSkipped('Twig is not available');
        }
    }

    public function testRenderMenu(): void
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

    public function testRenderMenuWithOptions(): void
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

    public function testRenderMenuWithRenderer(): void
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

    public function testRenderMenuByName(): void
    {
        $helper = $this->getHelperMock(['render']);
        $helper->expects($this->once())
            ->method('render')
            ->with('default', [], null)
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu) }}', $helper)->render(['menu' => 'default']));
    }

    public function testRetrieveMenuByName(): void
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

    public function testGetBreadcrumbsArray(): void
    {
        $helper = $this->getHelperMock(['getBreadcrumbsArray']);
        $helper->expects($this->any())
            ->method('getBreadcrumbsArray')
            ->with('default')
            ->will($this->returnValue(['A', 'B']))
        ;

        $this->assertEquals('A, B', $this->getTemplate('{{ knp_menu_get_breadcrumbs_array("default")|join(", ") }}', $helper)->render([]));
    }

    public function testPathAsString(): void
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

    public function testIsCurrent(): void
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

    public function testIsAncestor(): void
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

    public function testGetCurrentItem(): void
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

    private function getTemplate(
        string $template,
        Helper $helper,
        ?MatcherInterface $matcher = null,
        ?MenuManipulator $menuManipulator = null
    ): Template {
        $loader = new ArrayLoader(['index' => $template]);
        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension(new MenuExtension($helper, $matcher, $menuManipulator));

        return $twig->loadTemplate('index');
    }
}
