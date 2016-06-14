<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\Backoffice\Manager\RedirectionManager;
use OpenOrchestra\ModelInterface\SiteEvents;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class UpdateSiteAliasRedirectionSiteSubscriber
 */
class UpdateSiteAliasRedirectionSiteSubscriber implements EventSubscriberInterface
{
    protected $objectManager;
    protected $redirectionManager;
    protected $nodeRepository;

    /**
     * @param ObjectManager           $objectManager
     * @param RedirectionManager      $redirectionManager
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(ObjectManager $objectManager, RedirectionManager $redirectionManager, NodeRepositoryInterface $nodeRepository)
    {
        $this->objectManager = $objectManager;
        $this->redirectionManager = $redirectionManager;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param SiteEvent $event
     */
    public function updateSiteAliasesIndexOnSiteCreate(SiteEvent $event)
    {
        $this->updateSiteAliasesIndex($event);
    }

    /**
     * @param SiteEvent $event
     */
    public function updateRedirectionOnSiteUpdate(SiteEvent $event)
    {
        $this->updateSiteAliasesIndex($event);

        $site = $event->getSite();
        if ($site->getAliases()->toArray() !== $event->getOldAliases()->toArray()) {
            $siteId = $site->getSiteId();
            $nodes = $this->nodeRepository->findLastVersionByType($siteId);
            foreach ($nodes as $node) {
                $this->redirectionManager->generateRedirectionForNode($node);
            }
        }
    }

    /**
     * @param SiteEvent $event
     */
    public function deleteRedirectionOnSiteDelete(SiteEvent $event)
    {
        $site = $event->getSite();
        $siteId = $site->getSiteId();
        $nodes = $this->nodeRepository->findLastVersionByType($siteId);
        foreach ($nodes as $node) {
            $this->redirectionManager->deleteRedirection(
                $node->getNodeId(),
                $node->getLanguage()
            );
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::SITE_CREATE => 'updateSiteAliasesIndexOnSiteCreate',
            SiteEvents::SITE_UPDATE => 'updateRedirectionOnSiteUpdate',
            SiteEvents::SITE_DELETE => 'deleteRedirectionOnSiteDelete',
        );
    }

    /**
     * @param SiteEvent $event
     */
    protected function updateSiteAliasesIndex(SiteEvent $event)
    {
        $site = $event->getSite();
        $aliases = $site->getAliases();
        foreach ($aliases as $key => $alias) {
            if (strpos($key, SiteInterface::PREFIX_SITE_ALIAS) === false) {
                $site->removeAlias($alias);
                $site->addAlias($alias);
            }
        }
        $this->objectManager->persist($site);
        $this->objectManager->flush($site);
    }
}
