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
    const TYPE_INTERNAL = 'internal';
    const TYPE_EXTERNAL = 'external';

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
            'choice_attr' => array(
                self::TYPE_INTERNAL => array('class' => self::TYPE_INTERNAL),
                self::TYPE_EXTERNAL => array('class' => self::TYPE_EXTERNAL),
            ),
            'expanded'     => true,
            'multiple'     => false,
            'mapped'       => false,
            'attr' => array(
                'class' => 'show-hide'
            )
        ))
        ->add('tmp', 'oo_internal_link', array(
            'label' => 'open_orchestra_backoffice.form.internal_external.node_link',
            'required' => false,
            'with_label' => false,
            'attr' => array(
                'class' => self::TYPE_INTERNAL
            ),
            'mapped' => false,
        ))
        ->add('url', 'text', array(
            'label'        => 'open_orchestra_backoffice.form.internal_external.url',
            'required'     => false,
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
