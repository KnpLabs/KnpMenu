<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\TwigRenderer;

class TwigRendererTest extends AbstractRendererTest
{
    public function createRenderer()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not available');
        }
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../../../../src/Knp/Menu/Resources/views');
        $environment = new \Twig_Environment($loader);
        $renderer = new TwigRenderer($environment, 'knp_menu.html.twig', true);

        return $renderer;
    }
}
