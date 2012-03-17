<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\MenuItem;
use Knp\Menu\MenuFactory;

class ListRendererTest extends AbstractRendererTest
{
    protected function createRenderer()
    {
        $renderer = new ListRenderer(array('compressed' => true));

        return $renderer;
    }

    public function testPrettyRendering()
    {
        $menu = new MenuItem('Root li', new MenuFactory());
        $menu->setChildrenAttributes(array('class' => 'root'));
        $menu->addChild('Parent 1');
        $menu->addChild('Parent 2');

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

        $this->assertEquals($rendered, $renderer->render($menu));
    }
}
