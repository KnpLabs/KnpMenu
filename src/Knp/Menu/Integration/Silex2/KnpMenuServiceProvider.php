<?php

namespace Knp\Menu\Integration\Silex2;

use Knp\Menu\Integration\Symfony\RoutingExtension;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
use Knp\Menu\Matcher\Voter\RouteVoter;
use Knp\Menu\MenuFactory;
use Knp\Menu\Provider\ArrayAccessProvider as PimpleMenuProvider;
use Knp\Menu\Renderer\ArrayAccessProvider as PimpleRendererProvider;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\TwigRenderer;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Twig\MenuExtension;
use Knp\Menu\Util\MenuManipulator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class KnpMenuServiceProvider implements ServiceProviderInterface {

    public function register(Container $app) {
        $app['knp_menu.factory'] = function($app) {
            $factory = new MenuFactory();

            if ( isset($app['url_generator']) ) {
                $factory->addExtension(new RoutingExtension( $app['url_generator'] ));
            }

            return $factory;
        };

        $app['knp_menu.matcher'] = function($app) {
            $matcher = new Matcher();

            if ( is_callable($app['knp_menu.matcher.configure']) ) {
                $app['knp_menu.matcher.configure']($matcher);
            }

            return $matcher;
        };

        if ( !isset($app['knp_menu.matcher.configure']) ) {
            $app['knp_menu.matcher.configure'] = $app->protect(function($matcher) use ($app) {
                $matcher->addVoter(new UriVoter($_SERVER['REQUEST_URI']));
                $matcher->addVoter(new RouteVoter( $app['request_stack']->getCurrentRequest() ));
            });
        }

        $app['knp_menu.menu_provider'] = function($app) {
            return new PimpleMenuProvider($app, $app['knp_menu.menus']);
        };

        if ( !isset($app['knp_menu.menus']) ) {
            $app['knp_menu.menus'] = array();
        }

        $app['knp_menu.renderer.list'] = function($app) {
            return new ListRenderer($app['knp_menu.matcher'], array(), $app['charset']);
        };

        $app['knp_menu.renderer_provider'] = function($app) {
            $app['knp_menu.renderers'] = array_merge(
                array('list' => 'knp_menu.renderer.list'),
                isset($app['knp_menu.renderer.twig']) ? array('twig' => 'knp_menu.renderer.twig') : array(),
                isset($app['knp_menu.renderers']) ? $app['knp_menu.renderers'] : array()
            );

            return new PimpleRendererProvider($app, $app['knp_menu.default_renderer'], $app['knp_menu.renderers']);
        };

        if ( !isset($app['knp_menu.default_renderer']) ) {
            $app['knp_menu.default_renderer'] = 'list';
        }

        $app['knp_menu.menu_manipulator'] = function($app) {
            return new MenuManipulator();
        };

        $app['knp_menu.helper'] = function($app) {
            return new Helper($app['knp_menu.renderer_provider'], $app['knp_menu.menu_provider'], $app['knp_menu.menu_manipulator'], $app['knp_menu.matcher']);
        };

        if ( isset($app['twig']) ) {
            $app['knp_menu.twig_extension'] = function($app) {
                return new MenuExtension($app['knp_menu.helper'], $app['knp_menu.matcher'], $app['knp_menu.menu_manipulator']);
            };

            $app['knp_menu.renderer.twig'] = function($app) {
                return new TwigRenderer($app['twig'], $app['knp_menu.template'], $app['knp_menu.matcher']);
            };

            if ( !isset($app['knp_menu.template']) ) {
                $app['knp_menu.template'] = 'knp_menu.html.twig';
            }

            $app->extend('twig', function($twig) use ($app) {
                $twig->addExtension($app['knp_menu.twig_extension']);
                return $twig;
            });

            $app->extend('twig.loader.filesystem', function(\Twig_Loader_Filesystem $loader) use($app) {
                $loader->addPath(__DIR__.'/../../Resources/views');
                return $loader;
            });
        };
    }

}