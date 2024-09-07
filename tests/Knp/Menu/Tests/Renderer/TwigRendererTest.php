<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Renderer\RendererInterface;
use Knp\Menu\Renderer\TwigRenderer;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Twig\MenuExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigRendererTest extends AbstractRendererTest
{
    public function createRenderer(MatcherInterface $matcher): RendererInterface
    {
        $loader = new FilesystemLoader(__DIR__.'/../../../../../src/Knp/Menu/Resources/views');
        $environment = new Environment($loader);
        $environment->addExtension(new MenuExtension($this->createMock(Helper::class)));

        return new TwigRenderer($environment, 'knp_menu.html.twig', $matcher, ['compressed' => true]);
    }

    public function testRenderOrderedList(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About')->addChild('foobar');

        $rendered = '<ol><li class="first last"><span>About</span><ol class="menu_level_1"><li class="first last"><span>foobar</span></li></ol></li></ol>';
        $this->assertEquals($rendered, $this->renderer->render($menu, ['template' => 'knp_menu_ordered.html.twig']));
    }
}
