<?php

namespace Knp\Menu\Renderer;

use \Knp\Menu\ItemInterface;

interface RendererInterface
{
  /**
   * Renders menu tree.
   *
   * Depth values corresppond to:
   *   * 0 - no children displayed at all (would return a blank string)
   *   * 1 - directly children only
   *   * 2 - children and grandchildren
   *
   * @param \Knp\Menu\ItemInterface    $item         Menu item
   * @param integer     $depth        The depth of children to render
   *
   * @return string
   */
  public function render(ItemInterface $item, $depth = null);
}
