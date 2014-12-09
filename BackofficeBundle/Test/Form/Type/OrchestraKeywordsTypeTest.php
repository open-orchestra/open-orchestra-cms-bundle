<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

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
    protected $transformer;

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        $this->transformer = Phake::mock('PHPOrchestra\BackofficeBundle\Form\DataTransformer\EmbedKeywordsToKeywordsTransformer');

        $this->form = new OrchestraKeywordsType($this->transformer);
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
        $this->assertSame('document', $this->form->getParent());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'class' => 'PHPOrchestra\ModelBundle\Document\Keyword',
            'property' => 'label',
        ));
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
