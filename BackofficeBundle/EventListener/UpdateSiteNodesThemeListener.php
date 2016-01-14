<?php

namespace OpenOrchestra\BackofficeBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class UpdateSiteNodesThemeListener
 */
class UpdateSiteNodesThemeListener
{
    protected $nodeClass;

    protected $nodes = array();

    /**
     * @param string $nodeClass
     */
    public function __construct($nodeClass)
    {
        $this->nodeClass = $nodeClass;
    }

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $document = $event->getDocument();
        if ($document instanceof SiteInterface && $event->hasChangedField("theme")) {
            $siteTheme = $document->getTheme()->getName();

            /* @var $nodeRepository NodeRepositoryInterface */
            $nodeRepository = $event->getDocumentManager()->getRepository($this->nodeClass);
            $nodesToUpdate = $nodeRepository->findBySiteIdAndDefaultTheme($document->getSiteId());

            /* @var $node NodeInterface */
            foreach ($nodesToUpdate as $node) {
                $node->setTheme($siteTheme);
                $this->nodes[] = $node;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        if (! empty($this->nodes)) {
            $documentManager = $event->getDocumentManager();
            foreach ($this->nodes as $node) {
                $documentManager->persist($node);
            }
            $this->nodes = array();
            $documentManager->flush();
        }
    }
}
