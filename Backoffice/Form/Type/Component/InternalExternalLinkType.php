<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InternalExternalLinkType
 */
class InternalExternalLinkType extends AbstractType
{
    const TYPE_INTERNAL = 'show-hide-internal';
    const TYPE_EXTERNAL = 'show-hide-external';

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('type', 'choice', array(
            'label'        => 'open_orchestra_backoffice.form.internal_external.type.label',
            'choices'      => array(
                self::TYPE_INTERNAL => 'open_orchestra_backoffice.form.internal_external.type.internal',
                self::TYPE_EXTERNAL => 'open_orchestra_backoffice.form.internal_external.type.external'
            ),
            'expanded'     => true,
            'multiple'     => false,
            'mapped'       => false,
            'attr' => array(
                'class' => 'show-hide'
            )
        ))
        ->add('internalUrl', 'oo_internal_url', array(
            'label' => 'open_orchestra_backoffice.form.internal_external.url',
            'required' => false,
            'attr_class' => self::TYPE_INTERNAL,
        ))
        ->add('url', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_external.url',
            'required' => false,
            'attr' => array(
                'class' => self::TYPE_EXTERNAL
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
        ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (is_null($form->get('type')->getData())) {
            $type = self::TYPE_INTERNAL;
            if ('' !== trim($form->get('url')->getData())) {
                $type = self::TYPE_EXTERNAL;
            }
            $form->get('type')->setData($type);
        }
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_internal_external_link";
    }
}
