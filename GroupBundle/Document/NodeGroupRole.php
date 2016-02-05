<?php

namespace OpenOrchestra\GroupBundle\Document;

use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class NodeGroupRole
 *
 * @ODM\EmbeddedDocument
 */
class NodeGroupRole extends AbstractGroupRole implements NodeGroupRoleInterface
{
    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $nodeId;

    /**
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }
}
