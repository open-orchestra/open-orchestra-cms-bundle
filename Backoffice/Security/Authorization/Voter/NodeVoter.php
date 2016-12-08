<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodeVoter
 *
 * Voter checking rights on node management
 */
class NodeVoter extends AbstractNodeVoter
{
    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array('OpenOrchestra\ModelInterface\Model\NodeInterface');
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getPath($node)
    {
        return $node->getPath();
    }
}
