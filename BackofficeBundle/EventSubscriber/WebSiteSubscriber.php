<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class WebSiteSubscriber
 */
class WebSiteSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $options = array(
            'label' => 'php_orchestra_backoffice.form.website.site_id',
            'attr' => array('class' => 'generate-id-dest')
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
        );
    }
}
