<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use PHPOrchestra\ModelBundle\Repository\TemplateRepository;

/**
 * Class TemplateChoiceSubscriber
 */
class TemplateChoiceSubscriber implements EventSubscriberInterface
{
    protected $templateRepository;

    /**
     * @param TemplateRepository $templateRepository
     */
    public function __construct(TemplateRepository $templateRepository)
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
            null === $templateChoiceContainer->getId()
        ) {
            $template = $this->templateRepository->findOneByTemplateId($data['templateId']);
            if (null !== $template) {
                $templateChoiceContainer->setAreas($template->getAreas());
                $templateChoiceContainer->setBlocks($template->getBlocks());
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
        );
    }
}
