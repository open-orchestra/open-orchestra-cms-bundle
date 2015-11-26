<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * Class AreaCollectionSubscriber
 */
class AreaCollectionSubscriber implements EventSubscriberInterface
{
    protected $areaClass;
    protected $translator;

    /**
     * @param string              $areaClass
     * @param TranslatorInterface $translator
     */
    public function __construct($areaClass, TranslatorInterface $translator)
    {
        $this->areaClass = $areaClass;
        $this->translator = $translator;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $areaContainer = $form->getData();
        $data = $event->getData();

        if (array_key_exists('newAreas', $data)) {
            foreach ($data['newAreas'] as $newAreaData) {
                $areaClass = $this->areaClass;
                /** @var AreaInterface $newArea */
                $newArea = new $areaClass();
                $newArea->setAreaId($newAreaData);

                $areaContainer->addArea($newArea);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $areaContainer = $event->getData();

        if (
            (!$areaContainer instanceof NodeInterface && 0 == count($areaContainer->getBlocks()))
            || ($areaContainer instanceof NodeInterface && $areaContainer->getId() )
        ) {
            $form->add('newAreas', 'collection', array(
                'type' => 'text',
                'allow_add' => !$form->isDisabled(),
                'mapped' => false,
                'required' => false,
                'label' => 'open_orchestra_backoffice.form.area.new_areas',
                'attr' => array(
                    'data-prototype-label-add' => $this->translator->trans('open_orchestra_backoffice.form.area.add_sub'),
                    'data-prototype-label-new' => $this->translator->trans('open_orchestra_backoffice.form.area.label_sub'),
                    'data-prototype-label-remove' => $this->translator->trans('open_orchestra_backoffice.form.area.remove_sub'),
                )
            ));
        } elseif ($areaContainer instanceof AreaInterface) {
            $form->add('newAreas', 'button', array(
                'disabled' => !$form->isDisabled(),
                'label' => $this->translator->trans('open_orchestra_backoffice.form.area.block_exists')
            ));
        }
    }
}
