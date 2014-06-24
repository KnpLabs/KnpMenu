<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\BreadcrumbRenderer;
use Knp\Menu\Tests\TestCase;
use Knp\Menu\Util\MenuManipulator;

class BreadcrumbRendererTest extends TestCase
{
    public function testBreadcrumbRendering()
    {
        $renderer = new BreadcrumbRenderer(new MenuManipulator, array('compressed' => true));

        $expected = '<ul><li>Root li</li><li>Parent 1</li><li>Child 1</li></ul>';

        $this->assertEquals($expected, $renderer->render($this->ch1));
    }

    public function testAdditionalPath()
    {
        $renderer = new BreadcrumbRenderer(new MenuManipulator, array('compressed' => true));

        $expected = '<ul><li>Root li</li><li>Parent 1</li><li>Child 1</li><li><a href="http://example.com">Foo</a></li></ul>';

        $this->assertEquals($expected, $renderer->render($this->ch1, array('additional_path' => array('Foo' => 'http://example.com'))));
    }

    public function testCurrentLink()
    {
        $this->pt1->setUri('foobar')->setCurrent(true);

        $renderer = new BreadcrumbRenderer(new MenuManipulator, array('compressed' => true));

        $expected = '<ul><li>Root li</li><li class="current"><a href="foobar">Parent 1</a></li><li>Child 1</li></ul>';

        $this->assertEquals($expected, $renderer->render($this->ch1));
    }

    public function testCurrentNoLink()
    {
        $this->pt1->setUri('foobar')->setCurrent(true);

        $renderer = new BreadcrumbRenderer(new MenuManipulator, array('compressed' => true, 'currentAsLink' => false));

        $expected = '<ul><li>Root li</li><li class="current">Parent 1</li><li>Child 1</li></ul>';

        $this->assertEquals($expected, $renderer->render($this->ch1));
    }

    public function testCurrentCustomClass()
    {
        $this->pt1->setCurrent(true);

        $renderer = new BreadcrumbRenderer(new MenuManipulator, array('compressed' => true, 'currentClass' => 'foo'));

        $expected = '<ul><li>Root li</li><li class="foo">Parent 1</li><li>Child 1</li></ul>';

        $this->assertEquals($expected, $renderer->render($this->ch1));
    }

    public function testEscapedLabel()
    {
        $this->pt1->setExtra('safe_label', true)->setLabel('<strong>Foo</strong>');

        $renderer = new BreadcrumbRenderer(new MenuManipulator, array('compressed' => true));

        $expected = '<ul><li>Root li</li><li>&lt;strong&gt;Foo&lt;/strong&gt;</li><li>Child 1</li></ul>';

        $this->assertEquals($expected, $renderer->render($this->ch1));
    }

    public function testUnsafeLabel()
    {
        $this->pt1->setLabel('<strong>Foo</strong>');

        $renderer = new BreadcrumbRenderer(new MenuManipulator, array('compressed' => true, 'allow_safe_labels' => true));

        $expected = '<ul><li>Root li</li><li>&lt;strong&gt;Foo&lt;/strong&gt;</li><li>Child 1</li></ul>';

        $this->assertEquals($expected, $renderer->render($this->ch1));
    }

    public function testSafeLabel()
    {
        $this->pt1->setExtra('safe_label', true)->setLabel('<strong>Foo</strong>');

        $renderer = new BreadcrumbRenderer(new MenuManipulator, array('compressed' => true, 'allow_safe_labels' => true));

        $expected = '<ul><li>Root li</li><li><strong>Foo</strong></li><li>Child 1</li></ul>';

        $this->assertEquals($expected, $renderer->render($this->ch1));
    }

    public function testPrettyRendering()
    {
        $renderer = new BreadcrumbRenderer(new MenuManipulator);

        $rendered = <<<HTML
<ul>
  <li>
    Root li
  </li>
  <li>
    Parent 1
  </li>
  <li>
    Child 1
  </li>
</ul>

HTML;

        $this->assertEquals($rendered, $renderer->render($this->ch1));
    }
}
