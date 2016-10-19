<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;

/**
 * Class NodeTemplateSelectionSubscriber
 */
class NodeTemplateSelectionSubscriber implements EventSubscriberInterface
{
    protected $nodeManager;
    protected $contextManager;
    protected $siteRepository;
    protected $templateSetparameters;

    /**
     * @param NodeManager             $nodeManager
     * @param CurrentSiteIdInterface  $contextManager
     * @param SiteRepositoryInterface $siteRepository
     * @param array                   $templateSetparameters
     */
    public function __construct(
        NodeManager $nodeManager,
        CurrentSiteIdInterface $contextManager,
        SiteRepositoryInterface $siteRepository,
        array $templateSetparameters
    ) {
        $this->nodeManager = $nodeManager;
        $this->contextManager = $contextManager;
        $this->siteRepository = $siteRepository;
        $this->templateSetparameters = $templateSetparameters;
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
            null === $formData->getId() &&
            array_key_exists('nodeSource', $data['nodeTemplateSelection']) &&
            '' != $data['nodeTemplateSelection']['nodeSource']
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
            $form->get('nodeTemplateSelection')->add('template', 'choice', array(
                'choices' => $this->getTemplateChoices(),
                'required' => false,
                'label' => 'open_orchestra_backoffice.form.node.template'
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
    protected function getTemplateChoices()
    {
        $siteId = $this->contextManager->getCurrentSiteId();
        $site = $this->siteRepository->findOneBySiteId($siteId);
        $templateSetId = $site->getTemplateSet();

        $choices = array();
        foreach ($this->templateSetparameters[$templateSetId]['templates'] as $key => $template) {
            $choices[$key] = $template['label'];
        }

        return $choices;
    }
}
