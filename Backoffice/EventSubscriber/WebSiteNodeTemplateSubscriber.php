<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class WebSiteNodeTemplateSubscriber
 */
class WebSiteNodeTemplateSubscriber implements EventSubscriberInterface
{
    protected $templateSetParameters;

    /**
     * @param array $templateSetParameters
     */
    public function __construct(array $templateSetParameters)
    {
        $this->templateSetParameters = $templateSetParameters;
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        if (null === $data->getSiteId()) {
            $form->add('siteTemplateSelection', 'form', array(
                'virtual' => true,
                'label' => false,
                'attr' => array('class' => 'select-grouping'),
                'mapped' => false,
                'required' => false,
            ));
            $form->get('siteTemplateSelection')->add('templateSet', 'choice', array(
                'label' => 'open_orchestra_backoffice.form.website.template_set',
                'choices' => $this->getTemplateSetChoices(),
                'attr' => array('class' => 'select-grouping-master'),
                'required' => true,
            ));
            $form->get('siteTemplateSelection')->add('templateRoot', 'choice', array(
                'label' => 'open_orchestra_backoffice.form.website.template_node_root.label',
                'choices' => $this->getTemplateChoices(),
                'attr'  => array(
                    'help_text' => 'open_orchestra_backoffice.form.website.template_node_root.helper',
                    'class' => 'select-grouping-slave'
                ),
                'required' => true,
            ));
        }
    }

    /**
     *
     * @return array
     */
    protected function getTemplateSetChoices()
    {
        $choices = array();
        foreach ($this->templateSetParameters as $key => $parameter) {
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
        $choices = array();
        foreach ($this->templateSetParameters as $keyTemplateSet => $templateSetParameters) {
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
