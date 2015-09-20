<?php

namespace Knp\Menu\Provider;

@trigger_error('The '.__NAMESPACE__.'\PimpleProvider class is deprecated since version 2.1 and will be removed in 3.0. Use the '.__NAMESPACE__.'\ArrayAccessProvider class instead.', E_USER_DEPRECATED);

/**
 * Menu provider getting menus from a Pimple 1 container
 *
 * @deprecated use the ArrayAccessProvider instead.
 */
class PimpleProvider extends ArrayAccessProvider
{
    public function __construct(\Pimple $pimple, array $menuIds = array())
    {
        parent::__construct($pimple, $menuIds);
    }
}
