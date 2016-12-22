<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TreeListCollectionType
 */
class TreeListCollectionType extends AbstractType
{
    protected $TreeListCollectionTransformer;

    /**
     * @param DataTransformerInterface $TreeListCollectionTransformer
     */
    public function __construct(
        DataTransformerInterface $TreeListCollectionTransformer
        ) {
            $this->TreeListCollectionTransformer = $TreeListCollectionTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tree_list_collection', 'collection', array(
                'entry_type' => 'oo_tree_list',
                'entry_options' => array(
                    'configuration' => $options['configuration'],
                ),
                'label' => false,
         ));

        $builder->addModelTransformer($this->TreeListCollectionTransformer);
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
       $view->vars['configuration'] = $options['configuration'];
   }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_tree_list_collection";
    }

}
