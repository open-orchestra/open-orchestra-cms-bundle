<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\Form\FormEvent;

/**
 * Class BlockTypeSubscriber
 */
class BlockTypeSubscriber extends AbstractBlockContentTypeSubscriber
{
    protected $generateFormManager;
    protected $fixedParams;
    protected $blockPosition;

    /**
     * @param GenerateFormManager $generateFormManager
     * @param array               $fixedParams
     * @param int                 $blockPosition
     */
    public function __construct(GenerateFormManager $generateFormManager, $fixedParams, $blockPosition = 0)
    {
        $this->generateFormManager = $generateFormManager;
        $this->fixedParams = $fixedParams;
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
        $form->add('class', 'text', array('data' => $data->getClass(), 'required'  => false));
        $form->add('id', 'text', array('data' => $data->getId(), 'required'  => false));

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
            if (in_array($key, $this->fixedParams)) {
                continue;
            }

            $value = $this->transformData($value, $form->get($key));

            $blockAttributes[$key] = $value;

            if (is_string($value) && is_array(json_decode($value, true))) {
                $blockAttributes[$key] = json_decode($value, true);
            }
        }

        foreach ($blockAttributes as $key => $blockAttribute) {
            if ($form->has($key) && !array_key_exists($key, $data)) {
                $blockAttributes[$key] = false;
            }
        }

        $block->setAttributes($blockAttributes);

        $this->generateFormManager->alterFormAfterSubmit($form, $block);
    }
}
