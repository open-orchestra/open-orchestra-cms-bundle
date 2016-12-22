<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TreeListType
 */
class TreeListType extends AbstractType
{
    protected $TreeListTransformer;

    /**
     * @param DataTransformerInterface $TreeListTransformer
     */
    public function __construct(
        DataTransformerInterface $TreeListTransformer
        ) {
            $this->TreeListTransformer = $TreeListTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tree_list', 'collection', array(
                'entry_type' => 'checkbox',
                'label' => false,
        ));

        $builder->addModelTransformer($this->TreeListTransformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'configuration' => array(),
            ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
       $id = 'default';
       if (!is_null($options['property_path'])) {
           $id = preg_replace('/^\[(.*)\]$/', '$1', $options['property_path']);
       }

       $view->vars['configuration'] = $options['configuration'][$id];
    }
    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_tree_list";
    }
}
