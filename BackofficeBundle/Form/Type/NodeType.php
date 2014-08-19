<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class NodeType
 */
class NodeType extends AbstractType
{
    protected $nodeClass;

    /**
     * @param string $nodeClass
     */
    public function __construct($nodeClass)
    {
        $this->nodeClass = $nodeClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('templateId', 'document', array(
                'empty_value' => '--------',
                'class' => 'PHPOrchestraModelBundle:Template',
            ))
            ->add('name', 'text')
            ->add('nodeType', 'choice', array(
                'choices' => array('page' => 'Page simple')
            ))
            ->add('path', 'text')
            ->add('alias', 'text')
            ->add('language', 'orchestra_language')
            ->add('status', 'orchestra_status')
            ->add('submit', 'submit', array(
                'label' => 'php_orchestra_backoffice.form.submit'
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->nodeClass,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node';
    }
}
