<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use OpenOrchestra\ModelInterface\SiteEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CreateRootNodeSubscriber
 */
class CreateRootNodeSubscriber implements EventSubscriberInterface
{
    protected $nodeManager;
    protected $repositoryTemplate;
    protected $objectManager;
    protected $translator;

    /**
     * @param NodeManager                 $nodeManager
     * @param TemplateRepositoryInterface $repositoryTemplate
     * @param ObjectManager               $objectManager
     * @param TranslatorInterface         $translator
     */
    public function __construct(
        NodeManager $nodeManager,
        TemplateRepositoryInterface $repositoryTemplate,
        ObjectManager $objectManager,
        TranslatorInterface $translator
    )
    {
        $this->nodeManager = $nodeManager;
        $this->repositoryTemplate = $repositoryTemplate;
        $this->objectManager = $objectManager;
        $this->translator = $translator;
    }

    /**
     * @param SiteEvent $siteEvent
     */
    public function createRootNode(SiteEvent $siteEvent)
    {
        $templateId = $siteEvent->getTemplateIdNodeHome();
        $site = $siteEvent->getSite();
        if (null !== $templateId && null !== $site) {
            $language = $site->getDefaultLanguage();
            $template = $this->repositoryTemplate->findOneByTemplateId($templateId);
            $name = $this->translator->trans('open_orchestra_backoffice.node.root_name');
            $routePattern = '/';

            $node = $this->nodeManager->createRootNode($site->getSiteId(), $language, $name, $routePattern, $template);
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
