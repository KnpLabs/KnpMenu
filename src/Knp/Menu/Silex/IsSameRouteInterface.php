<?php

namespace Knp\Menu\Silex;


interface IsSameRouteInterface{
	/**
	 * Checks if Url is Current Url
	 * @param string $url
	 * @return boolean
	 */
	public function isSameRoute($urla, $urlb);
}