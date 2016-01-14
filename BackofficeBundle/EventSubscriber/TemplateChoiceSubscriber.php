<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class TemplateChoiceSubscriber
 */
class TemplateChoiceSubscriber implements EventSubscriberInterface
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
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $templateChoiceContainer = $form->getData();
        $data = $event->getData();

        if (
            array_key_exists('templateId', $data) &&
            null === $templateChoiceContainer->getId() &&
            0 === $templateChoiceContainer->getAreas()->count() &&
            0 === $templateChoiceContainer->getBlocks()->count()
        ) {
            $template = $this->templateRepository->findOneByTemplateId($data['templateId']);
            if (null !== $template) {
                $templateChoiceContainer->setBoDirection($template->getBoDirection());
                $templateChoiceContainer->setAreas($template->getAreas());
                $templateChoiceContainer->setBlocks($template->getBlocks());
            }
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
            $form->add('templateId', 'choice', array(
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
