<?php

namespace OpenOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraMediaType;

/**
 * Class OrchestraMediaTypeTest
 */
class OrchestraMediaTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraMediaType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new OrchestraMediaType();
    }
    
    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }
    
    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('orchestra_media', $this->form->getName());
    }
    
    /**
     * Test parent
     */
    public function testParent()
    {
        $this->assertSame('text', $this->form->getParent());
    }
    
    /**
     * Test add dataTransformer
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');

        $this->form->buildForm($builder, array());

        Phake::verify($builder)->addModelTransformer(Phake::anyParameters());
    }
}
