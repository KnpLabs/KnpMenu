<?php

namespace Knp\Menu\Tests\Util;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Tests\MenuTestCase;
use Knp\Menu\Util\MenuManipulator;

final class MenuManipulatorTest extends MenuTestCase
{
    public function testMoveToFirstPosition(): void
    {
        $menu = new MenuItem('root', new MenuFactory());
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $manipulator = new MenuManipulator();
        $manipulator->moveToFirstPosition($menu->getChildren()['c3']);
        $this->assertEquals(['c3', 'c1', 'c2', 'c4'], \array_keys($menu->getChildren()));
    }

    public function testMoveToLastPosition(): void
    {
        $menu = new MenuItem('root', new MenuFactory());
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $manipulator = new MenuManipulator();
        $manipulator->moveToLastPosition($menu->getChildren()['c2']);
        $this->assertEquals(['c1', 'c3', 'c4', 'c2'], \array_keys($menu->getChildren()));
    }

    public function testMoveToPosition(): void
    {
        $menu = new MenuItem('root', new MenuFactory());
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $manipulator = new MenuManipulator();
        $c1 = $menu['c1'];
        $this->assertNotNull($c1);
        $manipulator->moveToPosition($c1, 2);
        $this->assertEquals(['c2', 'c3', 'c1', 'c4'], \array_keys($menu->getChildren()));
    }

