<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class KeywordType
 */
class KeywordType extends AbstractType
{
    /**
    * @param FormBuilderInterface $builder
    * @param array                $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text', array(
                'label' => 'open_orchestra_backoffice.form.keyword.label',
                'sub_group_id' => 'property',
            )
        );

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['delete_button'] = $options['delete_button'];
        $view->vars['new_button'] = $options['new_button'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'group_enabled' => true,
            'delete_button' => false,
            'new_button' => false,
            'sub_group_render' => array(
                'property' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.keyword.sub_group.property',
                )
            )
        ));
    }

    /**
    * @return string
    */
    public function getName()
    {
        return 'oo_keyword';
    }
}
