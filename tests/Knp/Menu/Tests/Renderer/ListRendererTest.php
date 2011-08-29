<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\MenuItem;
use Knp\Menu\MenuFactory;

class ListRendererTest extends AbstractRendererTest
{
    public function createRenderer()
    {
        $renderer = new ListRenderer();
        $renderer->setRenderCompressed(true);

        return $renderer;
    }
}
