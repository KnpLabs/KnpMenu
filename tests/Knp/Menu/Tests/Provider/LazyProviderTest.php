<?php

namespace Knp\Menu\Tests\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\LazyProvider;
use PHPUnit\Framework\TestCase;

final class LazyProviderTest extends TestCase
{
    public function testHas(): void
    {
        $provider = new LazyProvider(['first' => static function (): void {}, 'second' => static function (): void {}]);
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $provider = new LazyProvider(['default' => function () use ($menu) {
            return $menu;
        }]);
        $this->assertSame($menu, $provider->get('default'));
    }

    public function testGetMenuAsClosure(): void
    {
        $menu = $this->getMockBuilder(ItemInterface::class)->getMock();
        $provider = new LazyProvider(['default' => [function () use ($menu) {
            return new FakeBuilder($menu);
        }, 'build']]);

        $this->assertSame($menu, $provider->get('default', ['foo' => 'bar']));
    }

    public function testGetNonExistentMenu(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $provider = new LazyProvider([]);
        $provider->get('non-existent');
    }

    public function testGetWithBrokenBuilder(): void
    {
        $this->expectException(\LogicException::class);

        $provider = new LazyProvider(['broken' => new \stdClass()]);
        $provider->get('broken');
    }

    public function testGetWithBrokenLazyBuilder(): void
    {
        $this->expectException(\LogicException::class);

        $provider = new LazyProvider(['broken' => [function () {return new \stdClass(); }, 'nonExistentMethod']]);
        $provider->get('broken');
    }
}

final class FakeBuilder
{
    /**
     * @var ItemInterface
     */
    private $menu;

    public function __construct(ItemInterface $menu)
    {
        $this->menu = $menu;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function build(array $options): ItemInterface
    {
        return $this->menu;
    }
}
