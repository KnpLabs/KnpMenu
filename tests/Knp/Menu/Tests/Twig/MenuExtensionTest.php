<?php

namespace Knp\Menu\Tests\Twig;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Twig\MenuExtension;
use Knp\Menu\Twig\MenuRuntimeExtension;
use Knp\Menu\Util\MenuManipulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\TemplateWrapper;

final class MenuExtensionTest extends TestCase
{
    public function testRenderMenu(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $helperMock = $this->getHelperMock(['render']);
        $helperMock->expects($this->once())
            ->method('render')
            ->with($menu, [], null)
            ->willReturn('<p>foobar</p>')
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        $matcher = null;
        $manipulator = null;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu) }}', $helper, $matcher, $manipulator)->render(['menu' => $menu]));
    }

    public function testRenderMenuWithOptions(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $helperMock = $this->getHelperMock(['render']);
        $helperMock->expects($this->once())
            ->method('render')
            ->with($menu, ['firstClass' => 'test'], null)
            ->willReturn('<p>foobar</p>')
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        $matcher = null;
        $manipulator = null;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu, {"firstClass": "test"}) }}', $helper, $matcher, $manipulator)->render(['menu' => $menu]));
    }



    public function testRenderMenuByName(): void
    {
        $helperMock = $this->getHelperMock(['render']);
        $helperMock->expects($this->once())
            ->method('render')
            ->with('default', [], null)
            ->willReturn('<p>foobar</p>')
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        $matcher = null;
        $manipulator = null;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(menu) }}', $helper, $matcher, $manipulator)->render(['menu' => 'default']));
    }

    public function testRetrieveMenuByName(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $helperMock = $this->getHelperMock(['get', 'render']);
        $helperMock->expects($this->once())
            ->method('render')
            ->with($menu, [], null)
            ->willReturn('<p>foobar</p>')
        ;
        $helperMock->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($menu)
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        $matcher = null;
        $manipulator = null;

        $this->assertEquals('<p>foobar</p>', $this->getTemplate('{{ knp_menu_render(knp_menu_get("default")) }}', $helper, $matcher, $manipulator)->render([]));
    }

    public function testGetBreadcrumbsArray(): void
    {
        $helperMock = $this->getHelperMock(['getBreadcrumbsArray']);
        $helperMock->expects($this->any())
            ->method('getBreadcrumbsArray')
            ->with('default')
            ->willReturn(['A', 'B'])
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        $matcher = null;
        $manipulator = null;

        $this->assertEquals('A, B', $this->getTemplate('{{ knp_menu_get_breadcrumbs_array("default")|join(", ") }}', $helper, $matcher, $manipulator)->render([]));
    }

    public function testPathAsString(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $helperMock = $this->getHelperMock(['get']);
        $manipulatorMock = $this->getManipulatorMock(['getPathAsString']);
        $helperMock->expects($this->any())
            ->method('get')
            ->with('default')
            ->willReturn($menu);
        $manipulatorMock->expects($this->any())
            ->method('getPathAsString')
            ->with($menu)
            ->willReturn('A > B')
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        /** @var MenuManipulator&MockObject $manipulator */
        $manipulator = $manipulatorMock;
        $matcher = null;

        $this->assertEquals('A &gt; B', $this->getTemplate('{{ knp_menu_get("default")|knp_menu_as_string }}', $helper, $matcher, $manipulator)->render([]));
    }

    public function testIsCurrent(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $helperMock = $this->getHelperMock([]);
        $matcherMock = $this->getMatcherMock();
        $matcherMock->expects($this->any())
            ->method('isCurrent')
            ->with($menu)
            ->willReturn(true)
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        /** @var MatcherInterface&MockObject $matcher */
        $matcher = $matcherMock;
        $manipulator = null;

        $this->assertEquals('current', $this->getTemplate('{{ menu is knp_menu_current ? "current" : "not current" }}', $helper, $matcher, $manipulator)->render(['menu' => $menu]));
    }

    public function testIsAncestor(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $helperMock = $this->getHelperMock([]);
        $matcherMock = $this->getMatcherMock();
        $matcherMock->expects($this->any())
            ->method('isAncestor')
            ->with($menu)
            ->willReturn(false)
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        /** @var MatcherInterface&MockObject $matcher */
        $matcher = $matcherMock;
        $manipulator = null;

        $this->assertEquals('not ancestor', $this->getTemplate('{{ menu is knp_menu_ancestor ? "ancestor" : "not ancestor" }}', $helper, $matcher, $manipulator)->render(['menu' => $menu]));
    }

    public function testGetCurrentItem(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $helperMock = $this->getHelperMock(['get', 'getCurrentItem']);
        $helperMock->expects($this->once())
            ->method('get')
            ->with('default')
            ->willReturn($menu)
        ;
        $matcherMock = $this->getMatcherMock();
        $matcherMock->expects($this->any())
            ->method('isCurrent')
            ->with($menu)
            ->willReturn(true)
        ;
        /** @var Helper&MockObject $helper */
        $helper = $helperMock;
        /** @var MatcherInterface&MockObject $matcher */
        $matcher = $matcherMock;
        $manipulator = null;

        $this->assertEquals('current', $this->getTemplate('{{ knp_menu_get_current_item("default") is knp_menu_current ? "current" : "not current" }}', $helper, $matcher, $manipulator)->render([]));
    }

    public function testLastModified(): void
    {
        $this->assertSame(max(
            filemtime((string) (new \ReflectionClass(MenuExtension::class))->getFileName()),
            filemtime((string) (new \ReflectionClass(MenuRuntimeExtension::class))->getFileName()),
        ), (new MenuExtension())->getLastModified());
    }

    /**
     * @param array<string> $methods
     */
    private function getHelperMock(array $methods): MockObject
    {
        return $this->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @param array<string> $methods
     */
    private function getManipulatorMock(array $methods): MockObject
    {
        return $this->getMockBuilder(MenuManipulator::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock()
        ;
    }

    private function getMatcherMock(): MockObject
    {
        return $this->getMockBuilder(MatcherInterface::class)->getMock();
    }

    private function getTemplate(
        string $template,
        Helper $helper,
        ?MatcherInterface $matcher = null,
        ?MenuManipulator $menuManipulator = null,
    ): TemplateWrapper {
        $loader = new ArrayLoader(['index' => $template]);
        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension(new MenuExtension());
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            MenuRuntimeExtension::class => fn () => new MenuRuntimeExtension(
                $helper,
                $matcher,
                $menuManipulator,
            ),
        ]));

        return $twig->load('index');
    }
}
