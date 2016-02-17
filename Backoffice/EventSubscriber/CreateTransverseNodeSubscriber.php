<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\BackofficeBundle\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\SiteEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CreateTransverseNodeSubscriber
 */
class CreateTransverseNodeSubscriber implements EventSubscriberInterface
{
    protected $nodeManager;
    protected $objectManager;
    protected $nodeRepository;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param NodeManager             $nodeManager
     * @param ObjectManager           $objectManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, NodeManager $nodeManager, ObjectManager $objectManager)
    {
        $this->nodeManager = $nodeManager;
        $this->objectManager = $objectManager;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param SiteEvent $event
     */
    public function onSiteCreation(SiteEvent $event)
    {
        $siteId = $event->getSite()->getSiteId();

        foreach ($event->getSite()->getLanguages() as $language) {
            $node =  $this->nodeManager->createTransverseNode($language, $siteId);
            $this->objectManager->persist($node);
            $this->objectManager->flush($node);
        }
    }

    /**
     * @param SiteEvent $event
     */
    public function onSiteUpdate(SiteEvent $event)
    {
        $nodes = $this->nodeRepository->findByNodeTypeAndSite(NodeInterface::TYPE_TRANSVERSE, $event->getSite()->getSiteId());

        $nodeLanguages = $this->extractLanguages($nodes);

        foreach ($event->getSite()->getLanguages() as $language) {
            if (in_array($language, $nodeLanguages)) {
                continue;
            }
            $newNode = $this->nodeManager->createNewLanguageNode(end($nodes), $language);
            $this->objectManager->persist($newNode);
            $this->objectManager->flush($newNode);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::SITE_CREATE => 'onSiteCreation',
            SiteEvents::SITE_UPDATE => 'onSiteUpdate',
        );
    }

    /**
     * @param array $nodes
     *
     * @return array
     */
    protected function extractLanguages(array $nodes)
    {
        $languages = array();
        /** @var NodeInterface $node */
        foreach ($nodes as $node) {
            $languages[] = $node->getLanguage();
        }

        return $languages;
    }
}
