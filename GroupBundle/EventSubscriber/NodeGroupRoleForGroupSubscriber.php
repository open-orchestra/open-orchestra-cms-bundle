<?php

namespace OpenOrchestra\GroupBundle\EventSubscriber;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\DisplayBundle\Manager\TreeManager;

/**
 * Class NodeGroupRoleForGroupSubscriber
 */
class NodeGroupRoleForGroupSubscriber extends AbstractNodeGroupRoleListener
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
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof GroupInterface && ($site = $document->getSite()) instanceof SiteInterface) {
            $siteId = $site->getSiteId();
            $nodes = $this->container->get('open_orchestra_model.repository.node')->findLastVersionByType($siteId);
            $nodes = $this->treeManager->generateTree($nodes);
            $this->createNodeGroupRoleForTree($nodes, $document);
            $meta = $event->getDocumentManager()->getClassMetadata(get_class($document));
            $uow = $event->getDocumentManager()->getUnitOfWork();
            $uow->recomputeSingleDocumentChangeSet($meta, $document);
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
