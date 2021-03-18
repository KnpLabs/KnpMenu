<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\RendererInterface;

final class ListRendererTest extends AbstractRendererTest
{
    protected function createRenderer(MatcherInterface $matcher): RendererInterface
    {
        return new ListRenderer($matcher, ['compressed' => true]);
    }

    public function testPrettyRendering(): void
    {
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

        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['compressed' => false, 'depth' => 1]));
    }
}
