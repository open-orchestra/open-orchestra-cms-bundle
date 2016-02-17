<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\VideoType;

/**
 * Class VideoTypeTest
 */
class VideoTypeTest extends AbstractBaseTestCase
{
    /**
     * @var VideoType
     */
    protected $form;

    protected $videoUrltoId;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->videoUrltoId = Phake::mock('OpenOrchestra\Backoffice\Form\DataTransformer\VideoUrlToIdTransformer');

        $this->form = new VideoType($this->videoUrltoId);
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
        $this->assertSame('oo_video', $this->form->getName());
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
