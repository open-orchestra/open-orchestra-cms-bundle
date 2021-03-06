<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

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

        if ($data instanceof RedirectionInterface) {
            if (false !== ($siteName = array_search($data->getSiteId(), $form->get('siteId')->getConfig()->getOption('choices')))) {
                $data->setSiteName($siteName);
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
