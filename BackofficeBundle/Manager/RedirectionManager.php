<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface;

/**
 * Class RedirectionManager
 */
class RedirectionManager
{
    protected $redirectionClass;
    protected $documentManager;
    protected $eventDispatcher;
    protected $contextManager;
    protected $siteRepository;
    protected $redirectionRepository;

    /**
     * @param string                         $redirectionClass
     * @param CurrentSiteIdInterface         $contextManager
     * @param DocumentManager                $documentManager
     * @param EventDispatcherInterface       $eventDispatcher
     * @param SiteRepositoryInterface        $siteRepository
     * @param RedirectionRepositoryInterface $siteRepository
     */
    public function __construct(
        $redirectionClass,
        CurrentSiteIdInterface $contextManager,
        DocumentManager $documentManager,
        EventDispatcherInterface $eventDispatcher,
        SiteRepositoryInterface $siteRepository,
        RedirectionRepositoryInterface $redirectionRepository
    ){
        $this->redirectionClass = $redirectionClass;
        $this->contextManager = $contextManager;
        $this->documentManager = $documentManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->siteRepository = $siteRepository;
        $this->redirectionRepository = $redirectionRepository;
    }

    /**
     * @param string $pattern
     * @param string $nodeId
     * @param int    $version
     * @param string $language
     */
    public function createRedirection($pattern, $nodeId, $language)
    {
        $redirectionClass = $this->redirectionClass;
        $site = $this->siteRepository->findOneBySiteId($this->contextManager->getCurrentSiteId());
        /** @var SiteAliasInterface $alias */
        foreach ($site->getAliases() as $alias) {
            if ($language == $alias->getLanguage()) {
                /** @var RedirectionInterface $redirection */
                $redirection = new $redirectionClass();
                $redirection->setNodeId($nodeId);
                $redirection->setLocale($language);
                $redirection->setRoutePattern(str_replace('//', '/', '/' . $alias->getPrefix() . '/' . $pattern));
                $redirection->setSiteId($site->getSiteId());
                $redirection->setSiteName($site->getName());
                $this->documentManager->persist($redirection);
                $this->documentManager->flush($redirection);
                $this->eventDispatcher->dispatch(RedirectionEvents::REDIRECTION_CREATE, new RedirectionEvent($redirection));
            }
        }
    }

    /**
     * @param string $nodeId
     * @param int    $version
     * @param string $language
     */
    public function deleteRedirection($nodeId, $language)
    {
        $redirections = $this->redirectionRepository->findByNode($nodeId, $language);
        if (is_array($redirections)) {
            foreach ($redirections as $redirection) {
                $this->documentManager->remove($redirection);
                $this->documentManager->flush($redirection);
                $this->eventDispatcher->dispatch(RedirectionEvents::REDIRECTION_DELETE, new RedirectionEvent($redirection));
            }
        }
    }
}
