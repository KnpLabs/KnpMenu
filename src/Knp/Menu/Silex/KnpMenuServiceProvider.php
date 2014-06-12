<?php

namespace Knp\Menu\Silex;

use Knp\Menu\Integration\Silex\KnpMenuServiceProvider as BaseKnpMenuServiceProvider;

class KnpMenuServiceProvider extends BaseKnpMenuServiceProvider
{
    public function __construct()
    {
        trigger_error(
            __CLASS__ . ' is deprecated because of namespace, use Knp\Menu\Integration\Silex\KnpMenuServerProvider instead.',
            E_USER_DEPRECATED
        );
    }
}
