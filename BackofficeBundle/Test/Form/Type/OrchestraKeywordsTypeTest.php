<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\OrchestraKeywordsType;

/**
 * Class OrchestraKeywordsTypeTest
 */
class OrchestraKeywordsTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraKeywordsType
     */
    protected $form;

    protected $builder;
    protected $keyword1;
    protected $keyword2;
    protected $keywords;
    protected $transformer;
    protected $keywordRepository;

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->keyword1 = Phake::mock('PHPOrchestra\ModelInterface\Model\KeywordInterface');
        $this->keyword2 = Phake::mock('PHPOrchestra\ModelInterface\Model\KeywordInterface');
        $this->keywords = new ArrayCollection();
        $this->keywords->add($this->keyword1);
        $this->keywords->add($this->keyword2);
        $this->keywordRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\KeywordRepository');
        Phake::when($this->keywordRepository)->findAll()->thenReturn($this->keywords);

        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->transformer = Phake::mock('PHPOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer');

        $this->form = new OrchestraKeywordsType($this->transformer, $this->keywordRepository);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('orchestra_keywords', $this->form->getName());
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
    public function testSetDefaultOptions($tagLabel)
    {
        Phake::when($this->keyword1)->getLabel()->thenReturn($tagLabel);
        Phake::when($this->keyword2)->getLabel()->thenReturn($tagLabel);

        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array( 'attr' => array(
            'class' => 'select2',
            'data-tags' => json_encode(array($tagLabel, $tagLabel))
        )));
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
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->addModelTransformer($this->transformer);
    }
}
