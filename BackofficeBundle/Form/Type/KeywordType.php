<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
                'label' => 'open_orchestra_backoffice.form.keyword.label'
            )
        );

        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
    * @return string
    */
    public function getName()
    {
        return 'oo_keyword';
    }
}
