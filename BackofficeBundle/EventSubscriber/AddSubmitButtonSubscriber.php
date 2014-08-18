<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class AddSubmitButtonSubscriber
 */
class AddSubmitButtonSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $form->add('submit', 'submit', array(
            'label' => 'php_orchestra_backoffice.form.submit'
        ));
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
        );
    }
}
