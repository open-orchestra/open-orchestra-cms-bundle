<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\OrchestraBlockCheckBoxType;

/**
 * Class OrchestraBlockCheckBoxType
 */
class OrchestraBlockCheckBoxTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraBlockCheckBoxType
     */
    protected $blockCheckBoxType;
    protected $stringToBooleanTransformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->stringToBooleanTransformer = Phake::mock('PHPOrchestra\BackofficeBundle\Form\DataTransformer\StringToBooleanTransformer');

        $this->blockCheckBoxType = new OrchestraBlockCheckBoxType($this->stringToBooleanTransformer);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->blockCheckBoxType);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('orchestra_block_checkbox', $this->blockCheckBoxType->getName());
    }

    /**
     * Test parent
     */
    public function testParent()
    {
        $this->assertSame('checkbox', $this->blockCheckBoxType->getParent());
    }

    /**
     * Test buildForm
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');

        $this->blockCheckBoxType->buildForm($builder, array());

        Phake::verify($builder)->addModelTransformer($this->stringToBooleanTransformer);
    }
}
