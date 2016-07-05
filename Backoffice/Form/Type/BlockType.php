<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\Form\DataTransformer\BlockToArrayTransformer;
use OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class BlockType
 */
class BlockType extends AbstractType
{
    protected $generateFormManager;
    protected $blockToArrayTransformer;
    protected $blockFormTypeSubscriber;

    /**
     * @param GenerateFormManager      $generateFormManager
     * @param BlockToArrayTransformer  $blockToArrayTransformer
     * @param EventSubscriberInterface $blockFormTypeSubscriber
     */
    public function __construct(
        GenerateFormManager $generateFormManager,
        BlockToArrayTransformer $blockToArrayTransformer,
        EventSubscriberInterface $blockFormTypeSubscriber
    ) {
        $this->generateFormManager = $generateFormManager;
        $this->blockToArrayTransformer = $blockToArrayTransformer;
        $this->blockFormTypeSubscriber = $blockFormTypeSubscriber;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'text', array(
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

        $builder->addViewTransformer($this->blockToArrayTransformer);
        $builder->addEventSubscriber($this->blockFormTypeSubscriber);

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
        return 'oo_block';
    }

}
