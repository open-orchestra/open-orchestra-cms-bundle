<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class AbstractEditionRoleController
 */
abstract class AbstractEditionRoleController extends AbstractAdminController
{
    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getAccessRole(NodeInterface $node)
    {
        return ContributionActionInterface::READ;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getEditionRole(NodeInterface $node)
    {
        return ContributionActionInterface::EDIT;
    }
}
