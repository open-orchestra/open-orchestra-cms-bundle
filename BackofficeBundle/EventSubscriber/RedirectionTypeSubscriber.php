<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class RedirectionTypeSubscriber
 */
class RedirectionTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var RedirectionInterface $data */
        $data = $event->getData();

        if ($data) {
            if (array_key_exists($data->getSiteId(), $form->get('siteId')->getConfig()->getOption('choices'))) {
                $data->setSiteName($form->get('siteId')->getConfig()->getOption('choices')[$data->getSiteId()]);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }
}
