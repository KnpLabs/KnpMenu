<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\SortingRenderer;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Util\MenuManipulator;

class SortingRendererTest extends AbstractRendererTest
{
    protected function createRenderer(MatcherInterface $matcher)
    {
        return new SortingRenderer(
            new ListRenderer($matcher, array('compressed' => true)),
            new MenuManipulator()
        );
    }

    public function testPrettyRendering()
    {
        $factory = new MenuFactory();
        $menu    = $factory->createItem('Root li', array('childrenAttributes' => array('class' => 'root')));
        $menu->addChild('Parent 1', array('extras' => array('weight' => 10)));
        $menu->addChild('Parent 2');

        $rendered = <<<HTML
<ul class="root">
  <li class="first">
    <span>Parent 2</span>
  </li>
  <li class="last">
    <span>Parent 1</span>
  </li>
</ul>

HTML;

        $this->assertEquals($rendered, $this->renderer->render($menu, array('compressed' => false, 'depth' => 1)));
    }
}
