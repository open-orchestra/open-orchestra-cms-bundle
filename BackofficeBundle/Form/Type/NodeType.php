<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BaseBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\TemplateChoiceSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PHPOrchestra\ModelBundle\Repository\TemplateRepository;

/**
 * Class NodeType
 */
class NodeType extends AbstractType
{
    protected $nodeClass;
    protected $templateRepository;

    /**
     * @param string             $nodeClass
     * @param TemplateRepository $templateRepository
     */
    public function __construct($nodeClass, TemplateRepository $templateRepository)
    {
        $this->nodeClass = $nodeClass;
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'label' => 'php_orchestra_backoffice.form.node.name',
            'attr' => array(
                'class' => 'alias-source',
            )
            ))
            ->add('nodeType', 'choice', array(
                'choices' => array(
                    'page' => 'Page simple'
                ),
                'label' => 'php_orchestra_backoffice.form.node.node_type'
            ))
            ->add('theme', 'orchestra_theme_choice', array(
                'label' => 'php_orchestra_backoffice.form.node.theme'
            ))
            ->add('alias', 'text', array(
                'label' => 'php_orchestra_backoffice.form.node.alias',
                'attr' => array(
                    'class' => 'alias-dest',
                )
            ))
            ->add('language', 'orchestra_language', array(
                'label' => 'php_orchestra_backoffice.form.node.language'
            ))
            ->add('inMenu', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.node.in_menu',
                'required' => false
            ))
            ->add('inFooter', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.node.in_footer',
                'required' => false
            ))
            ->add('status', 'orchestra_status', array(
                'label' => 'php_orchestra_backoffice.form.node.status'
            ))
            ->add('nodeId', 'hidden', array(
                'disabled' => true
            ));

        $builder->addEventSubscriber(new TemplateChoiceSubscriber($this->templateRepository));
        $builder->addEventSubscriber(new AreaCollectionSubscriber());
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->nodeClass
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
