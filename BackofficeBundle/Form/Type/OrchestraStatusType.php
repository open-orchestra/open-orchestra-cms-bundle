<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\Form\DataTransformer\EmbedToStatusTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraStatusType
 */
class OrchestraStatusType extends AbstractType
{
    protected $statusTransformer;

    /**
     * @param EmbedToStatusTransformer $statusTransformer
     */
    public function __construct(EmbedToStatusTransformer $statusTransformer)
    {
        $this->statusTransformer = $statusTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->statusTransformer);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => 'PHPOrchestra\ModelBundle\Document\Status',
                'property' => 'labels',
            )
        );
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'document';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'orchestra_status';
    }
}
