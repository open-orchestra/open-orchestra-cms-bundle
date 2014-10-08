<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlockTypeSubscriber
 */
class BlockTypeSubscriber implements EventSubscriberInterface
{
    protected $generateFormManager;
    protected $blockPosition;

    /**
     * @param GenerateFormManager $generateFormManager
     * @param int                 $blockPosition
     */
    public function __construct(GenerateFormManager $generateFormManager, $blockPosition)
    {
        $this->generateFormManager = $generateFormManager;
        $this->blockPosition = $blockPosition;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $label = $data->getLabel();
        if ('' == $label) {
            $label = $data->getComponent() . ' #' . ($this->blockPosition + 1);
        }

        $form->add('label', 'text', array('data' => $label));

        $this->generateFormManager->buildForm($form, $data);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $block = $form->getData();
        $blockAttributes = $block->getAttributes();
        $data = $event->getData();

        foreach ($data as $key => $value) {
            if ('component' == $key || 'submit' == $key || 'label' == $key) {
                continue;
            }

            $blockAttributes[$key] = $value;

            if (is_string($value) && is_array(json_decode($value, true))) {
                $blockAttributes[$key] = json_decode($value, true);
            }
        }

        $block->setAttributes($blockAttributes);

        $this->generateFormManager->alterFormAfterSubmit($form, $block);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }
}
