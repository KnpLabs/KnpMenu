<?php

namespace Knp\Menu\Tests\Silex;

use Knp\Menu\Silex\RouterAwareFactory;

class RouterAwareFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!interface_exists('Symfony\Component\Routing\Generator\UrlGeneratorInterface')) {
            $this->markTestSkipped('The Symfony2 Routing component is not available');
        }
    }

    public function testCreateItemWithRoute()
    {
        $generator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $generator->expects($this->once())
            ->method('generate')
            ->with('homepage', array(), false)
            ->will($this->returnValue('/foobar'))
        ;

        $deprecatedErrorCatched = false;
        set_error_handler(function ($errorNumber, $message, $file, $line) use (&$deprecatedErrorCatched) {
            if ($errorNumber & E_USER_DEPRECATED) {
                $deprecatedErrorCatched = true;
                return true;
            }

            return \PHPUnit_Util_ErrorHandler::handleError($errorNumber, $message, $file, $line);
        });

        try {
            $factory = new RouterAwareFactory($generator);
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }

        restore_error_handler();

        $this->assertTrue($deprecatedErrorCatched, 'The RouterAwareFactory throws a E_USER_DEPRECATED when instantiating it.');

        $item = $factory->createItem('test_item', array('uri' => '/hello', 'route' => 'homepage'));
        $this->assertEquals('/foobar', $item->getUri());
    }
}
