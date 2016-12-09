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
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        return $this->supportedClasses(
            $subject,
            array('OpenOrchestra\ModelInterface\Model\NodeInterface')
        );
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
