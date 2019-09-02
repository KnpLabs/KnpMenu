<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;

final class MenuFactoryTest extends TestCase
{
    public function testExtensions()
    {
        $factory = new MenuFactory();

        $extension1 = $this->getMockBuilder('Knp\Menu\Factory\ExtensionInterface')->getMock();
        $extension1->expects($this->once())
            ->method('buildOptions')
            ->with(['foo' => 'bar'])
            ->will($this->returnValue(['uri' => 'foobar']));
        $extension1->expects($this->once())
            ->method('buildItem')
            ->with($this->isInstanceOf('Knp\Menu\ItemInterface'), $this->contains('foobar'));

        $factory->addExtension($extension1);

        $extension2 = $this->getMockBuilder('Knp\Menu\Factory\ExtensionInterface')->getMock();
        $extension2->expects($this->once())
            ->method('buildOptions')
            ->with(['foo' => 'baz'])
            ->will($this->returnValue(['foo' => 'bar']));
        $extension1->expects($this->once())
            ->method('buildItem')
            ->with($this->isInstanceOf('Knp\Menu\ItemInterface'), $this->contains('foobar'));

        $factory->addExtension($extension2, 10);

        $item = $factory->createItem('test', ['foo' => 'baz']);
        $this->assertEquals('foobar', $item->getUri());
    }

    public function testCreateItem()
    {
        $factory = new MenuFactory();

        $item = $factory->createItem('test', [
            'uri' => 'http://example.com',
            'linkAttributes' => ['class' => 'foo'],
            'display' => false,
            'displayChildren' => false,
        ]);

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $item);
        $this->assertEquals('test', $item->getName());
        $this->assertFalse($item->isDisplayed());
        $this->assertFalse($item->getDisplayChildren());
        $this->assertEquals('foo', $item->getLinkAttribute('class'));
    }
}
