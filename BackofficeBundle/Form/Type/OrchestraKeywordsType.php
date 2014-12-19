<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PHPOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer;

/**
 * Class OrchestraKeywordsType
 */
class OrchestraKeywordsType extends AbstractType
{
    protected $keywordsTransformer;
    protected $keywordRepository;

    /**
     * @param EmbedKeywordsToKeywordsTransformer $keywordsTransformer
     * @param KeywordRepositoryInterface $keywordRepository
     */
    public function __construct(EmbedKeywordsToKeywordsTransformer $keywordsTransformer, KeywordRepositoryInterface $keywordRepository)
    {
        $this->keywordsTransformer = $keywordsTransformer;
        $this->keywordRepository = $keywordRepository;
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
        $resolver->setDefaults(array('attr' => array(
            'class' => 'select2',
            'data-tags' => $this->getTags(),
        )));
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
        return 'orchestra_keywords';
    }

    /**
     * @return string
     */
    protected function getTags()
    {
        $keywords = $this->keywordRepository->findAll();
        $tags = array();
        foreach ($keywords as $tag) {
            $tags[] = $tag->getLabel();
        }

        return json_encode($tags);
    }
}
