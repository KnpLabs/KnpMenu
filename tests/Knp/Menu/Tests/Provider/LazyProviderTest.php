<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\LazyProvider;
use PHPUnit\Framework\TestCase;

final class LazyProviderTest extends TestCase
{
    public function testHas()
    {
        $provider = new LazyProvider(['first' => function () {}, 'second' => function () {}]);
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $provider = new LazyProvider(['default' => function () use ($menu) {
            return $menu;
        }]);
        $this->assertSame($menu, $provider->get('default'));
    }

    public function testGetMenuAsClosure()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $provider = new LazyProvider(['default' => [function () use ($menu) {
            return new FakeBuilder($menu);
        }, 'build']]);

        $this->assertSame($menu, $provider->get('default', ['foo' => 'bar']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new LazyProvider([]);
        $provider->get('non-existent');
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetWithBrokenBuilder()
    {
        $provider = new LazyProvider(['broken' => new \stdClass()]);
        $provider->get('broken');
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetWithBrokenLazyBuilder()
    {
        $provider = new LazyProvider(['broken' => [function () {return new \stdClass();}, 'nonExistentMethod']]);
        $provider->get('broken');
    }
}

final class FakeBuilder
{
    private $menu;

    public function __construct(ItemInterface $menu)
    {
        $this->menu = $menu;
    }

    public function build(array $options)
    {
        return $this->menu;
    }
}
