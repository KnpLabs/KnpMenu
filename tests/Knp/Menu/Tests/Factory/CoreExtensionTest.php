<?php

namespace Knp\Menu\Tests\Factory;

use Knp\Menu\Factory\CoreExtension;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;

final class CoreExtensionTest extends TestCase
{
    public function testBuildOptions()
    {
        $extension = $this->getExtension();
        $item = $this->createItem('test');

        $options = $extension->buildOptions([]);

        $this->assertArrayHasKey('uri', $options);
        $this->assertArrayHasKey('label', $options);
        $this->assertArrayHasKey('attributes', $options);
        $this->assertArrayHasKey('linkAttributes', $options);
        $this->assertArrayHasKey('childrenAttributes', $options);
        $this->assertArrayHasKey('labelAttributes', $options);
        $this->assertArrayHasKey('extras', $options);
        $this->assertArrayHasKey('current', $options);
        $this->assertArrayHasKey('display', $options);
        $this->assertArrayHasKey('displayChildren', $options);
    }

    public function testBuildItemsSetsExtras()
    {
        $item = $this->createItem('test');
        $item->setExtra('test1', 'original value');
        $extension = $this->getExtension();
        $options = $extension->buildOptions(
            [
                'extras' => [
                    'test1' => 'options value 1',
                    'test2' => 'options value 2',
                ],
            ]
        );

        $extension->buildItem($item, $options);

        $extras = $item->getExtras();

        $this->assertCount(2, $extras);

        $this->assertArrayHasKey('test1', $extras);
        $this->assertEquals('options value 1', $item->getExtra('test1'));

        $this->assertArrayHasKey('test2', $extras);
        $this->assertEquals('options value 2', $item->getExtra('test2'));
    }

    public function testBuildItemDoesNotOverrideExistingExtras()
    {
        $item = $this->createItem('test');
        $item->setExtra('test1', 'original value');
        $extension = $this->getExtension();
        $options = $extension->buildOptions(
            [
                'extras' => [
                    'test2' => 'options value',
                ],
            ]
        );

        $extension->buildItem($item, $options);

        $this->assertArrayHasKey('test1', $item->getExtras());
        $this->assertEquals('original value', $item->getExtra('test1'));
    }

    private function getExtension()
    {
        return new CoreExtension();
    }

    private function createItem($name)
    {
        $factory = new MenuFactory();
        $item = new MenuItem($name, $factory);

        return $item;
    }
}
