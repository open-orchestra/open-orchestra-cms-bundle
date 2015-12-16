<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\DatePickerType;
use Phake;

/**
 * Class DatePickerTypeTest
 */
class DatePickerTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new DatePickerType();
    }

    /**
     * test Instance
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\Extension\Core\Type\DateType', $this->form);
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_date_picker', $this->form->getName());
    }
}
