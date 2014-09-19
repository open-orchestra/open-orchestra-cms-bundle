<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Document\Area;

/**
 * Class AreaManager
 */
class AreaManager
{
    /**
     * Remove a block reference from an area
     * 
     * @param Area $area
     * @param int  $blockPosition
     *
     * @return Area
     */
    public function removeBlockFromArea(Area $area, $blockPosition)
    {
        $blocks = $area->getBlocks();

        if (is_array($blocks) && isset($blocks[$blockPosition])) {
            unset($blocks[$blockPosition]);
            $area->setBlocks($blocks);
        }

        return $area;
    }
}
