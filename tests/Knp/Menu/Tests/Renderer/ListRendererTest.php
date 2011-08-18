<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\MenuItem;

class ListRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Knp\Menu\Renderer\ListRenderer
     */
    private $renderer;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $menu;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $pt1;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $ch1;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $ch2;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $ch3;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $pt2;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $ch4;

    /**
     * @var \Knp\Menu\MenuItem
     */
    private $gc1;

    public function setUp()
    {
        $this->renderer = new ListRenderer();
        $this->renderer->setRenderCompressed(true);
        $this->menu = new MenuItem('Root li', null, array('class' => 'root'));
        $this->menu->setRenderer($this->renderer);
        $this->pt1 = $this->menu->addChild('Parent 1');
        $this->ch1 = $this->pt1->addChild('Child 1');
        $this->ch2 = $this->pt1->addChild('Child 2');

        // add the 3rd child via addChild with an object
        $this->ch3 = new MenuItem('Child 3');
        $this->pt1->addChild($this->ch3);

        $this->pt2 = $this->menu->addChild('Parent 2');
        $this->ch4 = $this->pt2->addChild('Child 4');
        $this->gc1 = $this->ch4->addChild('Grandchild 1');
    }

    public function tearDown()
    {
        $this->renderer = null;
        $this->menu = null;
        $this->pt1 = null;
        $this->ch1 = null;
        $this->ch2 = null;
        $this->ch3 = null;
        $this->pt2 = null;
        $this->ch4 = null;
        $this->gc1 = null;
    }

    public function testRenderEmptyRoot()
    {
        $menu = new MenuItem('test');
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderRootWithAttributes()
    {
        $menu = new MenuItem('test', null, array('class' => 'test_class'));
        $menu->addChild('c1');
        $rendered = '<ul class="test_class"><li class="first last"><span>c1</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderEncodedAttributes()
    {
        $menu = new MenuItem('test', null, array('title' => 'encode " me >'));
        $menu->addChild('c1');
        $rendered = '<ul title="encode &quot; me &gt;"><li class="first last"><span>c1</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLink()
    {
        $about = $this->menu->addChild('About', '/about');

        $rendered = '<a href="/about">About</a>';
        $this->assertEquals($rendered, $this->renderer->renderLink($about));

        $rendered = '<li class="last"><a href="/about">About</a></li>';
        $this->assertEquals($rendered, $this->renderer->renderItem($about));
    }

    public function testRenderLinkWithAttributes()
    {
        $about = $this->menu->addChild('About', '/about');
        $about->setLinkAttribute('title', 'About page');

        $rendered = '<li class="last"><a href="/about" title="About page">About</a></li>';
        $this->assertEquals($rendered, $this->renderer->renderItem($about));
    }

    public function testRenderWeirdLink()
    {
        $about = $this->menu->addChild('About', 'http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&v2=2');

        $rendered = '<a href="http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&v2=2">About</a>';
        $this->assertEquals($rendered, $this->renderer->renderLink($about));

        $rendered = '<li class="last"><a href="http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&v2=2">About</a></li>';
        $this->assertEquals($rendered, $this->renderer->renderItem($about));
    }

    public function testRenderWholeMenu()
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testToString()
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, (string) $this->menu);
    }

    public function testRenderWithClassAndTitle()
    {
        $this->pt2->setAttribute('class', 'parent2_class');
        $this->pt2->setAttribute('title', 'parent2 title');
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="parent2_class last" title="parent2 title"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testRenderWithCurrentItem()
    {
        $this->ch2->setIsCurrent(true);
        $rendered = '<ul class="root"><li class="current_ancestor first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li class="current"><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testRenderWithCurrentItemAsLink()
    {
        $about = $this->menu->addChild('About', '/about');
        $about->setIsCurrent(true);
        $this->menu->setCurrentAsLink(true);

        $rendered = '<li class="current last"><a href="/about">About</a></li>';
        $this->assertEquals($rendered, $this->renderer->renderItem($about));
    }

    public function testRenderWithCurrentItemNotAsLink()
    {
        $about = $this->menu->addChild('About', '/about');
        $about->setIsCurrent(true);
        $this->menu->setCurrentAsLink(false);

        $rendered = '<li class="current last"><span>About</span></li>';
        $this->assertEquals($rendered, $this->renderer->renderItem($about));
    }

    public function testRenderSubMenuPortionWithClassAndTitle()
    {
        $this->pt2->setAttribute('class', 'parent2_class')->setAttribute('title', 'parent2 title');
        $rendered = '<ul class="parent2_class" title="parent2 title"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu['Parent 2']));
    }

    public function testDoNotShowChildrenRendersNothing()
    {
        $this->menu->setShowChildren(false);
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDoNotShowChildChildrenRendersPartialMenu()
    {
        $this->menu['Parent 1']->setShowChildren(false);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDoNotShowChildRendersPartialMenu()
    {
        $this->menu['Parent 1']->setShow(false);
        $rendered = '<ul class="root"><li class="first last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDepth0()
    {
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, 0));
    }

    public function testDepth1()
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, 1));
    }

    public function testDepth2()
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, 2));
    }

    public function testDepth2WithNotShowChildChildren()
    {
        $this->menu['Parent 1']->setShowChildren(false);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, 2));
    }
}
