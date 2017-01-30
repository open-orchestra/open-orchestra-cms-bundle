<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\SiteEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CreateMandatoryNodesSubscriber
 */
class CreateMandatoryNodesSubscriber implements EventSubscriberInterface
{
    const ROOT_NODE_PATTERN = '/';
    const ROOT_NAME = 'Homepage';

    protected $nodeManager;
    protected $objectManager;
    protected $translator;
    protected $statusRepository;

    /**
     * @param NodeManager                 $nodeManager
     * @param StatusRepositoryInterface   $statusRepository
     * @param ObjectManager               $objectManager
     * @param TranslatorInterface         $translator
     */
    public function __construct(
        NodeManager $nodeManager,
        StatusRepositoryInterface $statusRepository,
        ObjectManager $objectManager,
        TranslatorInterface $translator
    ){
        $this->nodeManager = $nodeManager;
        $this->statusRepository = $statusRepository;
        $this->objectManager = $objectManager;
        $this->translator = $translator;
    }

    /**
     * @param SiteEvent $siteEvent
     */
    public function createMandatoryNodes(SiteEvent $siteEvent)
    {
        $site = $siteEvent->getSite();
        if (null !== $site) {
            $languages = $site->getLanguages();
            $status = $this->statusRepository->findOneByTranslationState();
            foreach ($languages as $language) {
                $this->createRootNodeWithStatus($status, $language, $site);
                $this->createErrorNodeWithStatus($status, NodeInterface::ERROR_404_NODE_ID, $language, $site);
                $this->createErrorNodeWithStatus($status, NodeInterface::ERROR_503_NODE_ID, $language, $site);
            }

            $this->objectManager->flush();
        }
    }

    /**
     * @param StatusInterface $status
     * @param string          $nodeId
     * @param string          $language
     * @param SiteInterface   $site
     *
     * @return NodeInterface
     */
    protected function createErrorNodeWithStatus(StatusInterface $status, $nodeId, $language, SiteInterface $site)
    {
        $errorNode = $this->nodeManager->createNewErrorNode($nodeId, NodeInterface::ROOT_PARENT_ID, $site->getSiteId(), $language, $site->getTemplateNodeRoot());
        $this->createMandatoryNode($errorNode, $status);
    }


    /**
     * @param StatusInterface $status
     * @param string          $language
     * @param SiteInterface   $site
     *
     * @return NodeInterface
     */
    protected function createRootNodeWithStatus(StatusInterface $status, $language, SiteInterface $site)
    {
        $rootNode = $this->nodeManager->createRootNode($site->getSiteId(), $language, self::ROOT_NAME, self::ROOT_NODE_PATTERN, $site->getTemplateNodeRoot());
        $this->createMandatoryNode($rootNode, $status);
    }

    /**
     * @param NodeInterface $node
     * @param StatusInterface $status
     */
    protected function createMandatoryNode(NodeInterface $node, StatusInterface $status)
    {
        $node = $this->nodeManager->initializeAreasNode($node);
        $node->setStatus($status);
        $this->objectManager->persist($node);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::SITE_CREATE => 'createMandatoryNodes',
        );
    }
}
