<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Router;
use PHPOrchestra\CMSBundle\Form\DataTransformer\SiteTypeTransformer;

/**
 * Class SiteType
 */
class SiteType extends AbstractType
{
    protected $siteClass;

    /**
     * @param string $siteClass
     */
    public function __construct($siteClass)
    {
        $this->siteClass = $siteClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteId', 'integer')
            ->add('domain', 'text')
            ->add('alias', 'text')
            ->add('defaultLanguage', 'orchestra_language', array('label' => 'Default Language'))
            ->add('languages', 'orchestra_language', array('label' => 'Languages', 'multiple' => true))
            ->add('blocks', 'orchestra_block', array('multiple' => true));

        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->siteClass,
            )
        );
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'site';
    }
}
