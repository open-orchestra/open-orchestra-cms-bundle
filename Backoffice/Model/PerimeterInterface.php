<?php

namespace OpenOrchestra\Backoffice\Model;

/**
 * Interface PerimeterInterface
 */
interface PerimeterInterface
{
    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $item
     */
    public function addItem($item);

    /**
     * @return array
     */
    public function getItems();
}
