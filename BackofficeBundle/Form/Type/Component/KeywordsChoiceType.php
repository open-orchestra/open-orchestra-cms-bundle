<?php

namespace OpenOrchestra\BackofficeBundle\Form\Type\Component;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\OptionsResolver\Options;
use OpenOrchestra\Backoffice\Exception\NotAllowedClassNameException;

/**
 * Class KeywordsChoiceType
 */
class KeywordsChoiceType extends AbstractType
{
    protected $keywordsTransformer;
    protected $keywordRepository;
    protected $router;

    /**
     * @param EmbedKeywordsToKeywordsTransformer $keywordsTransformer
     * @param KeywordRepositoryInterface         $keywordRepository
     * @param RouterInterface                    $router
     * @param AuthorizationCheckerInterface      $authorizationChecker
     */
    public function __construct(
        EmbedKeywordsToKeywordsTransformer $keywordsTransformer,
        KeywordRepositoryInterface $keywordRepository,
        RouterInterface $router,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->keywordsTransformer = $keywordsTransformer;
        $this->keywordRepository = $keywordRepository;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['embedded']) {
            $builder->addModelTransformer($this->keywordsTransformer);
        }
        if (!is_null($options['transformerClass'])) {
            if(!is_string($options['transformerClass']) || !is_subclass_of($options['transformerClass'], 'OpenOrchestra\Transformer\ConditionFromBooleanToBddTransformer')) {
                throw new NotAllowedClassNameException();
            }
            $transformerClass = $options['transformerClass'];
            $transformer = new $transformerClass($options['name']);
            $builder->addModelTransformer($transformer);
        }

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $isGranted = $this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_KEYWORD);

        $resolver->setDefaults(array(
            'attr' => function(Options $options) use ($isGranted) {
                $default = array(
                    'class' => 'select2',
                    'data-tags' => $this->getTags(),
                    'data-authorize-new' => ($isGranted) ? "true" : "false",
                );
                return array_replace($default, $options['new_attr']);
            },
            'embedded' => true,
            'name' => '',
            'new_attr' => array(),
            'transformerClass' => null,
        ));
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
        return 'oo_keywords_choice';
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
