<?php

namespace OpenOrchestra\Backoffice\GenerateForm;

use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
     * Get the required uri parameter
     *
     * @return array
     */
    public function getRequiredUriParameter();

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @return string
     */
    public function getName();
}
