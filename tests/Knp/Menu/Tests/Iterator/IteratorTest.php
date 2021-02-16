<?php

namespace Knp\Menu\Tests\Iterator;

use Knp\Menu\ItemInterface;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Knp\Menu\Tests\MenuTestCase;

final class IteratorTest extends MenuTestCase
{
    public function testIterator(): void
    {
        $count = 0;
        foreach ($this->pt1 as $key => $value) {
            ++$count;
            $this->assertEquals('Child '.$count, $key);
            $this->assertEquals('Child '.$count, $value->getLabel());
        }
    }

    public function testRecursiveIterator(): void
    {
        // Adding an item which does not provide a RecursiveIterator to be sure it works properly.
        $child = $this->getMockBuilder(ItemInterface::class)->getMock();
        $child->expects($this->any())
            ->method('getName')
            ->willReturn('Foo');
        $child->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \EmptyIterator());
        $this->menu->addChild($child);

        $names = [];
        foreach (new \RecursiveIteratorIterator(new RecursiveItemIterator($this->menu), \RecursiveIteratorIterator::SELF_FIRST) as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Parent 1', 'Child 1', 'Child 2', 'Child 3', 'Parent 2', 'Child 4', 'Grandchild 1', 'Foo'], $names);
    }

    public function testRecursiveIteratorLeavesOnly(): void
    {
        $names = [];
        foreach (new \RecursiveIteratorIterator(new RecursiveItemIterator($this->menu), \RecursiveIteratorIterator::LEAVES_ONLY) as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Child 1', 'Child 2', 'Child 3', 'Grandchild 1'], $names);
    }

    public function testFullTreeIterator(): void
    {
        $fullTreeIterator = new \RecursiveIteratorIterator(
            new RecursiveItemIterator(new \ArrayIterator([$this->menu])), // recursive iterator containing the root item
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $names = [];
        foreach ($fullTreeIterator as $value) {
            $names[] = $value->getName();
        }

        $this->assertEquals(['Root li', 'Parent 1', 'Child 1', 'Child 2', 'Child 3', 'Parent 2', 'Child 4', 'Grandchild 1'], $names);
    }
}
