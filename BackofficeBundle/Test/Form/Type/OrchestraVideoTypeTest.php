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
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::never())->add(Phake::anyParameters());
        Phake::verify($builder, Phake::never())->addEventSubscriber(Phake::anyParameters());
        Phake::verify($builder, Phake::never())->addEventListener(Phake::anyParameters());
    }
}
