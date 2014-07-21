<?php
/**
 * This file is part of the PHPOrchestra\CMSBundle.
 *
 * @author Noël Gilain <noel.gilain@businessdecision.com>
 */

namespace PHPOrchestra\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Model\PHPOrchestraCMSBundle\ContentType as ContentTypeModel;
use PHPOrchestra\CMSBundle\Form\DataTransformer\ContentTypeTransformer;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContentTypeType extends AbstractType
{
    protected $serializer = null;
    protected $customTypes = null;

    public function __construct($serializer, $orchestraCustomTypes = array())
    {
        $this->serializer = $serializer;
        $this->customTypes = $orchestraCustomTypes;
    }

    /**
     * (non-PHPdoc)
     * @see src/symfony2/vendor/symfony/symfony/src/Symfony/Component/Form/Symfony
     * \Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ContentTypeTransformer($this->serializer, $this->customTypes);
        $builder->addModelTransformer($transformer);
        
        $builder
            ->add(
                'contentTypeId',
                'text',
                array(
                    'label' => 'php_orchestra_cms.content_types.form.identifier',
                    'translation_domain' => 'backOffice',
                    'constraints' => new NotBlank()
                )
            )
            ->add(
                'name',
                'multilingualText',
                array(
                    'label' => 'php_orchestra_cms.content_types.form.label',
                    'translation_domain' => 'backOffice'
                )
            )
            ->add(
                'status',
                'choice',
                array(
                    'choices' => array(
                        ContentTypeModel::STATUS_DRAFT => ContentTypeModel::STATUS_DRAFT,
                        ContentTypeModel::STATUS_PUBLISHED => ContentTypeModel::STATUS_PUBLISHED
                    ),
                    'label' => 'php_orchestra_cms.content_types.form.status',
                    'translation_domain' => 'backOffice'
                )
            )
            ->add('fields', 'content_type_fields', array('data' => $options['data']->getFields()))
            ->add('id', 'hidden', array('mapped' => false, 'data' => (string)$options['data']->getId()))
            ->add('version', 'hidden', array('read_only' => true))
            ->add('newField', 'hidden', array('required' => false));
    }

    /**
     * (non-PHPdoc)
     * @see src/symfony2/vendor/symfony/symfony/src/Symfony/Component/Form/Symfony
     * \Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'content_type';
    }
}
