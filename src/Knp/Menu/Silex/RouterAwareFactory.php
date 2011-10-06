<?php

namespace Knp\Menu\Silex;

use Knp\Menu\MenuFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Factory able to use the Symfony2 Routing component to build the url
 */
class RouterAwareFactory extends MenuFactory implements IsSameRouteInterface
{
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function createItem($name, array $options = array())
    {
        if (!empty($options['route'])) {
            $params = isset($options['routeParameters']) ? $options['routeParameters'] : array();
            $absolute = isset($options['routeAbsolute']) ? $options['routeAbsolute'] : false;
            $options['uri'] = $this->generator->generate($options['route'], $params, $absolute);
        }

        return parent::createItem($name, $options);
    }
    public function isSameRoute($urla, $urlb){
    	$basePath = $this->generator->getContext()->getBaseUrl();
    	if (!empty($basePath) && 0 === strpos($urla, $basePath)) {
    		$urla = substr($urla, strlen($basePath));
    	}
    	$aRoute = $this->generator->match($urla);
    	if (!empty($basePath) && 0 === strpos($urlb, $basePath)) {
    		$urlb = substr($urlb, strlen($basePath));
    	}
    	$bRoute = $this->generator->match($urlb);
    	
    	return null !== $urla && ($aRoute['_route'] === $bRoute['_route']);
    }
}
