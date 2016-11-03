<?php

namespace OpenOrchestra\GroupBundle\Document;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;

/**
 * @ODM\EmbeddedDocument
 */
class Perimeter implements PerimeterInterface
{
    /**
     * @var Collection $paths
     *
     * @ODM\Field(type="hash")
     */
    protected $paths;

    /**
     * @param string $path
     */
    public function addPath($path)
    {
        $this->paths->add($path);
    }

    /**
     * @param string $path
     */
    public function removePath($path)
    {
        $this->paths->removeElement($path);
    }
}
