<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Doctrine\Common\Util\Inflector;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactory;

/**
 * Class BlockTypeSubscriber
 */
class BlockTypeSubscriber extends AbstractBlockContentTypeSubscriber
{
    protected $generateFormManager;
    protected $fixedParams;
    protected $blockPosition;
    protected $formFactory;

    /**
     * @param GenerateFormManager $generateFormManager
     * @param array               $fixedParams
     * @param FormFactory         $formFactory
     * @param int                 $blockPosition
     */
    public function __construct(GenerateFormManager $generateFormManager, $fixedParams,FormFactory $formFactory, $blockPosition = 0)
    {
        $this->generateFormManager = $generateFormManager;
        $this->fixedParams = $fixedParams;
        $this->blockPosition = $blockPosition;
        $this->formFactory = $formFactory;
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
            $data->setLabel($data->getComponent() . ' #' . ($this->blockPosition + 1));
        }

        $newForm = $this->formFactory->create($this->generateFormManager->createForm($data));

        foreach ($newForm->all() as $newFormChildren) {
            $form->add($newFormChildren);
        }

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
                $setter = 'set' . Inflector::classify($key);
                $block->$setter($value);
                continue;
            }

            if (is_string($value)) {
                $value = $this->transformData($value, $form->get($key));

            } elseif (is_array($value)) {
                $transformedElements = array();
                foreach ($value as $element) {
                    $transformedElements[] = $this->transformData($element, $form->get($key));
                }
                $value = $transformedElements;
            }

            $blockAttributes[$key] = $value;

            if (is_string($value) && is_array(json_decode($value, true))) {
                $blockAttributes[$key] = json_decode($value, true);
            }
        }

        $block->setAttributes($blockAttributes);
    }
}
