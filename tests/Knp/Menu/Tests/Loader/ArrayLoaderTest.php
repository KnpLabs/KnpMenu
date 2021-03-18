<?php

namespace Knp\Menu\Tests\Loader;

use Knp\Menu\Loader\ArrayLoader;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;

final class ArrayLoaderTest extends TestCase
{
    public function testLoadWithoutChildren(): void
    {
        $array = [
            'name' => 'joe',
            'uri' => '/foobar',
            'display' => false,
        ];

        $loader = new ArrayLoader(new MenuFactory());
        $item = $loader->load($array);

        $this->assertEquals('joe', $item->getName());
        $this->assertEquals('/foobar', $item->getUri());
        $this->assertFalse($item->isDisplayed());
        $this->assertEmpty($item->getAttributes());
        $this->assertEmpty($item->getChildren());
    }

    public function testLoadWithChildren(): void
    {
        $array = [
            'name' => 'joe',
            'children' => [
                'jack' => [
                    'name' => 'jack',
                    'label' => 'Jack',
                ],
                [
                    'name' => 'john',
                ],
            ],
        ];

        $loader = new ArrayLoader(new MenuFactory());
        $item = $loader->load($array);

        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertCount(2, $item);
        $this->assertTrue(isset($item['john']));
    }

    public function testLoadWithChildrenOmittingName(): void
    {
        $array = [
            'name' => 'joe',
            'children' => [
                'jack' => [
                    'label' => 'Jack',
                ],
                'john' => [
                    'label' => 'John',
                ],
            ],
        ];

        $loader = new ArrayLoader(new MenuFactory());
        $item = $loader->load($array);

        $this->assertEquals('joe', $item->getName());
        $this->assertEmpty($item->getAttributes());
        $this->assertCount(2, $item);
        $this->assertTrue(isset($item['john']));
        $this->assertTrue(isset($item['jack']));
    }

    public function testLoadInvalidData(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $loader = new ArrayLoader(new MenuFactory());

        $loader->load(new \stdClass());
    }

    /**
     * @param mixed $data
     *
     * @dataProvider provideSupportingData
     */
    public function testSupports($data, bool $expected): void
    {
        $loader = new ArrayLoader(new MenuFactory());

        $this->assertSame($expected, $loader->supports($data));
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public function provideSupportingData(): array
    {
        return [
            [[], true],
            [null, false],
            ['foobar', false],
            [new \stdClass(), false],
            [53, false],
            [true, false],
        ];
    }
}
