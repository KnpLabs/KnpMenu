<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\ListRenderer;

class ListRendererTest extends AbstractRendererTest
{
    protected function createRenderer()
    {
        $renderer = new ListRenderer(array('compressed' => true));

        return $renderer;
    }

    public function testPrettyRendering()
    {
        $renderer = new ListRenderer();
        $rendered = <<<HTML
<ul class="root">
  <li class="first">
    <span>Parent 1</span>
  </li>
  <li class="last">
    <span>Parent 2</span>
  </li>
</ul>

HTML;

        $this->assertEquals($rendered, $renderer->render($this->menu, array('depth' => 1)));
    }
}
