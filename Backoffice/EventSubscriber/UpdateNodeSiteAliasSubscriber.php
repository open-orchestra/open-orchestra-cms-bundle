<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\SiteEvents;
use OpenOrchestra\ModelInterface\Event\SiteEvent;

use Doctrine\ODM\MongoDB\PersistentCollection;

/**
 * Class UpdateNodeSiteAliasSubscriber
 */
class UpdateNodeSiteAliasSubscriber implements EventSubscriberInterface
{
    protected $nodeManager;
    protected $nodeRepository;
    protected $objectManager;

    /**
     * @param NodeManager             $nodeManager
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(
        NodeManager $nodeManager,
        NodeRepositoryInterface $nodeRepository,
        ObjectManager $objectManager
    ) {
        $this->nodeManager = $nodeManager;
        $this->nodeRepository = $nodeRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param SiteEvent $event
     */
    public function updateNodeOnSiteAliasUpdate(SiteEvent $event)
    {
        $languages = array();
        foreach ($event->getOldAliases() as $alias) {
            $languages[] = $alias->getLanguage();
        }

        $languageReference = current($languages);
        $languages = array_diff($event->getSite()->getLanguages(), $languages);

        if (count($languages) > 0) {
            $nodes = $this->nodeRepository->findLastVersionByLanguage($event->getSite()->getSiteId(), $languageReference);

            foreach ($languages as $language) {
                foreach ($nodes as $node) {
                    $this->objectManager->persist($this->nodeManager->createNewLanguageNode($node, $language));
                }
            }

            $this->objectManager->flush();
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::SITE_UPDATE => 'updateNodeOnSiteAliasUpdate',
        );
    }
}
