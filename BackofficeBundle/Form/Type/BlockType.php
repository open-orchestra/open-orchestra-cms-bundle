<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use PHPOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BlockType
 */
class BlockType extends AbstractType
{
    protected $generateFormManager;
    protected $fixedParams;

    /**
     * @param GenerateFormManager $generateFormManager
     * @param array               $fixedParams
     */
    public function __construct(GenerateFormManager $generateFormManager, $fixedParams)
    {
        $this->generateFormManager = $generateFormManager;
        $this->fixedParams = $fixedParams;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new BlockTypeSubscriber($this->generateFormManager, $this->fixedParams, $options['blockPosition']));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'blockPosition' => 0
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
