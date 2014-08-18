<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Document\Block;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class AreaTypeSubscriber
 */
class AreaTypeSubscriber implements EventSubscriberInterface
{
    protected $node;

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $area = $form->getData();
        $data = $event->getData();

        if (array_key_exists('newBlocks', $data)) {
            foreach ($data['newBlocks'] as $newBlockType) {
                $newBlock = new Block();
                $newBlock->setComponent($newBlockType);

                $this->node->addBlock($newBlock);
                $blockIndex = $this->node->getBlockIndex($newBlock);

                $area->addBlock(array('nodeId' => 0, 'blockId' => $blockIndex));
            }
        }
        if (array_key_exists('newAreas', $data)) {
            foreach ($data['newAreas'] as $newAreaData) {
                $newArea = new Area();
                $newArea->setAreaId($newAreaData);

                $area->addSubArea($newArea);
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
        $area = $event->getData();

        if (0 == count($area->getBlocks())) {
            $form->add('newAreas', 'collection', array(
                'type' => 'text',
                'allow_add' => true,
                'mapped' => false,
                'attr' => array(
                    'data-prototype-label-add' => 'Ajout',
                    'data-prototype-label-remove' => 'Suppression',
                )
            ));
        }
        if (0 == $area->getSubAreas()->count()) {
            $form->add('newBlocks', 'collection', array(
                'type' => 'orchestra_block',
                'allow_add' => true,
                'mapped' => false,
                'attr' => array(
                    'data-prototype-label-add' => 'Ajout',
                    'data-prototype-label-remove' => 'Suppression',
                )
            ));
        }
    }
}
