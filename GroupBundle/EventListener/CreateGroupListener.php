<?php

namespace OpenOrchestra\GroupBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class CreateGroupListener
 */
class CreateGroupListener extends AbstractNodeGroupRoleListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof GroupInterface && ($site = $document->getSite()) instanceof SiteInterface) {
            $siteId = $site->getSiteId();
            $nodes = $this->container->get('open_orchestra_model.repository.node')->findLastVersionByType($siteId);
            $nodesRoles = $this->getNodeRoles();
            foreach ($nodes as $node) {
                $accessType = $this->getNodeAccessType($node);
                foreach ($nodesRoles as $role) {
                    $nodeGroupRole = $this->createNodeGroupRole($node->getNodeId(), $role, $accessType);
                    $document->addNodeRole($nodeGroupRole);
                }
            }
        }
    }
}
