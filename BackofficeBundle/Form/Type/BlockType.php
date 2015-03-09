<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use OpenOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use OpenOrchestra\BackofficeBundle\Form\DataTransformer\BlockToArrayTransformer;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BlockType
 */
class BlockType extends AbstractType
{
    protected $generateFormManager;
    protected $fixedParameters;
    protected $formFactory;

    /**
     * @param GenerateFormManager  $generateFormManager
     * @param array                $fixedParameters
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(GenerateFormManager $generateFormManager, $fixedParameters, FormFactoryInterface $formFactory)
    {
        $this->generateFormManager = $generateFormManager;
        $this->fixedParameters = $fixedParameters;
        $this->formFactory = $formFactory;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', null, array(
            'label' => 'open_orchestra_backoffice.form.block.label'
        ));
        $builder->add('class', 'text', array(
            'label' => 'open_orchestra_backoffice.form.block.class',
            'required' => false,
        ));
        $builder->add('id', 'text', array(
            'label' => 'open_orchestra_backoffice.form.block.id',
            'required' => false
        ));
        $builder->add('max_age', 'integer', array(
            'label' => 'open_orchestra_backoffice.form.block.max_age',
            'required' => false,
            'empty_data' => 0
        ));

        $builder->setAttribute('template', $this->generateFormManager->getTemplate($options['data']));

        $builder->addViewTransformer(new BlockToArrayTransformer());
        $builder->addEventSubscriber(new BlockTypeSubscriber($this->generateFormManager, $this->fixedParameters, $this->formFactory, $options['blockPosition']));
        if(!array_key_exists('disabled', $options) || $options['disabled'] == false){
            $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'blockPosition' => 0,
                'data_class' => null,
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'block';
    }

}
