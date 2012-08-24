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

    public function testGetMenuWithOptions()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('get'));
        $helper->expects($this->once())
            ->method('get')
            ->with('default', array(), array('foo' => 'bar'))
            ->will($this->returnValue($menu))
        ;
        $extension = new MenuExtension($helper);
        $this->assertSame($menu, $extension->get('default', array(), array('foo' => 'bar')));
    }

    public function testGetMenuByPath()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helper = $this->getHelperMock(array('get'));
        $helper->expects($this->once())
            ->method('get')
            ->with('default', array('child'))
            ->will($this->returnValue($menu))
        ;
        $extension = new MenuExtension($helper);
        $this->assertSame($menu, $extension->get('default', array('child')));
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

    private function getHelperMock(array $methods)
    {
        return $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock()
        ;
    }

    /**
     * @param string                $template
     * @param \Knp\Menu\Twig\Helper $helper
     *
     * @return \Twig_Template
     */
    private function getTemplate($template, $helper)
    {
        $loader = new \Twig_Loader_Array(array('index' => $template));
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new MenuExtension($helper));

        return $twig->loadTemplate('index');
    }
}
