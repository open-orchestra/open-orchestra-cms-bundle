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
     * @ODM\Field(type="hash")
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
}
