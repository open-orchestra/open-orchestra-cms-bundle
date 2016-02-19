<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class SiteSubscriber
 */
class SiteSubscriber implements EventSubscriberInterface
{
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
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        if (!is_null($data) && $data['siteId'] != '') {
            $form->add('siteAlias', 'choice', array(
                'label' => 'open_orchestra_backoffice.form.internal_link.site_alias',
                'attr' => array(
                    'class' => 'to-tinyMce',
                    'data-key' => 'site-alias'
                ),
                'choices' => $this->getChoices($data['siteId']),
                'required' => false,
            ));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param string $siteId
     */
    protected function getChoices($siteId)
    {
        $choices = array();
        $site = $this->siteRepository->findOneBySiteId($siteId);
        $aliases = $site->getAliases();
        foreach ($aliases as $alias) {
            $choices[$alias->getDomain()] = $alias->getDomain();
        }

        return $choices;
    }
}
