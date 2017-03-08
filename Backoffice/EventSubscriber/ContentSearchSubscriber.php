<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\ModelInterface\Repository\ReadContentRepositoryInterface;

/**
 * Class ContentSearchSubscriber
 */
class ContentSearchSubscriber implements EventSubscriberInterface
{
    protected $contentRepository;
    protected $contextManager;
    protected $required;

    /**
     * @param ContentRepositoryInterface                    $contentRepository
     * @param CurrentSiteIdInterface                        $contextManager
     * @param boolean                                       $required
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        CurrentSiteIdInterface $contextManager,
        $required
    ) {
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
        $this->required = $required;
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ('PATCH' !== $form->getRoot()->getConfig()->getMethod()) {
            $this->addFormType($event);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $this->addFormType($event);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    protected function addFormType(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $event->setData($data);
        $choices = array();
        if (!is_null($data)) {
            if (array_key_exists('contentType', $data) && $data['contentType'] != '') {
                $choices = array_merge($choices, $this->getChoices($data['contentType'],
                    array_key_exists('choiceType', $data) && $data['choiceType'] != '' ? $data['choiceType']: ReadContentRepositoryInterface::CHOICE_AND,
                    array_key_exists('keywords', $data) && $data['keywords'] ? $data['keywords']: null));
            }
            if (array_key_exists('contentId', $data) && $data['contentId'] != '') {
                $choices = array_merge($choices, $this->getChoice($data['contentId']));
            }
        }
        $form->add('refresh', 'button', array(
            'label' => 'open_orchestra_backoffice.form.content_search.refresh_content_list',
            'attr' => array('class' => 'glyphicon glyphicon-refresh patch-submit-click'),
            'button_class' => 'border'
        ));
        $form->add('contentId', 'choice', array(
            'label' => false,
            'empty_value' => ' ',
            'required' => $this->required,
            'choices' => $choices,
            'attr' => array('class' => 'subform-to-refresh'),
        ));
    }

    /**
     * @param string $contentType
     * @param string $choiceType
     * @param string $condition
     *
     * @return array
     */
    protected function getChoices($contentType, $choiceType, $condition)
    {
        $choices = array();
        $language = $this->contextManager->getCurrentSiteDefaultLanguage();
        $siteId = $this->contextManager->getCurrentSiteId();

        $contents = $this->contentRepository->findByContentTypeAndCondition($language, $contentType, $choiceType, $condition, $siteId);
        foreach ($contents as $content) {
            $choices[$content->getContentId()] = $content->getName();
        }

        return $choices;
    }

    /**
     * @param string $contentId
     *
     * @return array
     */
    protected function getChoice($contentId)
    {
        $choices = array();
        $content = $this->contentRepository->findOneByContentId($contentId);
        $choices[$content->getContentId()] = $content->getName();

        return $choices;
    }
}
