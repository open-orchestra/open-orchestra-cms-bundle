<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\Transformer\ConditionFromBooleanToBddTransformerInterface;

/**
 * Class ContentSearchSubscriber
 */
class ContentSearchSubscriber implements EventSubscriberInterface
{
    protected $contentRepository;
    protected $contextManager;
    protected $transformer;
    protected $attributes;
    protected $required;

    /**
     * @param ContentRepositoryInterface                    $contentRepository
     * @param CurrentSiteIdInterface                        $contextManager
     * @param ConditionFromBooleanToBddTransformerInterface $transformer
     * @param array                                         $attributes
     * @param boolean                                       $required
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        CurrentSiteIdInterface $contextManager,
        ConditionFromBooleanToBddTransformerInterface $transformer,
        array $attributes,
        $required
    ) {
        $this->contentRepository = $contentRepository;
        $this->contextManager = $contextManager;
        $this->transformer = $transformer;
        $this->attributes = $attributes;
        $this->required = $required;
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ('PATCH' !== $form->getParent()->getConfig()->getMethod()) {
            $this->addFormType($event);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        if ('PATCH' === $form->getParent()->getConfig()->getMethod()) {
            $this->addFormType($event);
        }
    }

    protected function addFormType(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $addDefault = $this->required;
        if (!is_null($data)) {
            if ($data['contentType'] != '' || $data['keywords'] != '') {
                $condition = null;
                if ($data['keywords'] != '') {
                    $condition = json_decode($this->transformer->reverseTransform($data['keywords']), true);
                }
                $form->add('contentId', 'choice', array(
                    'label' => false,
                    'required' => $this->required,
                    'choices' => $this->getChoices($data['contentType'], $data['choiceType'], $condition),
                    'attr' => $this->attributes,
                ));
                $addDefault = false;
            } elseif (array_key_exists('contentId', $data) && $data['contentId'] != '') {
                $form->add('contentId', 'choice', array(
                    'label' => false,
                    'required' => $this->required,
                    'choices' => $this->getChoice($data['contentId']),
                    'attr' => $this->attributes,
                ));
                $addDefault = false;
            }
        }
        if ($addDefault) {
            $form->add('contentId', 'hidden', array(
                'required' => $this->required,
            ));
            $form->add('help-text', 'button', array(
                'disabled' => true,
                'label' => 'open_orchestra_backoffice.form.content_search.use'
            ));
        }
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
     * @param string $contentType
     * @param string $operator
     * @param string $keywords
     */
    protected function getChoices($contentType, $choiceType, $condition)
    {
        $choices = array();
        $language = $this->contextManager->getCurrentSiteDefaultLanguage();
        $contents = $this->contentRepository->findByContentTypeAndCondition($language, $contentType, $choiceType, $condition);

        foreach ($contents as $content) {
            $choices[$content->getId()] = $content->getName();
        }

        return $choices;
    }

    /**
     * @param string $contentId
     */
    protected function getChoice($contentId)
    {
        $choices = array();
        $content = $this->contentRepository->find($contentId);
        $choices[$content->getId()] = $content->getName();

        return $choices;
    }
}
