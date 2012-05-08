<?php

namespace Knp\Menu\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\TwigRenderer;
use Knp\Menu\Provider\PimpleProvider as PimpleMenuProvider;
use Knp\Menu\Renderer\PimpleProvider as PimpleRendererProvider;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Twig\MenuExtension;

class KnpMenuServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['knp_menu.factory'] = $app->share(function () use ($app) {
            if (isset($app['url_generator'])) {
                return new RouterAwareFactory($app['url_generator']);
            }

            return new MenuFactory();
        });

        $app['knp_menu.renderer.list'] = $app->share(function () use ($app) {
            return new ListRenderer(array(), $app['charset']);
        });

        $app['knp_menu.menu_provider'] = $app->share(function () use ($app) {
            return new PimpleMenuProvider($app, $app['knp_menu.menus']);
        });

        if (!isset($app['knp_menu.menus'])) {
            $app['knp_menu.menus'] = array();
        }

        $app['knp_menu.renderer_provider'] = $app->share(function () use ($app) {
            $app['knp_menu.renderers'] = array_merge(
                array('list' => 'knp_menu.renderer.list'),
                isset($app['knp_menu.renderer.twig']) ? array('twig' => 'knp_menu.renderer.twig') : array(),
                isset($app['knp_menu.renderers']) ? $app['knp_menu.renderers'] : array()
            );

            return new PimpleRendererProvider($app, $app['knp_menu.default_renderer'], $app['knp_menu.renderers']);
        });

        if (!isset($app['knp_menu.default_renderer'])) {
            $app['knp_menu.default_renderer'] = 'list';
        }

        $app['knp_menu.helper'] = $app->share(function () use ($app){
            return new Helper($app['knp_menu.renderer_provider'], $app['knp_menu.menu_provider']);
        });

        if (isset($app['twig'])) {
            $app['knp_menu.twig_extension'] = $app->share(function () use ($app) {
                return new MenuExtension($app['knp_menu.helper']);
            });

            $app['knp_menu.renderer.twig'] = $app->share(function () use ($app) {
                return new TwigRenderer($app['twig'], $app['knp_menu.template']);
            });

            if (!isset($app['knp_menu.template'])) {
                $app['knp_menu.template'] = 'knp_menu.html.twig';
            }

            $app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig) use ($app) {
                $twig->addExtension($app['knp_menu.twig_extension']);

                return $twig;
            }));

            $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function (\Twig_Loader_Filesystem $loader) use ($app) {
                $loader->addPath(__DIR__.'/../Resources/views');

                return $loader;
            }));
        }
    }
}
