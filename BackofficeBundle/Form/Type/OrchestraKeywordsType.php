<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BaseBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PHPOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer;

/**
 * Class OrchestraKeywordsType
 */
class OrchestraKeywordsType extends AbstractType
{
    protected $keywordsTransformer;

    /**
     * @param EmbedKeywordsToKeywordsTransformer $keywordsTransformer
     */
    public function __construct(EmbedKeywordsToKeywordsTransformer $keywordsTransformer)
    {
        $this->keywordsTransformer = $keywordsTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->keywordsTransformer);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => 'PHPOrchestra\ModelBundle\Document\Keyword',
                'property' => 'label',
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
        return 'orchestra_keywords';
    }
}
