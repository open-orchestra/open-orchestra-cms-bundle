<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\Backoffice\Manager\TemplateManager;

/**
 * Class WebSiteNodeTemplateSubscriber
 */
class WebSiteNodeTemplateSubscriber implements EventSubscriberInterface
{
    protected $templateManager;

    /**
     * @param array $templateSetParameters
     */
    public function __construct(TemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $disabled = null !== $data->getSiteId();
        $form->add('siteTemplateSelection', 'form', array(
            'virtual' => true,
            'label' => false,
            'attr' => array('class' => 'select-grouping'),
            'mapped' => false,
            'required' => false,
            'disabled' => $disabled,
            'tabulation_rank' => 1,
        ));
        $form->get('siteTemplateSelection')->add('templateSet', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.website.template_set',
            'choices' => $this->getTemplateSetChoices(),
            'attr' => array('class' => 'select-grouping-master'),
            'required' => true,
            'disabled' => $disabled,
        ));
        $form->get('siteTemplateSelection')->add('templateNodeRoot', 'choice', array(
            'label' => 'open_orchestra_backoffice.form.website.template_node_root.label',
            'choices' => $this->getTemplateChoices(),
            'attr'  => array(
                'help_text' => 'open_orchestra_backoffice.form.website.template_node_root.helper',
                'class' => 'select-grouping-slave'
            ),
            'required' => true,
            'disabled' => $disabled,
        ));
    }

    /**
     *
     * @return array
     */
    protected function getTemplateSetChoices()
    {
        $templateSetParameters = $this->templateManager->getTemplateSetParameters();
        $choices = array();
        foreach ($templateSetParameters as $key => $parameter) {
            $choices[$key] = $parameter['label'];
        }

        return $choices;
    }

    /**
     *
     * @return array
     */
    protected function getTemplateChoices()
    {
        $templateSetParameters = $this->templateManager->getTemplateSetParameters();
        $choices = array();
        foreach ($templateSetParameters as $keyTemplateSet => $templateSetParameters) {
            foreach ($templateSetParameters['templates'] as $key => $template) {
                $choices[$keyTemplateSet][$key] = $template['label'];
            }
        }

        return $choices;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        );
    }
}
