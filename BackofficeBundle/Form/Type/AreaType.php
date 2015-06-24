<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Transformer\HtmlIdTransformer;
use OpenOrchestra\BackofficeBundle\Transformer\HtmlClassTransformer;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use OpenOrchestra\BackofficeBundle\EventSubscriber\AreaCollectionSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TemplateAreaType
 */
class AreaType extends AbstractType
{
    protected $areaClass;
    protected $translator;

    /**
     * @param string              $areaClass
     * @param TranslatorInterface $translator
     */
    public function __construct($areaClass, TranslatorInterface $translator)
    {
        $this->areaClass = $areaClass;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $htmlClassTransformer = new HtmlClassTransformer($options['data']);
        $htmlIdTransformer = new HtmlIdTransformer($options['data']);

        $builder->add('label', 'text', array(
            'label' => 'open_orchestra_backoffice.form.area.label',
            'required' => false,
        ));

        $builder->add(
            $builder->create('areaId', 'text', array('label' => 'open_orchestra_backoffice.form.area.area_id'))
                ->addViewTransformer($htmlIdTransformer)
        );
        $builder->add(
            $builder->create('htmlClass', 'text', array(
                'required' => false,
                'label' => 'open_orchestra_backoffice.form.area.html_class'
            ))
                ->addViewTransformer($htmlClassTransformer)
        );
        $builder->add('boDirection', 'choice', array(
            'choices' => array('v' => 'vertical', 'h' => 'horizontal'),
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.area.bo_direction'
        ));
        $builder->add('boPercent', 'text', array(
            'required' => false,
            'label' => 'open_orchestra_backoffice.form.area.bo_percent'
        ));
        if(!array_key_exists('disabled', $options) || $options['disabled'] === false){
            $builder->addEventSubscriber(new AreaCollectionSubscriber($this->areaClass, $this->translator));
            $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->areaClass,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'area';
    }
}
