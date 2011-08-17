<?php

namespace Knp\Menu\Tests;
use Knp\Menu\Menu;

class MenuTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateMenuWithEmptyParameter()
    {
        $menu = new Menu();
        $this->assertTrue($menu instanceof Menu);
    }

    public function testCreateMenuWithAttributes()
    {
        $menu = new Menu(array('class' => 'root'));
        $this->assertEquals('root', $menu->getAttribute('class'));
    }

    public function testCreateMenuWithItemClass()
    {
        $childClass = 'Knp\Menu\OtherMenuItem';
        $menu = new Menu(null, $childClass);
        $this->assertEquals($childClass, $menu->getChildClass());
    }
}
