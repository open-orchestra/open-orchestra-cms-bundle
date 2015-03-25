<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Doctrine\Common\Util\Inflector;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class BlockTypeSubscriber
 */
class BlockTypeSubscriber extends AbstractBlockContentTypeSubscriber
{
    protected $generateFormManager;
    protected $fixedParameters;
    protected $blockPosition;
    protected $formFactory;

    /**
     * @param GenerateFormManager  $generateFormManager
     * @param array                $fixedParameters
     * @param FormFactoryInterface $formFactory
     * @param int                  $blockPosition
     */
    public function __construct(GenerateFormManager $generateFormManager, $fixedParameters, FormFactoryInterface $formFactory, $blockPosition = 0)
    {
        $this->generateFormManager = $generateFormManager;
        $this->fixedParameters = $fixedParameters;
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
        $blockAttributes = array();
        $data = $event->getData();

        foreach ($data as $key => $value) {
            if ($key == 'submit') {
                continue;
            }
            if (in_array($key, $this->fixedParameters)) {
                $setter = 'set' . Inflector::classify($key);
                $block->$setter($value);
                continue;
            }

            try {
                $value = $this->transformData($value, $form->get($key));
            } catch (TransformationFailedException $e) {
            }

            $blockAttributes[$key] = $value;

            if (is_string($value) && is_array(json_decode($value, true))) {
                $blockAttributes[$key] = json_decode($value, true);
            }
        }

        $block->setAttributes($blockAttributes);
    }
}
