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
     * @param string $path
     */
    public function removePath($path);
}
