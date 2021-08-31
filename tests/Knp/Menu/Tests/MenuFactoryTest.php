<?php

namespace Knp\Menu\Tests;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;

final class MenuFactoryTest extends TestCase
{
    public function testExtensions(): void
    {
        $factory = new MenuFactory();

        $extension1 = $this->getMockBuilder(ExtensionInterface::class)->getMock();
        $extension1->expects($this->once())
            ->method('buildOptions')
            ->with(['foo' => 'bar'])
            ->willReturn(['uri' => 'foobar']);
        $extension1->expects($this->once())
            ->method('buildItem')
            ->with($this->isInstanceOf(ItemInterface::class), $this->containsCustom('foobar'));

        $factory->addExtension($extension1);

        $extension2 = $this->getMockBuilder(ExtensionInterface::class)->getMock();
        $extension2->expects($this->once())
            ->method('buildOptions')
            ->with(['foo' => 'baz'])
            ->willReturn(['foo' => 'bar']);
        $extension1->expects($this->once())
            ->method('buildItem')
            ->with($this->isInstanceOf(ItemInterface::class), $this->containsCustom('foobar'));

        $factory->addExtension($extension2, 10);

        $item = $factory->createItem('test', ['foo' => 'baz']);
        $this->assertEquals('foobar', $item->getUri());
    }

    public function testCreateItem(): void
    {
        $factory = new MenuFactory();

        $item = $factory->createItem('test', [
            'uri' => 'http://example.com',
            'linkAttributes' => ['class' => 'foo'],
            'display' => false,
            'displayChildren' => false,
        ]);

        $this->assertInstanceOf(ItemInterface::class, $item);
        $this->assertEquals('test', $item->getName());
        $this->assertFalse($item->isDisplayed());
        $this->assertFalse($item->getDisplayChildren());
        $this->assertEquals('foo', $item->getLinkAttribute('class'));
    }

    private function containsCustom(string $value): ?object
    {
        if (\method_exists($this, 'contains')) {
            return $this->contains($value);
        }

        if (\method_exists($this, 'containsEqual')) {
            return $this->containsEqual($value);
        }

        return null;
    }
}
