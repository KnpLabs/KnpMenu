<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;

class MenuFactoryTest extends TestCase
{
    public function testExtensions()
    {
        $factory = new MenuFactory();

        $extension1 = $this->getMockBuilder('Knp\Menu\Factory\ExtensionInterface')->getMock();
        $extension1->expects($this->once())
            ->method('buildOptions')
            ->with(array('foo' => 'bar'))
            ->will($this->returnValue(array('uri' => 'foobar')));
        $extension1->expects($this->once())
            ->method('buildItem')
            ->with($this->isInstanceOf('Knp\Menu\ItemInterface'), $this->contains('foobar'));

        $factory->addExtension($extension1);

        $extension2 = $this->getMockBuilder('Knp\Menu\Factory\ExtensionInterface')->getMock();
        $extension2->expects($this->once())
            ->method('buildOptions')
            ->with(array('foo' => 'baz'))
            ->will($this->returnValue(array('foo' => 'bar')));
        $extension1->expects($this->once())
            ->method('buildItem')
            ->with($this->isInstanceOf('Knp\Menu\ItemInterface'), $this->contains('foobar'));

        $factory->addExtension($extension2, 10);

        $item = $factory->createItem('test', array('foo' => 'baz'));
        $this->assertEquals('foobar', $item->getUri());
    }

    public function testCreateItem()
    {
        $factory = new MenuFactory();

        $item = $factory->createItem('test', array(
            'uri' => 'http://example.com',
            'linkAttributes' => array('class' => 'foo'),
            'display' => false,
            'displayChildren' => false,
        ));

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $item);
        $this->assertEquals('test', $item->getName());
        $this->assertFalse($item->isDisplayed());
        $this->assertFalse($item->getDisplayChildren());
        $this->assertEquals('foo', $item->getLinkAttribute('class'));
    }
}
