<?php

namespace PHPOrchestra\CMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PHPOrchestra\CMSBundle\Form\DataTransformer\NodeTypeTransformer;

/**
 * Class NodeType
 */
class NodeType extends AbstractType
{
    protected $router;
    protected $nodeTypeTransformer;

    /**
     * @param NodeTypeTransformer $nodeTypeTransformer
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        NodeTypeTransformer $nodeTypeTransformer,
        UrlGeneratorInterface $router
    )
    {
        $this->nodeTypeTransformer = $nodeTypeTransformer;
        $this->router = $router;
    }

    /**
     * (non-PHPdoc)
     * @see src/symfony2/vendor/symfony/symfony/src/Symfony/Component/Form/Symfony
     * \Component\Form.AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->nodeTypeTransformer);

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
                'areas',
                'orchestra_areas',
                array(
                    'controller' => 'PHPOrchestraCMSBundle:Area:form',
                    'parameter' => array('type' => 'node')
                )
            )
            ->add(
                'blocks',
                'orchestra_blocks',
                array(
                    'mapped' => false,
                    'controller' => 'PHPOrchestraCMSBundle:Block:form',
                    'parameter' => array('type' => 'node')
                )
            )
            ->add('theme', 'orchestra_theme_choice')
            ->add('save', 'submit', array('attr' => array('class' => 'not-mapped')));
    }
    
    /**
     * (non-PHPdoc)
     * @see src/symfony2/vendor/symfony/symfony/src/Symfony/Component/Form/Symfony
     * \Component\Form.AbstractType::buildView()
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['inDialog'] = $options['inDialog'];
        $view->vars['beginJs'] = $options['beginJs'];
        $view->vars['endJs'] = $options['endJs'];
    }
    
    /**
     * (non-PHPdoc)
     * @see src/symfony2/vendor/symfony/symfony/src/Symfony/Component/Form/Symfony
     * \Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'inDialog' => false,
                'beginJs' => array(),
                'endJs' => array(),
                'data_class' => 'Model\PHPOrchestraCMSBundle\Node',
            )
        );
    }
            
    /**
     * (non-PHPdoc)
     * @see src/symfony2/vendor/symfony/symfony/src/Symfony/Component/Form/Symfony
     * \Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'node';
    }
}
