<?php

namespace Knp\Menu\Tests\Renderer;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Matcher\Voter\UriVoter;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Renderer\RendererInterface;
use Knp\Menu\Tests\MenuTestCase;

abstract class AbstractRendererTest extends MenuTestCase
{
    /**
     * @var RendererInterface|null
     */
    protected $renderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderer = $this->createRenderer(new Matcher());
    }

    abstract protected function createRenderer(MatcherInterface $matcher): RendererInterface;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->renderer = null;
    }

    public function testRenderEmptyRoot(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderRootWithAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->setChildrenAttributes(['class' => 'test_class']);
        $menu->addChild('c1');
        $rendered = '<ul class="test_class"><li class="first last"><span>c1</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderEncodedAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->setChildrenAttributes(['title' => 'encode " me >']);
        $menu->addChild('c1');
        $rendered = '<ul title="encode &quot; me &gt;"><li class="first last"><span>c1</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLink(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['uri' => '/about']);

        $rendered = '<ul><li class="first last"><a href="/about">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLinkWithAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['uri' => '/about', 'linkAttributes' => ['title' => 'About page']]);

        $rendered = '<ul><li class="first last"><a href="/about" title="About page">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLinkWithEmptyAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', [
            'uri' => '/about',
            'linkAttributes' => ['title' => '', 'rel' => null, 'target' => false],
        ]);

        $rendered = '<ul><li class="first last"><a href="/about" title="">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLinkWithSpecialAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['uri' => '/about', 'linkAttributes' => ['title' => true]]);

        $rendered = '<ul><li class="first last"><a href="/about" title="title">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderChildrenWithAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About');
        $about->addChild('Us');
        $about->setChildrenAttribute('title', 'About page');

        $rendered = '<ul><li class="first last"><span>About</span><ul title="About page" class="menu_level_1"><li class="first last"><span>Us</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderChildrenWithEmptyAttributes(): void
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

    public function testRenderChildrenWithSpecialAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About');
        $about->addChild('Us');
        $about->setChildrenAttribute('title', true);

        $rendered = '<ul><li class="first last"><span>About</span><ul title="title" class="menu_level_1"><li class="first last"><span>Us</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLabelWithAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['labelAttributes' => ['title' => 'About page']]);

        $rendered = '<ul><li class="first last"><span title="About page">About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLabelWithEmptyAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['labelAttributes' => ['title' => '', 'rel' => null, 'target' => false]]);

        $rendered = '<ul><li class="first last"><span title="">About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderLabelWithSpecialAttributes(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['labelAttributes' => ['title' => true]]);

        $rendered = '<ul><li class="first last"><span title="title">About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderWeirdLink(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['uri' => 'http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&v2=2']);

        $rendered = '<ul><li class="first last"><a href="http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&amp;v2=2">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderEscapedLabel(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['label' => 'Encode " me']);
        $menu->addChild('Safe', ['label' => 'Encode " me again', 'extras' => ['safe_label' => true]]);
        $menu->addChild('Escaped', ['label' => 'Encode " me too', 'extras' => ['safe_label' => false]]);

        $rendered = '<ul><li class="first"><span>Encode &quot; me</span></li><li><span>Encode &quot; me again</span></li><li class="last"><span>Encode &quot; me too</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderSafeLabel(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['label' => 'Encode " me']);
        $menu->addChild('Safe', ['label' => 'Encode " me again', 'extras' => ['safe_label' => true]]);
        $menu->addChild('Escaped', ['label' => 'Encode " me too', 'extras' => ['safe_label' => false]]);

        $rendered = '<ul><li class="first"><span>Encode &quot; me</span></li><li><span>Encode " me again</span></li><li class="last"><span>Encode &quot; me too</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu, ['allow_safe_labels' => true]));
    }

    public function testRenderWholeMenu(): void
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testRenderWithClassAndTitle(): void
    {
        $this->pt2->setAttribute('class', 'parent2_class');
        $this->pt2->setAttribute('title', 'parent2 title');
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="parent2_class last" title="parent2 title"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testRenderWithCurrentItem(): void
    {
        $this->ch2->setCurrent(true);
        $rendered = '<ul class="root"><li class="current_ancestor first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li class="current"><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testRenderWithCurrentItemAsLink(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About', ['uri' => '/about']);
        $about->setCurrent(true);

        $rendered = '<ul><li class="current first last"><a href="/about">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderWithCurrentItemAsLinkUsingMatcherWithVoters(): void
    {
        $matcher = new Matcher([new UriVoter('/about')]);
        $this->renderer = $this->createRenderer($matcher);

        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['uri' => '/about']);

        $rendered = '<ul><li class="current first last"><a href="/about">About</a></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu));
    }

    public function testRenderWithCurrentItemNotAsLink(): void
    {
        $menu = new MenuItem('test', new MenuFactory());
        $about = $menu->addChild('About', ['uri' => '/about']);
        $about->setCurrent(true);

        $rendered = '<ul><li class="current first last"><span>About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu, ['currentAsLink' => false]));
    }

    public function testRenderWithCurrentItemNotAsLinkUsingMatcherWithVoters(): void
    {
        $matcher = new Matcher([new UriVoter('/about')]);
        $this->renderer = $this->createRenderer($matcher);

        $menu = new MenuItem('test', new MenuFactory());
        $menu->addChild('About', ['uri' => '/about']);

        $rendered = '<ul><li class="current first last"><span>About</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($menu, ['currentAsLink' => false]));
    }

    public function testRenderSubMenuPortionWithClassAndTitle(): void
    {
        $this->pt2->setChildrenAttribute('class', 'parent2_class')->setChildrenAttribute('title', 'parent2 title');
        $rendered = '<ul class="parent2_class" title="parent2 title"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu['Parent 2']));
    }

    public function testDoNotShowChildrenRendersNothing(): void
    {
        $this->menu->setDisplayChildren(false);
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDoNotShowChildChildrenRendersPartialMenu(): void
    {
        $this->menu['Parent 1']->setDisplayChildren(false);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDoNotShowChildRendersPartialMenu(): void
    {
        $this->menu['Parent 1']->setDisplay(false);
        $rendered = '<ul class="root"><li class="first last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu));
    }

    public function testDepth0(): void
    {
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 0]));
    }

    public function testDepth1(): void
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 1]));
    }

    public function testDepth2(): void
    {
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 2]));
    }

    public function testDepth2WithNotShowChildChildren(): void
    {
        $this->menu['Parent 1']->setDisplayChildren(false);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 2]));
    }

    public function testEmptyUncompressed(): void
    {
        $rendered = '';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 0, 'compressed' => false]));
    }

    public function testMatchingDepth0(): void
    {
        $this->menu['Parent 1']['Child 1']->setCurrent(true);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 1, 'matchingDepth' => 1]));
    }

    public function testMatchingDepth1(): void
    {
        $this->menu['Parent 1']['Child 1']->setCurrent(true);
        $rendered = '<ul class="root"><li class="current_ancestor first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 1, 'matchingDepth' => 2]));
    }

    public function testMatchingDepth2(): void
    {
        $this->menu['Parent 1']['Child 1']->setCurrent(true);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 1, 'matchingDepth' => 0]));
    }

    public function testLeafAndBranchRendering(): void
    {
        $rendered = '<ul class="root"><li class="first branch"><span>Parent 1</span><ul class="menu_level_1"><li class="first leaf"><span>Child 1</span></li><li class="leaf"><span>Child 2</span></li><li class="last leaf"><span>Child 3</span></li></ul></li><li class="last branch"><span>Parent 2</span><ul class="menu_level_1"><li class="first last leaf"><span>Child 4</span></li></ul></li></ul>';

        $this->assertEquals($rendered, $this->renderer->render($this->menu, ['depth' => 2, 'leaf_class' => 'leaf', 'branch_class' => 'branch']));
    }
}
