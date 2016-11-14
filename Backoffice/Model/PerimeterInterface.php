<?php

namespace OpenOrchestra\Backoffice\Model;

/**
 * Interface PerimeterInterface
 */
interface PerimeterInterface
{
    /**
     * @param string $path
     */
    public function addPath($path);

    /**
     * Check if a path is contained in the perimeter
     *
     * @param string $path
     *
     * @return boolean
     */
    public function contains($path);
}
