<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\NodeChoiceSubscriber;
use PHPOrchestra\BackofficeBundle\Manager\NodeManager;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\TemplateChoiceSubscriber;
use PHPOrchestra\ModelInterface\Repository\TemplateRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class NodeType
 */
class NodeType extends AbstractType
{
    protected $areaClass;
    protected $nodeClass;
    protected $nodeManager;
    protected $templateRepository;

    /**
     * @param string                      $nodeClass
     * @param TemplateRepositoryInterface $templateRepository
     * @param NodeManager                 $nodeManager
     * @param string                      $areaClass
     */
    public function __construct($nodeClass, TemplateRepositoryInterface $templateRepository, NodeManager $nodeManager, $areaClass)
    {
        $this->nodeClass = $nodeClass;
        $this->nodeManager = $nodeManager;
        $this->templateRepository = $templateRepository;
        $this->areaClass = $areaClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'php_orchestra_backoffice.form.node.name',
                'attr' => array(
                    'class' => 'alias-source',
                )
            ))
            ->add('sitemap_changefreq', 'choice', array(
                'label' => 'php_orchestra_backoffice.form.node.changefreq.title',
                'choices' => array(
                    'always' => 'php_orchestra_backoffice.form.node.changefreq.always',
                    'hourly' => 'php_orchestra_backoffice.form.node.changefreq.hourly',
                    'daily' => 'php_orchestra_backoffice.form.node.changefreq.daily',
                    'weekly' => 'php_orchestra_backoffice.form.node.changefreq.weekly',
                    'monthly' => 'php_orchestra_backoffice.form.node.changefreq.monthly',
                    'yearly' => 'php_orchestra_backoffice.form.node.changefreq.yearly',
                    'never' => 'php_orchestra_backoffice.form.node.changefreq.never'
                )
            ))
            ->add('sitemap_priority', 'text', array(
                'label' => 'php_orchestra_backoffice.form.node.priority',
            ))
            ->add('theme', 'orchestra_theme_choice', array(
                'label' => 'php_orchestra_backoffice.form.node.theme'
            ))
            ->add('alias', 'text', array(
                'label' => 'php_orchestra_backoffice.form.node.alias',
                'required' => false,
                'attr' => array(
                    'class' => 'alias-dest',
                )
            ))
            ->add('inMenu', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.node.in_menu',
                'required' => false
            ))
            ->add('inFooter', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.node.in_footer',
                'required' => false
            ))
            ->add('metaKeywords', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.meta_keywords',
                'required' => false,
            ))
            ->add('metaDescription', 'text', array(
                'label' => 'php_orchestra_backoffice.form.website.meta_description',
                'required' => false,
            ))
            ->add('metaIndex', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.website.meta_index',
                'required' => false,
            ))
            ->add('metaFollow', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.website.meta_follow',
                'required' => false,
            ))
            ->add('nodeId', 'hidden', array(
                'disabled' => true
            ))
            ->add('role', 'orchestra_role_choice', array(
                'label' => 'php_orchestra_backoffice.form.node.role',
                'required' => false,
            ));

        $builder->addEventSubscriber(new NodeChoiceSubscriber($this->nodeManager));
        $builder->addEventSubscriber(new TemplateChoiceSubscriber($this->templateRepository));
        $builder->addEventSubscriber(new AreaCollectionSubscriber($this->areaClass));
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
