<?php

namespace OpenOrchestra\GroupBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class AddNodeGroupRoleForNodeListener
 */
class AddNodeGroupRoleForNodeListener extends AbstractNodeGroupRoleListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof NodeInterface) {
            $accessType = $this->getNodeAccessType($document);
            $siteId = $document->getSiteId();
            $groups = $this->container->get('open_orchestra_user.repository.group')->findAllWithSite();
            $nodesRoles = $this->getNodeRoles();
            /** @var GroupInterface $group */
            foreach ($groups as $group) {
                if ($siteId === $group->getSite()->getSiteId()) {
                    foreach ($nodesRoles as $role => $translation) {
                        $nodeGroupRole = $this->createNodeGroupRole($document, $group, $role, $accessType);
                        $group->addNodeRole($nodeGroupRole);
                        $event->getDocumentManager()->persist($group);
                        $event->getDocumentManager()->flush($group);
                    }
                }
            }
        }
    }
}
