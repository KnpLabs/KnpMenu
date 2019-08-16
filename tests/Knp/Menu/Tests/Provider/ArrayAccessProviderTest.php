<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\Provider\ArrayAccessProvider;
use PHPUnit\Framework\TestCase;

class ArrayAccessProviderTest extends TestCase
{
    public function testHas(): void
    {
        $provider = new ArrayAccessProvider(new \ArrayObject(), ['first' => 'first', 'second' => 'dummy']);
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu(): void
    {
        $registry = new \ArrayObject();
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $registry['menu'] = $menu;
        $provider = new ArrayAccessProvider($registry, ['default' => 'menu']);
        $this->assertSame($menu, $provider->get('default'));
    }

    public function testGetMenuAsClosure(): void
    {
        $registry = new \ArrayObject();
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $registry['menu'] = function ($options, $c) use ($menu) {
            $c['options'] = $options;

            return $menu;
        };
        $provider = new ArrayAccessProvider($registry, ['default' => 'menu']);

        $this->assertSame($menu, $provider->get('default', ['foo' => 'bar']));
        $this->assertEquals(['foo' => 'bar'], $registry['options']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentMenu(): void
    {
        $provider = new ArrayAccessProvider(new \ArrayObject());
        $provider->get('non-existent');
    }
}
