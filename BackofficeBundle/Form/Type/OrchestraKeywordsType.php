<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PHPOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer;
use Symfony\Component\Routing\Router;

/**
 * Class OrchestraKeywordsType
 */
class OrchestraKeywordsType extends AbstractType
{
    protected $keywordsTransformer;
    protected $keywordRepository;
    protected $router;

    /**
     * @param EmbedKeywordsToKeywordsTransformer $keywordsTransformer
     * @param KeywordRepositoryInterface         $keywordRepository
     * @param Router                             $router
     */
    public function __construct(
        EmbedKeywordsToKeywordsTransformer $keywordsTransformer,
        KeywordRepositoryInterface $keywordRepository,
        Router $router
    )
    {
        $this->keywordsTransformer = $keywordsTransformer;
        $this->keywordRepository = $keywordRepository;
        $this->router = $router;
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
            'data-check' => $this->router->generate('php_orchestra_api_check_keyword', array()),
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
