<?php

namespace OpenOrchestra\BackofficeBundle\EventListener;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\Form\FormEvent;

/**
 * Class NodeThemeSelectionListener
 */
class NodeThemeSelectionListener
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
            $data->setThemeSiteDefault(true);
        } else {
            $data->setThemeSiteDefault(false);
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
            if ($document->isThemeSiteDefault()) {
                $document->setTheme(NodeInterface::THEME_DEFAULT);
            }
        }
    }
}
