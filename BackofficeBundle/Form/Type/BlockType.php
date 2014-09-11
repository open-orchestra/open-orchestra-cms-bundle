<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\BlockTypeSubscriber;
use PHPOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BlockType
 */
class BlockType extends AbstractType
{
    protected $generateFormManager;
    protected $translator;

    /**
     * @param GenerateFormManager $generateFormManager
     */
    public function __construct(GenerateFormManager $generateFormManager, TranslatorInterface $translator)
    {
        $this->generateFormManager = $generateFormManager;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'component',
            'orchestra_block',
            array('label' => $this->translator->trans('php_orchestra_backoffice.block.component'))
        );
        $builder->addEventSubscriber(new BlockTypeSubscriber($this->generateFormManager));
        $builder->addEventSubscriber(new AddSubmitButtonSubscriber());
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
