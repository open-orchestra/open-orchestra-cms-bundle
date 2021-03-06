<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\Backoffice\Manager\TemplateManager;

/**
 * Class NodeTemplateSelectionSubscriber
 */
class NodeTemplateSelectionSubscriber implements EventSubscriberInterface
{
    protected $nodeManager;
    protected $contextManager;
    protected $siteRepository;
    protected $templateManager;

    /**
     * @param NodeManager                $nodeManager
     * @param ContextBackOfficeInterface $contextManager
     * @param SiteRepositoryInterface    $siteRepository
     * @param TemplateManager            $templateManager
     */
    public function __construct(
        NodeManager $nodeManager,
        ContextBackOfficeInterface $contextManager,
        SiteRepositoryInterface $siteRepository,
        TemplateManager $templateManager
    ) {
        $this->nodeManager = $nodeManager;
        $this->contextManager = $contextManager;
        $this->siteRepository = $siteRepository;
        $this->templateManager = $templateManager;
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
            $data['nodeTemplateSelection']['template'] = $formData->getTemplate();
            $event->setData($data);
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
                'label' => false,
                'mapped' => false,
                'group_id' => 'properties',
                'sub_group_id' => 'style'
            ));
            $form->get('nodeTemplateSelection')->add('template', 'choice', array(
                'choices' => $this->getTemplateChoices(),
                'required' => true,
                'label' => 'open_orchestra_backoffice.form.node.template'
            ));

            $form->get('nodeTemplateSelection')->add('nodeSource', 'oo_node_choice', array(
                'required' => false,
                'mapped' => false,
                'label' => 'open_orchestra_backoffice.form.node.node_source',
            ));
        } else {
            $form->add('template', 'choice', array(
                'choices' => $this->getTemplateChoices(),
                'required' => true,
                'label' => 'open_orchestra_backoffice.form.node.template',
                'group_id' => 'properties',
                'sub_group_id' => 'style'
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
        $siteId = $this->contextManager->getSiteId();
        $site = $this->siteRepository->findOneBySiteId($siteId);
        $templateSetId = $site->getTemplateSet();
        $templateSetParameters = $this->templateManager->getTemplateSetParameters();
        $choices = array();
        foreach ($templateSetParameters[$templateSetId]['templates'] as $key => $template) {
            $choices[$key] = $template['label'];
        }

        return $choices;
    }
}
