<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class CheckListCollectionType
 */
class CheckListCollectionType extends AbstractType
{
    protected $checkListCollectionTransformer;

    /**
     * @param DataTransformerInterface $checkListCollectionTransformer
     */
    public function __construct(
        DataTransformerInterface $checkListCollectionTransformer
        ) {
            $this->checkListCollectionTransformer = $checkListCollectionTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check_list_collection', 'collection', array(
                'entry_type' => 'oo_check_list',
                'label' => false,
         ));

        $builder->addModelTransformer($this->checkListCollectionTransformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'configuration' => array(),
                'max_columns' => 0,
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
       $view->vars['max_columns'] = $options['max_columns'];
   }

    /**
     * @return string The name of this type
     */
    public function getName()
    {
        return "oo_check_list_collection";
    }
}
