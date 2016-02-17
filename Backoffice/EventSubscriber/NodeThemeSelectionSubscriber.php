<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class NodeThemeSelectionSubscriber
 */
class NodeThemeSelectionSubscriber implements EventSubscriberInterface
{
    /**
     * @var SiteRepositoryInterface
     */
    protected $siteRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        /* @var $data NodeInterface */
        $data = $event->getData();

        if (NodeInterface::THEME_DEFAULT === $data->getTheme()) {
            $siteId = $data->getSiteId();
            /* @var $site SiteInterface */
            $site = $this->siteRepository->findOneBySiteId($siteId);
            $theme = $site->getTheme()->getName();

            $data->setTheme($theme);
            $data->setDefaultSiteTheme(true);
        } else {
            $data->setDefaultSiteTheme(false);
        }
        $event->setData($data);
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /* @var $document NodeInterface */
        $document = $event->getData();
        if ($document instanceof NodeInterface) {
            if ($document->hasDefaultSiteTheme()) {
                $document->setTheme(NodeInterface::THEME_DEFAULT);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'submit',
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }
}
