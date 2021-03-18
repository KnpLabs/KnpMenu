<?php

namespace Knp\Menu\Tests;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;

final class MenuItemGetterSetterTest extends TestCase
{
    public function testCreateMenuItemWithEmptyParameter(): void
    {
        $menu = $this->createMenu();
        self::assertInstanceOf(MenuItem::class, $menu);
    }

    public function testCreateMenuWithNameAndUri(): void
    {
        $menu = $this->createMenu('test1', 'other_uri');
        $this->assertEquals('test1', $menu->getName());
        $this->assertEquals('other_uri', $menu->getUri());
    }

    public function testCreateMenuWithTitle(): void
    {
        $title = 'This is a test item title';
        $menu = $this->createMenu('', null, ['title' => $title]);
        $this->assertEquals($title, $menu->getAttribute('title'));
    }

    public function testName(): void
    {
        $menu = $this->createMenu();
        $menu->setName('menu name');
        $this->assertEquals('menu name', $menu->getName());
    }

    public function testLabel(): void
    {
        $menu = $this->createMenu();
        $menu->setLabel('menu label');
        $this->assertEquals('menu label', $menu->getLabel());
    }

    public function testNameIsUsedAsDefaultLabel(): void
    {
        $menu = $this->createMenu('My Label');
        $this->assertEquals('My Label', $menu->getLabel());
        $menu->setLabel('Other Label');
        $this->assertEquals('Other Label', $menu->getLabel());
    }

    public function testUri(): void
    {
        $menu = $this->createMenu();
        $menu->setUri('menu_uri');
        $this->assertEquals('menu_uri', $menu->getUri());
    }

    public function testAttributes(): void
    {
        $attributes = ['class' => 'test_class', 'title' => 'Test title'];
        $menu = $this->createMenu();
        $menu->setAttributes($attributes);
        $this->assertEquals($attributes, $menu->getAttributes());
    }

    public function testDefaultAttribute(): void
    {
        $menu = $this->createMenu('', null, ['id' => 'test_id']);
        $this->assertEquals('test_id', $menu->getAttribute('id'));
        $this->assertEquals('default_value', $menu->getAttribute('unknown_attribute', 'default_value'));
    }

    public function testLinkAttributes(): void
    {
        $attributes = ['class' => 'test_class', 'title' => 'Test title'];
        $menu = $this->createMenu();
        $menu->setLinkAttributes($attributes);
        $this->assertEquals($attributes, $menu->getLinkAttributes());
    }

    public function testDefaultLinkAttribute(): void
    {
        $menu = $this->createMenu();
        $menu->setLinkAttribute('class', 'test_class');
        $this->assertEquals('test_class', $menu->getLinkAttribute('class'));
        $this->assertNull($menu->getLinkAttribute('title'));
        $this->assertEquals('foobar', $menu->getLinkAttribute('title', 'foobar'));
    }

    public function testChildrenAttributes(): void
    {
        $attributes = ['class' => 'test_class', 'title' => 'Test title'];
        $menu = $this->createMenu();
        $menu->setChildrenAttributes($attributes);
        $this->assertEquals($attributes, $menu->getChildrenAttributes());
    }

    public function testDefaultChildrenAttribute(): void
    {
        $menu = $this->createMenu();
        $menu->setChildrenAttribute('class', 'test_class');
        $this->assertEquals('test_class', $menu->getChildrenAttribute('class'));
        $this->assertNull($menu->getChildrenAttribute('title'));
        $this->assertEquals('foobar', $menu->getChildrenAttribute('title', 'foobar'));
    }

    public function testLabelAttributes(): void
    {
        $attributes = ['class' => 'test_class', 'title' => 'Test title'];
        $menu = $this->createMenu();
        $menu->setLabelAttributes($attributes);
        $this->assertEquals($attributes, $menu->getLabelAttributes());
    }

    public function testDefaultLabelAttribute(): void
    {
        $menu = $this->createMenu();
        $menu->setLabelAttribute('class', 'test_class');
        $this->assertEquals('test_class', $menu->getLabelAttribute('class'));
        $this->assertNull($menu->getLabelAttribute('title'));
        $this->assertEquals('foobar', $menu->getLabelAttribute('title', 'foobar'));
    }

    public function testExtras(): void
    {
        $extras = ['class' => 'test_class', 'title' => 'Test title'];
        $menu = $this->createMenu();
        $menu->setExtras($extras);
        $this->assertEquals($extras, $menu->getExtras());
    }

    public function testDefaultExtras(): void
    {
        $menu = $this->createMenu();
        $menu->setExtra('class', 'test_class');
        $this->assertEquals('test_class', $menu->getExtra('class'));
        $this->assertNull($menu->getExtra('title'));
        $this->assertEquals('foobar', $menu->getExtra('title', 'foobar'));
    }

    public function testDisplay(): void
    {
        $menu = $this->createMenu();
        $this->assertTrue($menu->isDisplayed());
        $menu->setDisplay(false);
        $this->assertFalse($menu->isDisplayed());
    }

    public function testShowChildren(): void
    {
        $menu = $this->createMenu();
        $this->assertTrue($menu->getDisplayChildren());
        $menu->setDisplayChildren(false);
        $this->assertFalse($menu->getDisplayChildren());
    }

    public function testParent(): void
    {
        $menu = $this->createMenu();
        $child = $this->createMenu('child_menu');
        $this->assertNull($child->getParent());
        $child->setParent($menu);
        $this->assertEquals($menu, $child->getParent());
    }

    public function testChildren(): void
    {
        $menu = $this->createMenu();
        $child = $this->createMenu('child_menu');
        $menu->setChildren([$child]);
        $this->assertEquals([$child], $menu->getChildren());
    }

    public function testSetExistingNameThrowsAnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $menu = $this->createMenu();
        $menu->addChild('jack');
        $menu->addChild('joe');
        $menu->getChild('joe')->setName('jack');
    }

    public function testSetSameName(): void
    {
        $parent = $this->getMockBuilder(ItemInterface::class)->getMock();
        $parent->expects($this->never())
            ->method('offsetExists');

        $menu = $this->createMenu('my_name');
        $menu->setParent($parent);
        $menu->setName('my_name');
        $this->assertEquals('my_name', $menu->getName());
    }

    public function testFactory(): void
    {
        $child1 = $this->getMockBuilder(ItemInterface::class)->getMock();
        $factory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $factory->expects($this->once())
            ->method('createItem')
            ->willReturn($child1);

        $menu = $this->createMenu();
        $menu->setFactory($factory);

        $menu->addChild('child1');
    }

    /**
     * Create a new MenuItem
     *
     * @param array<string, mixed> $attributes
     */
    protected function createMenu(string $name = 'test_menu', ?string $uri = 'homepage', array $attributes = []): ItemInterface
    {
        $factory = new MenuFactory();

        return $factory->createItem($name, ['attributes' => $attributes, 'uri' => $uri]);
    }
}
