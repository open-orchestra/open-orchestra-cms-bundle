<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\OrchestraVideoType;

/**
 * Class OrchestraVideoTypeTest
 */
class OrchestraVideoTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraVideoType
     */
    protected $form;

    protected $videoUrltoId;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->videoUrltoId = Phake::mock('PHPOrchestra\BackofficeBundle\Form\DataTransformer\VideoUrlToIdTransformer');

        $this->form = new OrchestraVideoType($this->videoUrltoId);
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
        $this->assertSame('orchestra_video', $this->form->getName());
    }

    /**
     * Test parent
     */
    public function testParent()
    {
        $this->assertSame('text', $this->form->getParent());
    }

    /**
     * Test buildForm
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');

        $this->form->buildForm($builder, array());

        Phake::verify($builder)->addViewTransformer($this->videoUrltoId);
    }
}
