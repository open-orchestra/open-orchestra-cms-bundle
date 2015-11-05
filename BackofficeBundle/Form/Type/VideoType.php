<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\DataTransformer\VideoUrlToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class VideoType
 */
class VideoType extends AbstractType
{
    protected $videoUrlTransformer;

    /**
     * @param VideoUrlToIdTransformer $videoUrlTransformer
     */
    public function __construct(VideoUrlToIdTransformer $videoUrlTransformer)
    {
        $this->videoUrlTransformer = $videoUrlTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->videoUrlTransformer);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_video';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }
}
