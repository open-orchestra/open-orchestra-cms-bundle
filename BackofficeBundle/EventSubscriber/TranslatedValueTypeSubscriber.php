<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class TranslatedValueTypeSubscriber
 */
class TranslatedValueTypeSubscriber implements EventSubscriberInterface
{
    protected $languages;
    
    /**
     * @param array $languages
     */
    public function __construct($languages)
    {
        $this->languages = $languages;
    }
    
    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (in_array($data->getLanguage(), $this->languages)) {
            $form->add('language', 'hidden');
            $form->add('value', 'text', array(
                'label' => $data->getLanguage(),
                'attr' => array(
                    'class' => 'generate-id-source',
                ),
            ));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

}
