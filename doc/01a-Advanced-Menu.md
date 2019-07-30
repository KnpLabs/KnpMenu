Advanced Menu
-------------

You can create a menu easily from a Tree structure (a nested set for example) by
making it implement `Knp\Menu\NodeInterface`. You will then be able
to create the menu easily, like in the following example. 

```php
<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Loader\NodeLoader;

class Builder
{
    private $factory;

    public function __construct(FactoryInterface $factory)
    {   
        $this->factory = $factory;
    }   

    public function createMenu(): ItemInterface
    {   
        $loader = new NodeLoader($this->factory);
        $rootNode = /* ... get an object implementing \Knp\Menu\NodeInterface */;
        $menu = $loader->load($rootNode);

        return $menu;
    } 
}
```

