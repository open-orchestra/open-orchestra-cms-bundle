<?php

namespace PHPOrchestra\Backoffice\GenerateForm;

use PHPOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Interface GenerateFormInterface
 */
interface GenerateFormInterface
{
    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    public function support(BlockInterface $block);

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options);

    /**
     * Get the default configuration for the block
     *
     * @return array
     */
    public function getDefaultConfiguration();

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @return string
     */
    public function getName();
}
