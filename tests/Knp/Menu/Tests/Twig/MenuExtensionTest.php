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
            ->with($menu, 'default', array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ menu|knp_menu_render("default") }}', $helper)->render(array('menu' => $menu)));
    }

    public function testRenderMenuWithOptions()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('render'));
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, 'default', array('firstClass' => 'test'))
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ menu|knp_menu_render("default", {"firstClass": "test"}) }}', $helper)->render(array('menu' => $menu)));
    }

    public function testRenderMenuByName()
    {
        $helper = $this->getHelperMock(array('render'));
        $helper->expects($this->once())
            ->method('render')
            ->with('default', 'default', array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ menu|knp_menu_render("default") }}', $helper)->render(array('menu' => 'default')));
    }

    public function testGetMenu()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('get'));
        $helper->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;
        $extension = new MenuExtension($helper);
        $this->assertSame($menu, $extension->get('default'));
    }

    public function testGetMenuByPath()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('getByPath'));
        $helper->expects($this->once())
            ->method('getByPath')
            ->with('default', array('child'))
            ->will($this->returnValue($menu))
        ;
        $extension = new MenuExtension($helper);
        $this->assertSame($menu, $extension->getByPath('default', array('child')));
    }

    public function testRetrieveMenuByName()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('get', 'render'));
        $helper->expects($this->once())
            ->method('render')
            ->with($menu, 'default', array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;
        $helper->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_get("default")|knp_menu_render("default") }}', $helper)->render(array()));
    }

    private function getHelperMock(array $methods)
    {
        return $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock()
        ;
    }

    /**
     * @param string $template
     * @param \Knp\Menu\Twig\Helper $helper
     * @return \Twig_TemplateInterface
     */
    private function getTemplate($template, $helper)
    {
        $loader = new \Twig_Loader_Array(array('index' => $template));
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new MenuExtension($helper));

        return $twig->loadTemplate('index');
    }
}
