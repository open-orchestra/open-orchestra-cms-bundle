<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type\Component;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\Component\KeywordsChoiceType;
use Symfony\Component\OptionsResolver\Options;

/**
 * Class KeywordsChoiceTypeTest
 */
class KeywordsChoiceTypeTest extends AbstractBaseTestCase
{
    /**
     * @var KeywordsChoiceTypeTest
     */
    protected $form;

    protected $router;
    protected $builder;
    protected $keyword1;
    protected $keyword2;
    protected $keywords;
    protected $transformer;
    protected $keywordRepository;
    protected $authorizationChecker;

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->keyword1 = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        $this->keyword2 = Phake::mock('OpenOrchestra\ModelInterface\Model\KeywordInterface');
        $this->keywords = new ArrayCollection();
        $this->keywords->add($this->keyword1);
        $this->keywords->add($this->keyword2);
        $this->keywordRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\KeywordRepositoryInterface');
        Phake::when($this->keywordRepository)->findAll()->thenReturn($this->keywords);

        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->transformer = Phake::mock('OpenOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer');

        $this->router = Phake::mock('Symfony\Component\Routing\RouterInterface');

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);

        $this->form = new KeywordsChoiceType($this->transformer, $this->keywordRepository, $this->router, $this->authorizationChecker);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('oo_keywords_choice', $this->form->getName());
    }

    /**
     * Test Parent
     */
    public function testParent()
    {
        $this->assertSame('text', $this->form->getParent());
    }

    /**
     * @param string $tagLabel
     *
     * @dataProvider provideTagLabel
     */
    public function testConfigureOptions($tagLabel)
    {
        $route = 'path';
        $isGranted = 1;
        Phake::when($this->router)->generate(Phake::anyParameters())->thenReturn($route);
        Phake::when($this->keyword1)->getLabel()->thenReturn($tagLabel);
        Phake::when($this->keyword2)->getLabel()->thenReturn($tagLabel);

        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'attr' => function(Options $options) use ($isGranted, $tagLabel) {
                $default = array(
                    'class' => 'select2',
                    'data-tags' => json_encode(array($tagLabel, $tagLabel)),
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
     * @return array
     */
    public function provideTagLabel()
    {
        return array(
            array('tag'),
            array('label'),
        );
    }

    /**
     * Test model transformer
     */
    public function testBuildForm()
    {
        $this->form->buildForm($this->builder, array('embedded' => true, 'transformerClass' => null));

        Phake::verify($this->builder)->addModelTransformer($this->transformer);
    }
}
