<?php

namespace Knp\Menu\Tests\Twig;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Twig\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function testRenderMenu()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu));
    }

    public function testRenderMenuWithOptions()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array('firstClass' => 'test'))
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu, array('firstClass' => 'test')));
    }

    public function testRenderMenuWithRenderer()
    {
        $menu = $this->getMockBuilder(  'Knp\Menu\ItemInterface')->getMock();
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with('custom')
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu, array(), 'custom'));
    }

    public function testRenderMenuByName()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $menuProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render('default'));
    }

    public function testGetMenu()
    {
        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $menuProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($menu, $helper->get('default'));
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetMenuWithBadReturnValue()
    {
        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $menuProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue(new \stdClass()))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);
        $helper->get('default');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetMenuWithoutProvider()
    {
        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $helper = new Helper($rendererProvider);
        $helper->get('default');
    }

    public function testGetMenuWithOptions()
    {
        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $menuProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default', array('foo' => 'bar'))
            ->will($this->returnValue($menu))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($menu, $helper->get('default', array(), array('foo' => 'bar')));
    }

    public function testGetMenuByPath()
    {
        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $menuProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $child = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->will($this->returnValue($child))
        ;
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($child, $helper->get('default', array('child')));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMenuByInvalidPath()
    {
        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $menuProvider = $this->getMockBuilder('Knp\Menu\Provider\MenuProviderInterface')->getMock();
        $child = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $child->expects($this->any())
            ->method('getChild')
            ->will($this->returnValue(null))
        ;
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->will($this->returnValue($child))
        ;
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($child, $helper->get('default', array('child', 'invalid')));
    }

    public function testRenderMenuByPath()
    {
        $child = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->will($this->returnValue($child))
        ;

        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($child, array())
            ->will($this->returnValue('<p>foobar</p>'))
        ;

        $rendererProvider = $this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->will($this->returnValue($renderer))
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render(array($menu, 'child')));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The array cannot be empty
     */
    public function testRenderByEmptyPath()
    {
        $helper = new Helper($this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock());
        $helper->render(array());
    }

    public function testBreadcrumbsArray()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();

        $manipulator = $this->getMockBuilder('Knp\Menu\Util\MenuManipulator')->getMock();
        $manipulator->expects($this->any())
            ->method('getBreadcrumbsArray')
            ->with($menu)
            ->will($this->returnValue(array('A', 'B')));

        $helper = new Helper($this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock(), null, $manipulator);

        $this->assertEquals(array('A', 'B'), $helper->getBreadcrumbsArray($menu));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCurrentItemWithoutMatcher()
    {
        $helper = new Helper($this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock());
        $helper->getCurrentItem('default');
    }

    public function testCurrentItem()
    {
        $matcher = new Matcher();

        $menu = new MenuItem('root', new MenuFactory());
        $menu->addChild('c1');
        $menu['c1']->addChild('c1_1');
        $menu->addChild('c2');
        $menu['c2']->addChild('c2_1');
        $menu['c2']->addChild('c2_2');
        $menu['c2']['c2_2']->addChild('c2_2_1');
        $menu['c2']['c2_2']->addChild('c2_2_2')->setCurrent(true);
        $menu['c2']['c2_2']->addChild('c2_2_3');

        $helper = new Helper($this->getMockBuilder('Knp\Menu\Renderer\RendererProviderInterface')->getMock(), null, null, $matcher);

        $this->assertSame('c2_2_2', $helper->getCurrentItem($menu)->getName());
    }
}
