<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InternalLinkType
 */
class InternalLinkType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        if (!is_array($data) || !array_key_exists('site', $data)) {
            $data['site'] = array();
        }

        if ($options['with_label']) {
            $builder->add('label', 'text', array(
                'label' => 'open_orchestra_backoffice.form.internal_link.label',
            ));
        }
        $builder->add('site', 'oo_site_site_alias', array(
            'label' => false,
            'required' => true,
            'data' => $data['site'],
        ))
        ->add('query', 'text', array(
            'label' => 'open_orchestra_backoffice.form.internal_link.query',
            'required' => false,
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'with_label' => true,
            'inherit_data' => true,
        ));
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_internal_link";
    }
}
