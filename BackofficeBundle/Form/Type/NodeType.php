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
            ->add('nodeId', 'hidden')
            ->add('siteId', 'hidden')
            ->add('deleted', 'hidden')
            ->add('templateId', 'orchestra_template_choice', array('empty_value' => '--------', 'attr' => array('data-url'=> $templateUrl)))
            ->add('name', 'text', array('attr' => array('class' => 'used-as-label')))
            ->add('nodeType', 'choice', array('choices' => array('page' => 'Page simple')))
            ->add('parentId', 'hidden')
            ->add('path', 'text')
            ->add('alias', 'text')
            ->add('language', 'orchestra_language')
            ->add('status', 'orchestra_status')
            ->add(
                'blocks',
                'orchestra_blocks',
                array(
                    'mapped' => false,
                    'controller' => 'PHPOrchestraBackofficeBundle:Block:form',
                    'parameter' => array('type' => 'node')
                )
            )
            ->add('theme', 'orchestra_theme_choice')
            ->add('save', 'submit', array('attr' => array('class' => 'not-mapped')));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['inDialog'] = $options['inDialog'];
        $view->vars['beginJs'] = $options['beginJs'];
        $view->vars['endJs'] = $options['endJs'];
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'inDialog' => false,
                'beginJs' => array(),
                'endJs' => array(),
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
