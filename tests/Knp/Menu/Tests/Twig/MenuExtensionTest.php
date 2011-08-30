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
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($renderer))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ menu|knp_menu_render("default") }}', $rendererProvider)->render(array('menu' => $menu)));
    }

    public function testRenderMenuWithOptions()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array('firstClass' => 'test'))
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($renderer))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ menu|knp_menu_render("default", {"firstClass": "test"}) }}', $rendererProvider)->render(array('menu' => $menu)));
    }

    public function testRenderMenuByName()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $menuProvider = $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($renderer))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ menu|knp_menu_render("default") }}', $rendererProvider, $menuProvider)->render(array('menu' => 'default')));
    }

    public function testGetMenu()
    {
        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $menuProvider = $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;
        $extension = new MenuExtension($rendererProvider, $menuProvider);
        $this->assertSame($menu, $extension->get('default'));
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testGetMenuWithoutProvider()
    {
        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $extension = new MenuExtension($rendererProvider);
        $extension->get('default');
    }

    public function testRetrieveMenuByName()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $menuProvider = $this->getMock('Knp\Menu\Provider\MenuProviderInterface');
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMock('Knp\Menu\Renderer\RendererProviderInterface');
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($renderer))
        ;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_get("default")|knp_menu_render("default") }}', $rendererProvider, $menuProvider)->render(array()));
    }

    /**
     * @param string $template
     * @param \Knp\Menu\Renderer\RendererProviderInterface $rendererProvider
     * @param \Knp\Menu\Provider\MenuProviderInterface|null $menuProvider
     * @return \Twig_TemplateInterface
     */
    private function getTemplate($template, $rendererProvider, $menuProvider = null)
    {
        $loader = new \Twig_Loader_Array(array('index' => $template));
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension(new MenuExtension($rendererProvider, $menuProvider));

        return $twig->loadTemplate('index');
    }
}
