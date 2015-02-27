<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use OpenOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use OpenOrchestra\BackofficeBundle\Form\DataTransformer\BlockToArrayTransformer;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BlockType
 */
class BlockType extends AbstractType
{
    protected $generateFormManager;
    protected $fixedParams;
    protected $formFactory;

    /**
     * @param GenerateFormManager $generateFormManager
     * @param array               $fixedParams
     * @param FormFactory         $formFactory
     */
    public function __construct(GenerateFormManager $generateFormManager, $fixedParams, FormFactory $formFactory)
    {
        $this->generateFormManager = $generateFormManager;
        $this->fixedParams = $fixedParams;
        $this->formFactory = $formFactory;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label');
        $builder->add('class', 'text', array(
            'required' => false,
        ));
        $builder->add('id', 'text', array(
            'required' => false
        ));

        $builder->setAttribute('template', $this->generateFormManager->getTemplate($options['data']));

        $builder->addViewTransformer(new BlockToArrayTransformer());
        $builder->addEventSubscriber(new BlockTypeSubscriber($this->generateFormManager, $this->fixedParams, $this->formFactory, $options['blockPosition']));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
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
