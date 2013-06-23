<?php

namespace Knp\Menu\Tests;

use Knp\Menu\MenuFactory;

class MenuFactoryTest extends \PHPUnit_Framework_TestCase
{
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
