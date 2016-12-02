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
use OpenOrchestra\Backoffice\Form\DataTransformer\CsvToReferenceKeywordTransformer;
use OpenOrchestra\Backoffice\Form\DataTransformer\ConditionToReferenceKeywordTransformer;

/**
 * Class KeywordsChoiceType
 */
class KeywordsChoiceType extends AbstractType
{
    protected $csvToReferenceKeywordTransformer;
    protected $conditionToReferenceKeywordTransformer;
    protected $keywordRepository;
    protected $router;
    protected $authorizationChecker;

    /**
     * @param CsvToReferenceKeywordTransformer       $csvToReferenceKeywordTransformer
     * @param ConditionToReferenceKeywordTransformer $conditionToReferenceKeywordTransformer
     * @param KeywordRepositoryInterface             $keywordRepository
     * @param RouterInterface                        $router
     * @param AuthorizationCheckerInterface          $authorizationChecker
     */
    public function __construct(
        CsvToReferenceKeywordTransformer $csvToReferenceKeywordTransformer,
        ConditionToReferenceKeywordTransformer $conditionToReferenceKeywordTransformer,
        KeywordRepositoryInterface $keywordRepository,
        RouterInterface $router,
        AuthorizationCheckerInterface $authorizationChecker
    ){
        $this->csvToReferenceKeywordTransformer = $csvToReferenceKeywordTransformer;
        $this->conditionToReferenceKeywordTransformer = $conditionToReferenceKeywordTransformer;
        $this->keywordRepository = $keywordRepository;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @throws NotAllowedClassNameException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['is_condition']) {
            $builder->addModelTransformer($this->conditionToReferenceKeywordTransformer);
        }
        else {
            $builder->addModelTransformer($this->csvToReferenceKeywordTransformer);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // @todo Update when voter for create and access is created
        //$isGranted = $this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_CREATE_KEYWORD);
        $isGranted = true;

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
            'is_condition' => false,
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
            $tags[] = array('id' => $tag->getId(), 'text' => $tag->getLabel(), 'type' => 'tag');
        }

        return json_encode($tags);
    }
}
