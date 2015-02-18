<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('price', 'text');
        $builder->add('name');
        $builder->add('description', null, array('required' => false));


        $builder->add('products', 'collection', array('type' => 'product'));

        $builder->add('save', 'submit');
    }

    Public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\ProductBundle\Entity\Product',
        ));
    }

    public function getName()
    {
        return 'product';
    }
} 