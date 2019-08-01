<?php

namespace Knp\Menu\Renderer;

use Knp\Menu\ItemInterface;

abstract class BaseRenderer implements RendererInterface
{
    protected $defaultOptions;

    public function __construct($defaultOptions)
    {
        $this->defaultOptions = array_merge(array(
            'depth' => null,
            'matchingDepth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'compressed' => false,
            'allow_safe_labels' => false,
            'clear_matcher' => true,
            'leaf_class' => null,
            'branch_class' => null,
        ), $defaultOptions);
    }

    public abstract function render(ItemInterface $item, array $options = array());
}
