<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\ModelBundle\Model\FieldTypeInterface;
use PHPOrchestra\ModelBundle\Repository\ContentTypeRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ContentTypeSubscriber
 */
class ContentTypeSubscriber implements EventSubscriberInterface
{
    protected $contentTypeRepository;

    /**
     * @param ContentTypeRepository $contentTypeRepository
     */
    public function __construct(ContentTypeRepository $contentTypeRepository)
    {
        $this->contentTypeRepository = $contentTypeRepository;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $contentType = $this->contentTypeRepository->findOneByContentTypeId($data->getContentType());

        if (is_object($contentType)) {
            /** @var FieldTypeInterface $field */
            foreach ($contentType->getFields() as $field) {
                $attribute = $data->getAttributeByName($field->getFieldId());
                if ($attribute) {
                    $defaultValue = $attribute->getValue();
                } else {
                    $defaultValue = $field->getDefaultValue();
                }
                $form->add($field->getFieldId(), $field->getSymfonyType(), array_merge(
                    array(
                        'data' => $defaultValue,
                        'label' => $field->getLabel(),
                        'mapped' => false,
                    ),
                    $field->getOptions()
                ));
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }
}
