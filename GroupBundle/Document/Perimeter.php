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
     * @var array $paths
     *
     * @ODM\Field(
     *  type="hash"
     * )
     */
    protected $paths;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->paths = array();
    }

    /**
     * @param string $path
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
    }

    /**
     * Check if a path is contained in the perimeter
     *
     * @param string $path
     *
     * @return boolean
     */
    public function contains($path)
    {
        if (is_string($path)) {
            return array_search($path, $this->paths) ? true : false;
        }

        return false;
    }
}
