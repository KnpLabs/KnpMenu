<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\Provider\PimpleProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group legacy
 */
class PimpleProviderTest extends TestCase
{
    protected function setUp()
    {
        if (!class_exists('Pimple')) {
            $this->markTestSkipped('Pimple is not available');
        }
    }

    public function testHas()
    {
        $provider = new PimpleProvider(new \Pimple(), ['first' => 'first', 'second' => 'dummy']);
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu()
    {
        $pimple = new \Pimple();
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $pimple['menu'] = function() use ($menu) {
            return $menu;
        };
        $provider = new PimpleProvider($pimple, ['default' => 'menu']);
        $this->assertSame($menu, $provider->get('default'));
    }

    public function testGetMenuAsClosure()
    {
        $pimple = new \Pimple();
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $pimple['menu'] = $pimple->protect(function($options, $c) use ($menu) {
            $c['options'] = $options;

            return $menu;
        });
        $provider = new PimpleProvider($pimple, ['default' => 'menu']);

        $this->assertSame($menu, $provider->get('default', ['foo' => 'bar']));
        $this->assertEquals(['foo' => 'bar'], $pimple['options']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new PimpleProvider(new \Pimple());
        $provider->get('non-existent');
    }
}
