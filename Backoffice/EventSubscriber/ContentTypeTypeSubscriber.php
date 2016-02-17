<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ContentTypeTypeSubscriber
 */
class ContentTypeTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $options = array(
            'label' => 'open_orchestra_backoffice.form.content_type.content_type_id',
            'attr' => array('class' => 'generate-id-dest')
        );
        if (null !== $data->getContentTypeId()) {
            $options['disabled'] = true;
        }
        $form->add('contentTypeId', 'text', $options);
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
