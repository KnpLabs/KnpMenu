<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\MenuItem;
use Knp\Menu\MenuFactory;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Tests\TestCase;

abstract class AbstractRendererTest extends TestCase
{
    /**
     * @var \Knp\Menu\Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @var MatcherInterface
     */
    private $matcher;

    protected function setUp()
    {
        parent::setUp();
        $this->matcher = new Matcher();
        $this->renderer = $this->createRenderer($this->matcher);
    }

    abstract protected function createRenderer(MatcherInterface $matcher);

    protected function tearDown()
    {
        parent::tearDown();
        $this->renderer = null;
    }

    public function testRenderEmptyRoot()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderRootWithAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->setChildrenAttributes(array('class' => 'test_class'));
        $menu->addChild('c1');
        $rendered = '<ul class="test_class"><li class="first last"><span>c1</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderEncodedAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->setChildrenAttributes(array('title' => 'encode " me >'));
        $menu->addChild('c1');
        $rendered = '<ul title="encode &quot; me &gt;"><li class="first last"><span>c1</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLink()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('uri' => '/about'));

        $rendered = '<ul><li class="first last"><a href="/about">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLinkWithAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('uri' => '/about', 'linkAttributes' => array('title' => 'About page')));

        $rendered = '<ul><li class="first last"><a href="/about" title="About page">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLinkWithEmptyAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array(
            'uri' => '/about',
            'linkAttributes' => array('title' => '', 'rel' => null, 'target' => false)
        ));

        $rendered = '<ul><li class="first last"><a href="/about" title="">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLinkWithSpecialAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('uri' => '/about', 'linkAttributes' => array('title' => true)));

        $rendered = '<ul><li class="first last"><a href="/about" title="title">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderChildrenWithAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About');
        $about->addChild('Us');
        $about->setChildrenAttribute('title', 'About page');

        $rendered = '<ul><li class="first last"><span>About</span><ul title="About page" class="menu_level_1"><li class="first last"><span>Us</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderChildrenWithEmptyAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About');
        $about->addChild('Us');
        $about->setChildrenAttribute('title', '');
        $about->setChildrenAttribute('rel', null);
        $about->setChildrenAttribute('target', false);

        $rendered = '<ul><li class="first last"><span>About</span><ul title="" class="menu_level_1"><li class="first last"><span>Us</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderChildrenWithSpecialAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About');
        $about->addChild('Us');
        $about->setChildrenAttribute('title', true);

        $rendered = '<ul><li class="first last"><span>About</span><ul title="title" class="menu_level_1"><li class="first last"><span>Us</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLabelWithAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('labelAttributes' => array('title' => 'About page')));

        $rendered = '<ul><li class="first last"><span title="About page">About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLabelWithEmptyAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('labelAttributes' => array('title' => '', 'rel' => null, 'target' => false)));

        $rendered = '<ul><li class="first last"><span title="">About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLabelWithSpecialAttributes()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('labelAttributes' => array('title' => true)));

        $rendered = '<ul><li class="first last"><span title="title">About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderWeirdLink()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('uri' => 'http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&v2=2'));

        $rendered = '<ul><li class="first last"><a href="http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&amp;v2=2">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderEscapedLabel()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('label' => 'Encode " me'));
        $menu->addChild('Safe', array('label' => 'Encode " me again', 'extras' => array('safe_label' => true)));
        $menu->addChild('Escaped', array('label' => 'Encode " me too', 'extras' => array('safe_label' => false)));

        $rendered = '<ul><li class="first"><span>Encode &quot; me</span></li><li><span>Encode &quot; me again</span></li><li class="last"><span>Encode &quot; me too</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderSafeLabel()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', array('label' => 'Encode " me'));
        $menu->addChild('Safe', array('label' => 'Encode " me again', 'extras' => array('safe_label' => true)));
        $menu->addChild('Escaped', array('label' => 'Encode " me too', 'extras' => array('safe_label' => false)));

        $rendered = '<ul><li class="first"><span>Encode &quot; me</span></li><li><span>Encode " me again</span></li><li class="last"><span>Encode &quot; me too</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu, array('allow_safe_labels' => true)));
    }

    public function testRenderWholeMenu()
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
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
        $this->ch2->setCurrent(true);
        $rendered = '<ul class="root"><li class="current_ancestor first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li class="current"><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testRenderWithCurrentItemAsLink()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About', array('uri' => '/about'));
        $about->setCurrent(true);

        $rendered = '<ul><li class="current first last"><a href="/about">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderWithCurrentItemNotAsLink()
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About', array('uri' => '/about'));
        $about->setCurrent(true);

        $rendered = '<ul><li class="current first last"><span>About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu, array('currentAsLink' => false)));
    }

    public function testRenderSubMenuPortionWithClassAndTitle()
    {
        $this->pt2->setChildrenAttribute('class', 'parent2_class')->setChildrenAttribute('title', 'parent2 title');
        $rendered = '<ul class="parent2_class" title="parent2 title"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu['Parent 2']));
    }

    public function testDoNotShowChildrenRendersNothing()
    {
        $this->menu->setDisplayChildren(false);
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDoNotShowChildChildrenRendersPartialMenu()
    {
        $this->menu['Parent 1']->setDisplayChildren(false);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDoNotShowChildRendersPartialMenu()
    {
        $this->menu['Parent 1']->setDisplay(false);
        $rendered = '<ul class="root"><li class="first last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDepth0()
    {
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, array('depth' => 0)));
    }

    public function testDepth1()
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, array('depth' => 1)));
    }

    public function testDepth2()
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, array('depth' => 2)));
    }

    public function testDepth2WithNotShowChildChildren()
    {
        $this->menu['Parent 1']->setDisplayChildren(false);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, array('depth' => 2)));
    }

    public function testEmptyUncompressed()
    {
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, array('depth' => 0, 'compressed' => false)));
    }

    public function testMatchingDepth0()
    {
        $this->menu['Parent 1']['Child 1']->setCurrent(true);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, array('depth' => 1,'matchingDepth' => 1)));
    }

    public function testMatchingDepth1()
    {
        $this->menu['Parent 1']['Child 1']->setCurrent(true);
        $rendered = '<ul class="root"><li class="current_ancestor first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, array('depth' => 1,'matchingDepth' => 2)));
    }

    public function testMatchingDepth2()
    {
        $this->menu['Parent 1']['Child 1']->setCurrent(true);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, array('depth' => 1,'matchingDepth' => 0)));
    }
}
