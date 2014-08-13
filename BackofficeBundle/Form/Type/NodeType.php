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
    protected $router;
    protected $nodeClass;

    /**
     * @param UrlGeneratorInterface $router
     * @param string                $nodeClass
     */
    public function __construct(
        UrlGeneratorInterface $router,
        $nodeClass
    )
    {
        $this->router = $router;
        $this->nodeClass = $nodeClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $templateUrl = $this->router->generate('php_orchestra_cms_templateajaxrequest', array('templateId' => '%s'));
        $templateUrl = urldecode($templateUrl);

        $builder
            ->add('templateId', 'orchestra_template_choice', array(
                'empty_value' => '--------',
                'attr' => array('data-url'=> $templateUrl),
                'required' => false,
            ))
            ->add('name', 'text')
            ->add('nodeType', 'choice', array('choices' => array('page' => 'Page simple')))
            ->add('parentId', 'hidden')
            ->add('path', 'text')
            ->add('alias', 'text')
            ->add('language', 'orchestra_language')
            ->add('status', 'orchestra_status')
            ->add('save', 'submit', array('attr' => array('class' => 'not-mapped')));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->nodeClass,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node';
    }
}