    /**
     * @param int|string           $offset
     * @param int|string|null      $length
     * @param array<string, mixed> $keys
     *
     * @dataProvider getSliceData
     */
    public function testSlice($offset, $length, int $count, array $keys): void
    {
        $manipulator = new MenuManipulator();
        $pt1 = $this->pt1;
        $this->assertNotNull($pt1);
        $sliced = $manipulator->slice($pt1, $offset, $length);
        $this->assertCount($count, $sliced);
        $this->assertEquals($keys, \array_keys($sliced->getChildren()));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function getSliceData(): array
    {
        $this->setUp();

        $ch1 = $this->ch1;
        $this->assertNotNull($ch1);
        $ch2 = $this->ch2;
        $this->assertNotNull($ch2);
        $ch3 = $this->ch3;
        $this->assertNotNull($ch3);

        return [
            'numeric offset and numeric length' => [0, 2, 2, [$ch1->getName(), $ch2->getName()]],
            'numeric offset and no length' => [0, null, 3, [$ch1->getName(), $ch2->getName(), $ch3->getName()]],
            'named offset and no length' => ['Child 2', null, 2, [$ch2->getName(), $ch3->getName()]],
            'child offset and no length' => [$ch3, null, 1, [$ch3->getName()]],
            'numeric offset and named length' => [0, 'Child 3', 2, [$ch1->getName(), $ch2->getName()]],
            'numeric offset and child length' => [0, $ch3, 2, [$ch1->getName(), $ch2->getName()]],
        ];
    }

    /**
     * @param int|string           $length
     * @param array<string, mixed> $keys
     *
     * @dataProvider getSplitData
     */
    public function testSplit($length, int $count, array $keys): void
    {
        $manipulator = new MenuManipulator();
        $pt1 = $this->pt1;
        $this->assertNotNull($pt1);
        $split = $manipulator->split($pt1, $length);
        $this->assertArrayHasKey('primary', $split);
        $this->assertArrayHasKey('secondary', $split);
        $this->assertCount($count, $split['primary']);
        $this->assertCount(3 - $count, $split['secondary']);
        $this->assertEquals($keys, \array_keys($split['primary']->getChildren()));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function getSplitData(): array
    {
        $this->setUp();

        $ch1 = $this->ch1;
        $this->assertNotNull($ch1);
        $ch2 = $this->ch2;
        $this->assertNotNull($ch2);
        $ch3 = $this->ch3;
        $this->assertNotNull($ch3);

        return [
            'numeric length' => [1, 1, [$ch1->getName()]],
            'named length' => ['Child 3', 2, [$ch1->getName(), $ch2->getName()]],
            'child length' => [$ch3, 2, [$ch1->getName(), $ch2->getName()]],
        ];
    }

    public function testPathAsString(): void
    {
        $manipulator = new MenuManipulator();
        $ch4 = $this->ch4;
        $this->assertNotNull($ch4);
        $ch2 = $this->ch2;
        $this->assertNotNull($ch2);
        $this->assertEquals('Root li > Parent 2 > Child 4', $manipulator->getPathAsString($ch4), 'Path with default separator');
        $this->assertEquals('Root li / Parent 1 / Child 2', $manipulator->getPathAsString($ch2, ' / '), 'Path with custom separator');
    }

    public function testBreadcrumbsArray(): void
    {
        $manipulator = new MenuManipulator();
        $menu = $this->menu;
        $this->assertNotNull($menu);
        $menu->addChild('child', ['uri' => 'http://www.symfony-reloaded.org']);

        $pt1 = $this->pt1;
        $this->assertNotNull($pt1);
        $child = $menu['child'];
        $this->assertNotNull($child);

        $this->assertEquals(
            [['label' => 'Root li', 'uri' => null, 'item' => $menu], ['label' => 'Parent 1', 'uri' => null, 'item' => $pt1]],
            $manipulator->getBreadcrumbsArray($pt1)
        );
        $this->assertEquals(
            [['label' => 'Root li', 'uri' => null, 'item' => $menu], ['label' => 'child', 'uri' => 'http://www.symfony-reloaded.org', 'item' => $child]],
            $manipulator->getBreadcrumbsArray($child)
        );
        $this->assertEquals(
            [
                ['label' => 'Root li', 'uri' => null, 'item' => $menu],
                ['label' => 'child', 'uri' => 'http://www.symfony-reloaded.org', 'item' => $child],
                ['label' => 'subitem1', 'uri' => null, 'item' => null],
            ],
            $manipulator->getBreadcrumbsArray($child, 'subitem1')
        );

        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->method('getLabel')->willReturn('mock');
        $item->method('getUri')->willReturn('foo');

        $this->assertEquals(
            [
                ['label' => 'Root li', 'uri' => null, 'item' => $menu],
                ['label' => 'child', 'uri' => 'http://www.symfony-reloaded.org', 'item' => $child],
                ['label' => 'subitem1', 'uri' => null, 'item' => null],
                ['label' => 'subitem2', 'uri' => null, 'item' => null],
                ['label' => 'subitem3', 'uri' => 'http://php.net', 'item' => null],
                ['label' => 'subitem4', 'uri' => null, 'item' => null],
                ['label' => 'mock', 'uri' => 'foo', 'item' => $item],
            ],
            $manipulator->getBreadcrumbsArray($child, [
                'subitem1',
                'subitem2' => null,
                'subitem3' => 'http://php.net',
                ['label' => 'subitem4', 'uri' => null, 'item' => null],
                $item,
            ])
        );
    }

    public function testBreadcrumbsArrayInvalidData(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $manipulator = new MenuManipulator();
        $pt1 = $this->pt1;
        $this->assertNotNull($pt1);
        $manipulator->getBreadcrumbsArray($pt1, [new \stdClass()]);
    }

    public function testCallRecursively(): void
    {
        $factory = new MenuFactory();

        $menu = $factory->createItem('test_menu');

        foreach (\range(1, 2) as $i) {
            $child = $this->getMockBuilder(ItemInterface::class)->getMock();
            $child->expects($this->any())
                ->method('getName')
                ->willReturn('Child '.$i)
            ;
            $child->expects($this->once())
                ->method('setDisplay')
                ->with(false)
            ;
            $child->expects($this->once())
                ->method('getChildren')
                ->willReturn([])
            ;
            $menu->addChild($child);
        }

        $manipulator = new MenuManipulator();

        $manipulator->callRecursively($menu, 'setDisplay', [false]);
        $this->assertFalse($menu->isDisplayed());
    }

    public function testToArrayWithChildren(): void
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', ['uri' => 'http://php.net', 'linkAttributes' => ['title' => 'php'], 'display' => false])
            ->addChild('john', ['current' => true])->setCurrent(true)
        ;
        $menu->addChild('joe', [
            'attributes' => ['class' => 'leaf'],
            'label' => 'test',
            'labelAttributes' => ['class' => 'center'],
            'displayChildren' => false,
        ])->setCurrent(false);

        $manipulator = new MenuManipulator();

        $this->assertEquals(
            [
                'name' => 'test_menu',
                'label' => 'test_menu',
                'uri' => 'homepage',
                'attributes' => [],
                'labelAttributes' => [],
                'linkAttributes' => [],
                'childrenAttributes' => [],
                'extras' => [],
                'display' => true,
                'displayChildren' => true,
                'current' => null,
                'children' => [
                    'jack' => [
                        'name' => 'jack',
                        'label' => 'jack',
                        'uri' => 'http://php.net',
                        'attributes' => [],
                        'labelAttributes' => [],
                        'linkAttributes' => ['title' => 'php'],
                        'childrenAttributes' => [],
                        'extras' => [],
                        'display' => false,
                        'displayChildren' => true,
                        'current' => null,
                        'children' => [
                            'john' => [
                                'name' => 'john',
                                'label' => 'john',
                                'uri' => null,
                                'attributes' => [],
                                'labelAttributes' => [],
                                'linkAttributes' => [],
                                'childrenAttributes' => [],
                                'extras' => [],
                                'display' => true,
                                'displayChildren' => true,
                                'children' => [],
                                'current' => true,
                            ],
                        ],
                    ],
                    'joe' => [
                        'name' => 'joe',
                        'label' => 'test',
                        'uri' => null,
                        'attributes' => ['class' => 'leaf'],
                        'labelAttributes' => ['class' => 'center'],
                        'linkAttributes' => [],
                        'childrenAttributes' => [],
                        'extras' => [],
                        'display' => true,
                        'displayChildren' => false,
                        'children' => [],
                        'current' => false,
                    ],
                ],
            ],
            $manipulator->toArray($menu)
        );
    }

    public function testToArrayWithLimitedChildren(): void
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', ['uri' => 'http://php.net', 'linkAttributes' => ['title' => 'php'], 'display' => false])
            ->addChild('john')
        ;
        $menu->addChild('joe', ['attributes' => ['class' => 'leaf'], 'label' => 'test', 'labelAttributes' => ['class' => 'center'], 'displayChildren' => false]);

        $manipulator = new MenuManipulator();

        $this->assertEquals(
            [
                'name' => 'test_menu',
                'label' => 'test_menu',
                'uri' => 'homepage',
                'attributes' => [],
                'labelAttributes' => [],
                'linkAttributes' => [],
                'childrenAttributes' => [],
                'extras' => [],
                'display' => true,
                'displayChildren' => true,
                'current' => null,
                'children' => [
                    'jack' => [
                        'name' => 'jack',
                        'label' => 'jack',
                        'uri' => 'http://php.net',
                        'attributes' => [],
                        'labelAttributes' => [],
                        'linkAttributes' => ['title' => 'php'],
                        'childrenAttributes' => [],
                        'extras' => [],
                        'display' => false,
                        'displayChildren' => true,
                        'current' => null,
                    ],
                    'joe' => [
                        'name' => 'joe',
                        'label' => 'test',
                        'uri' => null,
                        'attributes' => ['class' => 'leaf'],
                        'labelAttributes' => ['class' => 'center'],
                        'linkAttributes' => [],
                        'childrenAttributes' => [],
                        'extras' => [],
                        'display' => true,
                        'displayChildren' => false,
                        'current' => null,
                    ],
                ],
            ],
            $manipulator->toArray($menu, 1)
        );
    }

    public function testToArrayWithoutChildren(): void
    {
        $menu = $this->createMenu();
        $menu->addChild('jack', ['uri' => 'http://php.net', 'linkAttributes' => ['title' => 'php'], 'display' => false]);
        $menu->addChild('joe', ['attributes' => ['class' => 'leaf'], 'label' => 'test', 'labelAttributes' => ['class' => 'center'], 'displayChildren' => false]);

        $manipulator = new MenuManipulator();

        $this->assertEquals(
            [
                'name' => 'test_menu',
                'label' => 'test_menu',
                'uri' => 'homepage',
                'attributes' => [],
                'labelAttributes' => [],
                'linkAttributes' => [],
                'childrenAttributes' => [],
                'extras' => [],
                'display' => true,
                'displayChildren' => true,
                'current' => null,
            ],
            $manipulator->toArray($menu, 0)
        );
    }

    /**
     * Create a new MenuItem.
     *
     * @param array<string, mixed> $attributes
     */
    private function createMenu(string $name = 'test_menu', string $uri = 'homepage', array $attributes = []): ItemInterface
    {
        $factory = new MenuFactory();

        return $factory->createItem($name, ['attributes' => $attributes, 'uri' => $uri]);
    }
}
