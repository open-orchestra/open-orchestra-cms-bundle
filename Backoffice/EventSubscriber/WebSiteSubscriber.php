<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class WebSiteSubscriber
 */
class WebSiteSubscriber implements EventSubscriberInterface
{
    protected $session;

    /**
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $this->session->remove(ContextBackOfficeInterface::KEY_SITE);
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $options = array(
            'label' => 'open_orchestra_backoffice.form.website.site_id',
            'attr' => array('class' => 'generate-id-dest'),
            'group_id' => 'information',
            'sub_group_id' => 'property',
        );
        if (null !== $data->getSiteId()) {
            $options['disabled'] = true;
        }
        $form->add('siteId', 'text', $options);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}
