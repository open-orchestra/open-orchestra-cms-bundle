<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\SiteEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CreateRootNodeSubscriber
 */
class CreateRootNodeSubscriber implements EventSubscriberInterface
{
    const ROOT_NODE_PATTERN = '/';

    protected $nodeManager;
    protected $objectManager;
    protected $translator;

    /**
     * @param NodeManager                 $nodeManager
     * @param ObjectManager               $objectManager
     * @param TranslatorInterface         $translator
     */
    public function __construct(
        NodeManager $nodeManager,
        ObjectManager $objectManager,
        TranslatorInterface $translator
    ){
        $this->nodeManager = $nodeManager;
        $this->objectManager = $objectManager;
        $this->translator = $translator;
    }

    /**
     * @param SiteEvent $siteEvent
     */
    public function createRootNode(SiteEvent $siteEvent)
    {
        $site = $siteEvent->getSite();
        if (null !== $site) {
            $language = $site->getDefaultLanguage();
            $name = $this->translator->trans('open_orchestra_backoffice.node.root_name');

            $node = $this->nodeManager->createRootNode($site->getSiteId(), $language, $name, self::ROOT_NODE_PATTERN, $site->getTemplateRoot());
            $this->objectManager->persist($node);
            $this->objectManager->flush();
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::SITE_CREATE => 'createRootNode',
        );
    }
}
