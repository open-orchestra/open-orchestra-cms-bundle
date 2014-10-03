<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\Transformer\HtmlIdTransformer;
use PHPOrchestra\BackofficeBundle\Transformer\HtmlClassTransformer;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TemplateAreaType
 */
class TemplateAreaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $htmlClassTransformer = new HtmlClassTransformer($options['data']);
        $htmlIdTransformer = new HtmlIdTransformer($options['data']);

        $builder->add(
            $builder->create('areaId', 'text', array('label' => 'php_orchestra_backoffice.form.area.area_id'))
                ->addViewTransformer($htmlIdTransformer)
        );
        $builder->add(
            $builder->create('htmlClass', 'text', array(
                'required' => false,
                'label' => 'php_orchestra_backoffice.form.area.html_class'
            ))
                ->addViewTransformer($htmlClassTransformer)
        );
        $builder->add('boDirection', 'choice', array(
            'choices' => array('v' => 'vertical', 'h' => 'horizontal'),
            'required' => false,
            'label' => 'php_orchestra_backoffice.form.area.bo_direction'
        ));
        $builder->add('boPercent', 'text', array(
            'required' => false,
            'label' => 'php_orchestra_backoffice.form.area.bo_percent'
        ));
        $builder->addEventSubscriber(new AreaCollectionSubscriber());
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PHPOrchestra\ModelBundle\Document\Area',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'template_area';
    }
}
