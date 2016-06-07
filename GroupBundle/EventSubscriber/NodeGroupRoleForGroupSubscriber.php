<?php

namespace OpenOrchestra\GroupBundle\EventSubscriber;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\DisplayBundle\Manager\TreeManager;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class NodeGroupRoleForGroupSubscriber
 */
class NodeGroupRoleForGroupSubscriber extends AbstractNodeGroupRoleSubscriber
{
    protected $treeManager;

    /**
     * @param string      $nodeGroupRoleClass
     * @param TreeManager $treeManager
     */
    public function __construct($nodeGroupRoleClass, TreeManager $treeManager)
    {
        parent::__construct($nodeGroupRoleClass);
        $this->treeManager = $treeManager;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof GroupInterface && ($site = $document->getSite()) instanceof SiteInterface) {
            $siteId = $site->getSiteId();
            $nodes = $this->container->get('open_orchestra_model.repository.node')->findLastVersionByType($siteId);
            $nodes = $this->treeManager->generateTree($nodes);
            $this->createNodeGroupRoleForTree($nodes, $document);
        }
    }

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $group = $event->getDocument();
        if ($group instanceof GroupInterface &&
           ($site = $group->getSite()) instanceof SiteInterface &&
            $event->hasChangedField('site')
        ) {
            $siteId = $site->getSiteId();
            $group->setModelGroupRoles(new ArrayCollection());
            $nodes = $this->container->get('open_orchestra_model.repository.node')->findLastVersionByType($siteId);
            $nodes = $this->treeManager->generateTree($nodes);
            $this->createNodeGroupRoleForTree($nodes, $group);
            $meta = $event->getDocumentManager()->getClassMetadata(get_class($group));
            $uow = $event->getDocumentManager()->getUnitOfWork();
            $uow->recomputeSingleDocumentChangeSet($meta, $group);
        }
    }

    /**
     * @param array          $nodes
     * @param GroupInterface $group
     */
    protected function createNodeGroupRoleForTree(array $nodes, GroupInterface $group)
    {
        $nodesRoles = $this->getNodeRoles();
        foreach ($nodes as $element) {
            $node = $element['node'];
            $accessType = $this->getNodeAccessType($node);
            foreach ($nodesRoles as $role => $translation) {
                $nodeGroupRole = $this->createNodeGroupRole($node, $group, $role, $accessType);
                $group->addModelGroupRole($nodeGroupRole);
            }
            if (array_key_exists('child', $element) && ! empty($element['child'])) {
                $this->createNodeGroupRoleForTree($element['child'], $group);
            }
        }
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }
}
