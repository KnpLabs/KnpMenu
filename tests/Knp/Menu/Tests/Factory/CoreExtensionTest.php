<?php
/**
 * @author: bchoquet
 */

namespace Knp\Menu\Tests\Factory;


use Knp\Menu\Factory\CoreExtension;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;

class CoreExtensionTest extends \PHPUnit_Framework_TestCase
{

    public function testBuildItemsSetsExtras()
    {
        $item = $this->createItem( 'test' );
        $item->setExtra( 'test', 'original value' );
        $options = array(
            'extras' => array(
                'test1' => 'options value 1',
                'test2' => 'options value 2',
            )
        );

        $extension = $this->getExtension();
        $extension->buildItem( $item, $options );

        $extras = $item->getExtras();

        $this->assertEquals( 3, count( $extras ) );

        $this->assertArrayHasKey( 'test1', $extras );
        $this->assertEquals( 'options value 1', $item->getExtra( 'test1' ) );

        $this->assertArrayHasKey( 'test2', $extras );
        $this->assertEquals( 'options value 2', $item->getExtra( 'test2' ) );
    }

    public function testBuildItemDoesNotOverrideExistingExtras()
    {
        $item = $this->createItem( 'test' );
        $item->setExtra( 'test', 'original value' );
        $options = array(
            'extras' => array(
                'test' => 'options value',
            )
        );

        $extension = $this->getExtension();
        $extension->buildItem( $item, $options );

        $this->assertArrayHasKey( 'test', $item->getExtras() );
        $this->assertEquals( 'original value', $item->getExtra( 'test' ) );
    }

    private function getExtension()
    {
        return new CoreExtension();
    }

    private function createItem( $name )
    {
        $factory = new MenuFactory();
        $item = new MenuItem( $name, $factory );

        return $item;
    }
}
 