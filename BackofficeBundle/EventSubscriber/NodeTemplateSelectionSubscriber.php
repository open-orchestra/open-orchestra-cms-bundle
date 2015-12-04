<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\BackofficeBundle\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class NodeTemplateSelectionSubscriber
 */
class NodeTemplateSelectionSubscriber implements EventSubscriberInterface
{
    protected $nodeManager;
    protected $templateRepository;

    /**
     * @param NodeManager                 $nodeManager
     * @param TemplateRepositoryInterface $templateRepository
     */
    public function __construct(NodeManager $nodeManager, TemplateRepositoryInterface $templateRepository)
    {
        $this->nodeManager = $nodeManager;
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $formData = $form->getData();
        $data = $event->getData();

        if (
            array_key_exists('nodeTemplateSelection', $data) &&
            array_key_exists('templateId', $data['nodeTemplateSelection']) &&
            null === $formData->getId() &&
            0 === $formData->getAreas()->count() &&
            0 === $formData->getBlocks()->count()
        ) {
            $template = $this->templateRepository->findOneByTemplateId($data['nodeTemplateSelection']['templateId']);
            if (null !== $template) {
                $formData->setAreas($template->getAreas());
                $formData->setBlocks($template->getBlocks());
            }
        }

        if (
            array_key_exists('nodeTemplateSelection', $data) &&
            array_key_exists('nodeSource', $data['nodeTemplateSelection']) &&
            is_null($formData->getId()) &&
            !is_null($data['nodeTemplateSelection']['nodeSource'])
        ) {
            $this->nodeManager->hydrateNodeFromNodeId($formData, $data['nodeTemplateSelection']['nodeSource']);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data->getId()) {
            $form->add('nodeTemplateSelection', 'form', array(
                'virtual' => true,
                'label' => 'open_orchestra_backoffice.form.node.template_selection.name',
                'mapped' => false,
                'label_attr' => array('class' => 'one-needed'),
                'required' => false,
                'attr' => array(
                    'help_text' => 'open_orchestra_backoffice.form.node.template_selection.helper',
                )
            ));
            $form->get('nodeTemplateSelection')->add('nodeSource', 'oo_node_choice', array(
                'required' => false,
                'mapped' => false,
                'label' => 'open_orchestra_backoffice.form.node.node_source',
            ));
            $form->get('nodeTemplateSelection')->add('templateId', 'choice', array(
                'choices' => $this->getChoices(),
                'required' => false,
                'label' => 'open_orchestra_backoffice.form.node.template_id'
            ));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    /**
     *
     * @return array
     */
    protected function getChoices()
    {
        $templates = $this->templateRepository->findByDeleted(false);
        $templatesChoices = array();
        foreach ($templates as $template) {
            $templatesChoices[$template->getTemplateId()] = $template->getName();
        }

        return $templatesChoices;
    }
}
