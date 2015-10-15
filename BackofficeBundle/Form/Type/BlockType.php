<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use OpenOrchestra\BackofficeBundle\Form\DataTransformer\BlockToArrayTransformer;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            'label' => 'open_orchestra_backoffice.form.block.label',
            'constraints' => new NotBlank(),
        ));
        $builder->add('class', 'text', array(
            'label' => 'open_orchestra_backoffice.form.block.class',
            'required' => false,
        ));
        $builder->add('id', 'text', array(
            'label' => 'open_orchestra_backoffice.form.block.id',
            'required' => false
        ));
        $builder->add('maxAge', 'integer', array(
            'label' => 'open_orchestra_backoffice.form.block.max_age',
            'required' => false,
        ));

        $builder->setAttribute('template', $this->generateFormManager->getTemplate($options['data']));

        $builder->addViewTransformer(new BlockToArrayTransformer());
        $builder->addEventSubscriber(
            new BlockTypeSubscriber(
                $this->generateFormManager, $this->fixedParameters, $this->formFactory, $options['blockPosition']
            )
        );
        if (array_key_exists('disabled', $options)) {
            $builder->setAttribute('disabled', $options['disabled']);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
