<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type\Component;

use OpenOrchestra\BackofficeBundle\Form\Type\Component\ColumnLayoutRowType;
use Phake;

/**
 * Class ColumnLayoutRowTypeTest
 */
class ColumnLayoutRowTypeTest extends \PHPUnit_Framework_TestCase
{

    protected $columnLayoutType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->columnLayoutType = new ColumnLayoutRowType();
    }

    /**
     * test the build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->columnLayoutType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(1))->add(Phake::anyParameters());
    }

}
