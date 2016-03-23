<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class WebSiteNodeTemplateSubscriber
 */
class WebSiteNodeTemplateSubscriber implements EventSubscriberInterface
{
    protected $templateRepository;

    /**
     * @param TemplateRepositoryInterface $templateRepository
     */
    public function __construct(TemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        if (null === $data->getSiteId()) {
            $form->add('templateId', 'choice', array(
                'choices' => $this->getTemplateChoices(),
                'required' => true,
                'mapped' => false,
                'label' => 'open_orchestra_backoffice.form.website.template_node_root.label',
                'attr'  => array(
                    'help_text' => 'open_orchestra_backoffice.form.website.template_node_root.helper',
                )
            ));
        }
    }

    /**
     *
     * @return array
     */
    protected function getTemplateChoices()
    {
        $templates = $this->templateRepository->findByDeleted(false);
        $templatesChoices = array();
        foreach ($templates as $template) {
            $templatesChoices[$template->getTemplateId()] = $template->getName();
        }

        return $templatesChoices;
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
