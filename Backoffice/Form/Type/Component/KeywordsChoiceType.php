<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\OptionsResolver\Options;
use OpenOrchestra\Backoffice\Exception\NotAllowedClassNameException;
use OpenOrchestra\Backoffice\Form\DataTransformer\ReferencedKeywordsToKeywordsTransformer;
use OpenOrchestra\Backoffice\Manager\KeywordToDocumentManager;

/**
 * Class KeywordsChoiceType
 */
class KeywordsChoiceType extends AbstractType
{
    protected $keywordsTransformer;
    protected $keywordRepository;
    protected $router;
    protected $authorizationChecker;

    /**
     * @param ReferencedKeywordsToKeywordsTransformer $keywordsTransformer
     * @param KeywordRepositoryInterface              $keywordRepository
     * @param KeywordToDocumentManager                $keywordToDocumentManager,
     * @param RouterInterface                         $router
     * @param AuthorizationCheckerInterface           $authorizationChecker
     */
    public function __construct(
        ReferencedKeywordsToKeywordsTransformer $keywordsTransformer,
        KeywordRepositoryInterface $keywordRepository,
        KeywordToDocumentManager $keywordToDocumentManager,
        RouterInterface $router,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->keywordsTransformer = $keywordsTransformer;
        $this->keywordRepository = $keywordRepository;
        $this->keywordToDocumentManager = $keywordToDocumentManager;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @throws NotAllowedClassNameException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!is_null($options['transformerClass'])) {
            if(!is_string($options['transformerClass']) || !is_subclass_of($options['transformerClass'], 'OpenOrchestra\ModelInterface\Form\DataTransformer\ConditionFromBooleanToBddTransformerInterface')) {
                throw new NotAllowedClassNameException();
            }
            $transformerClass = $options['transformerClass'];
            $transformer = new $transformerClass($this->keywordToDocumentManager, $this->keywordRepository);
            $transformer->setField($options['name']);
            $builder->addModelTransformer($transformer);
        }
        else {
            $builder->addModelTransformer($this->keywordsTransformer);
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
                    'data-check' => $this->router->generate('open_orchestra_api_check_keyword', array()),
                );
                return array_replace($default, $options['new_attr']);
            },
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
