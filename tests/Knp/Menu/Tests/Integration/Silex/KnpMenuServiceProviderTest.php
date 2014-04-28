<?php

namespace Knp\Menu\Tests\Integration\Silex;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Integration\Silex\KnpMenuServiceProvider;
use Knp\Menu\Matcher\Voter\RouteVoter;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class KnpMenuServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Silex\Application')) {
            $this->markTestSkipped('Silex is not available');
        }
    }

    public function testFactoryWithoutRouter()
    {
        $app = new Application();
        $app->register(new KnpMenuServiceProvider());

        $this->assertEquals('Knp\Menu\MenuFactory', get_class($app['knp_menu.factory']));
    }

    public function testTwigRendererNotRegistered()
    {
        $app = new Application();
        $app->register(new KnpMenuServiceProvider());

        $this->assertFalse(isset($app['knp_menu.renderer.twig']));
    }

    public function testTwigRendererRegistered()
    {
        $app = new Application();
        $app->register(new TwigServiceProvider());
        $app->register(new KnpMenuServiceProvider());

        $this->assertTrue(isset($app['knp_menu.renderer.twig']));
    }

    public function testRenderNotCurrentWithList()
    {
        $app = $this->bootstrapApp();

        $request = Request::create('/list');
        $response = $app->handle($request);
        $this->assertEquals('<ul class="nav"><li class="first"><a href="/twig">Home</a></li><li class="last"><a href="http://knplabs.com">KnpLabs</a></li></ul>', $response->getContent());
    }

    public function testRenderCurrentWithTwig()
    {
        $app = $this->bootstrapApp();

        $request = Request::create('/twig');
        $response = $app->handle($request);
        $this->assertEquals('<ul class="nav"><li class="current first"><a href="/twig">Home</a></li><li class="last"><a href="http://knplabs.com">KnpLabs</a></li></ul>', $response->getContent());

        $app = $this->bootstrapApp();

        $request = Request::create('/other-twig');
        $response = $app->handle($request);
        $this->assertEquals('<ul class="nav"><li class="first"><a href="/twig">Home</a></li><li class="current last"><a href="http://knplabs.com">KnpLabs</a></li></ul>', $response->getContent());
    }

    private function bootstrapApp()
    {
        $app = new Application();
        $app['debug'] = true;
        $app->register(new TwigServiceProvider(), array(
            'twig.templates' => array('main' => '{{ knp_menu_render("my_menu", {"compressed": true}, renderer) }}'),
        ));
        $app->register(new KnpMenuServiceProvider(), array(
            'knp_menu.menus' => array('my_menu' => 'test.menu.my'),
        ));
        $app->register(new UrlGeneratorServiceProvider());

        $app['test.menu.my'] = function (Application $app) {
            /** @var $factory \Knp\Menu\FactoryInterface */
            $factory = $app['knp_menu.factory'];

            $root = $factory->createItem('root', array('childrenAttributes' => array('class' => 'nav')));
            $root->addChild('home', array('route' => 'homepage', 'label' => 'Home'));
            $root->addChild('KnpLabs', array('uri' => 'http://knplabs.com', 'extras' => array('routes' => 'other_route')));

            return $root;
        };

        $app['test.voter'] = $app->share(function (Application $app) {
            $voter = new RouteVoter();
            $voter->setRequest($app['request']);

            return $voter;
        });

        $app['knp_menu.matcher.configure'] = $app->protect(function (Matcher $matcher) use ($app) {
            $matcher->addVoter($app['test.voter']);
        });

        $app->get('/twig', function (Application $app) {
            return $app['twig']->render('main', array('renderer' => 'twig'));
        })->bind('homepage');

        $app->get('/other-twig', function (Application $app) {
            return $app['twig']->render('main', array('renderer' => 'twig'));
        })->bind('other_route');

        $app->get('/list', function (Application $app) {
            return $app['twig']->render('main', array('renderer' => 'list'));
        })->bind('list');

        return $app;
    }
}
