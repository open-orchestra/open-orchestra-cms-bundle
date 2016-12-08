<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class CollectionSubscriber
 *
 */
class CollectionSubscriber implements EventSubscriberInterface
{
    /**
     * Triggered when a collection is submitted
     *
     * @param FormEvent $event
     */
    public function changeCollection(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $form->getData();
        $order = array_flip(array_keys($event->getData()));

        foreach ($order as $key => $value) {
            $child = $data->get($key);
            $data->remove($key);
            $data->set($key, $child);
        }
        $form->setData($data);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'changeCollection',
        );
    }
}
