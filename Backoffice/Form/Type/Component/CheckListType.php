<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CheckListType
 */
class CheckListType extends AbstractType
{
    protected $checkListTransformer;

    /**
     * @param DataTransformerInterface $checkListTransformer
     */
    public function __construct(
        DataTransformerInterface $checkListTransformer
        ) {
            $this->checkListTransformer = $checkListTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check_list', 'collection', array(
                'entry_type' => 'checkbox',
                'label' => false,
        ));

        $builder->addModelTransformer($this->checkListTransformer);
    }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_check_list";
    }
}
