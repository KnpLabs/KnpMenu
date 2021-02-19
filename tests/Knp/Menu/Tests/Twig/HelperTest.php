<?php

namespace Knp\Menu\Tests\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Provider\MenuProviderInterface;
use Knp\Menu\Renderer\RendererInterface;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Util\MenuManipulator;
use PHPUnit\Framework\TestCase;

final class HelperTest extends TestCase
{
    public function testRenderMenu(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, [])
            ->willReturn('<p>foobar</p>')
        ;

        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->willReturn($renderer)
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu));
    }

    public function testRenderMenuWithOptions(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, ['firstClass' => 'test'])
            ->willReturn('<p>foobar</p>')
        ;

        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->willReturn($renderer)
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu, ['firstClass' => 'test']));
    }

    public function testRenderMenuWithRenderer(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, [])
            ->willReturn('<p>foobar</p>')
        ;

        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with('custom')
            ->willReturn($renderer)
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render($menu, [], 'custom'));
    }

    public function testRenderMenuByName(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menuProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($menu)
        ;

        $renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($menu, [])
            ->willReturn('<p>foobar</p>')
        ;

        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->willReturn($renderer)
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render('default'));
    }

    public function testGetMenu(): void
    {
        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $menuProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($menu)
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($menu, $helper->get('default'));
    }

    public function testGetMenuWithoutProvider(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $helper = new Helper($rendererProvider);
        $helper->get('default');
    }

    public function testGetMenuWithOptions(): void
    {
        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $menuProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default', ['foo' => 'bar'])
            ->willReturn($menu)
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($menu, $helper->get('default', [], ['foo' => 'bar']));
    }

    public function testGetMenuByPath(): void
    {
        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $menuProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
        $child = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->willReturn($child)
        ;
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($menu)
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($child, $helper->get('default', ['child']));
    }

    public function testGetMenuByInvalidPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $menuProvider = $this->getMockBuilder(MenuProviderInterface::class)->getMock();
        $child = $this->getMockBuilder(ItemInterface::class)->getMock();
        $child->expects($this->any())
            ->method('getChild')
            ->willReturn(null)
        ;
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->willReturn($child)
        ;
        $menuProvider->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($menu)
        ;

        $helper = new Helper($rendererProvider, $menuProvider);

        $this->assertSame($child, $helper->get('default', ['child', 'invalid']));
    }

    public function testRenderMenuByPath(): void
    {
        $child = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $menu->expects($this->any())
            ->method('getChild')
            ->with('child')
            ->willReturn($child)
        ;

        $renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->with($child, [])
            ->willReturn('<p>foobar</p>')
        ;

        $rendererProvider = $this->getMockBuilder(RendererProviderInterface::class)->getMock();
        $rendererProvider->expects($this->once())
            ->method('get')
            ->with(null)
            ->willReturn($renderer)
        ;

        $helper = new Helper($rendererProvider);

        $this->assertEquals('<p>foobar</p>', $helper->render([$menu, 'child']));
    }

    public function testRenderByEmptyPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The array cannot be empty');

        $helper = new Helper($this->getMockBuilder(RendererProviderInterface::class)->getMock());
        $helper->render([]);
    }

    public function testBreadcrumbsArray(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();

        $manipulator = $this->getMockBuilder(MenuManipulator::class)->getMock();
        $manipulator->expects($this->any())
            ->method('getBreadcrumbsArray')
            ->with($menu)
            ->willReturn(['A', 'B']);

        $helper = new Helper($this->getMockBuilder(RendererProviderInterface::class)->getMock(), null, $manipulator);

        $this->assertEquals(['A', 'B'], $helper->getBreadcrumbsArray($menu));
    }

    public function testCurrentItemWithoutMatcher(): void
    {
        $this->expectException(\BadMethodCallException::class);

        $helper = new Helper($this->getMockBuilder(RendererProviderInterface::class)->getMock());
        $helper->getCurrentItem('default');
    }

    public function testCurrentItem(): void
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

        $helper = new Helper($this->getMockBuilder(RendererProviderInterface::class)->getMock(), null, null, $matcher);

        $this->assertSame('c2_2_2', $helper->getCurrentItem($menu)->getName());
    }
}
