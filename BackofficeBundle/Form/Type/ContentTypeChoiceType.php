<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\ModelBundle\Repository\ContentTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContentTypeChoiceType
 */
class ContentTypeChoiceType extends AbstractType
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
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contentTypes = $this->contentTypeRepository->findAll();
        if (!empty($contentTypes)) {
            $choices = array();
            foreach ($contentTypes as $contentType) {
                $choices[$contentType->getContentTypeId()] = $contentType->getName();
            }
            
            $builder->add('contentTypeId', 'choice', array(
                'required' => false,
                'choices' => $choices,
            ));
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'content_type_choice';
    }
}
