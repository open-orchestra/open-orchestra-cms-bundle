<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\FieldTypeCollectionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ContentTypeType
 */
class ContentTypeType extends AbstractType
{
    protected $contentTypeClass;
    protected $translator;

    /**
     * @param string              $contentTypeClass
     * @param TranslatorInterface $translator
     */
    public function __construct($contentTypeClass, TranslatorInterface $translator)
    {
        $this->contentTypeClass = $contentTypeClass;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentTypeId', 'text')
            ->add('name', 'text')
            ->add('version', 'text')
            ->add('status', 'orchestra_status');
        $builder->add('fields', 'collection', array(
            'type' => 'field_type',
            'allow_add' => true,
            'allow_delete' => true,
            'label' => 'New fields',
            'attr' => array(
                'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.form.field_type.add'),
                'data-prototype-label-new' => $this->translator->trans('php_orchestra_backoffice.form.field_type.new'),
                'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.form.field_type.delete'),
            )
        ));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->contentTypeClass,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'content_type';
    }
}
