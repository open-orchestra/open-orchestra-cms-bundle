<?php

namespace OpenOrchestra\MediaAdminBundle\Form\Type;

use OpenOrchestra\MediaAdminBundle\Form\DataTransformer\OrchestraMediaTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OrchestraMediaType
 */
class OrchestraMediaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new OrchestraMediaTransformer());
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_media';
    }
}
