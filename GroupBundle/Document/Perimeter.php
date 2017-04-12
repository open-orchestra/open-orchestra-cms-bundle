<?php

namespace OpenOrchestra\GroupBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;

/**
 * @ODM\EmbeddedDocument
 */
class Perimeter implements PerimeterInterface
{
    /**
     * @var string $type
     *
     * @ODM\Field(
     *  type="string"
     * )
     */
    protected $type;

    /**
     * @var array $items
     *
     * @ODM\Field(
     *  type="collection"
     * )
     */
    protected $items;

    /**
     * Constructor
     *
     * @param string $type
     */
    public function __construct($type = '')
    {
        $this->setType($type);
        $this->items = array();
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if (is_string($type)) {
            $this->type = $type;
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $item
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * @param array $items
     */
    public function addItems(array $items)
    {
        $this->items = array_unique (array_merge ($this->items, $items));;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
