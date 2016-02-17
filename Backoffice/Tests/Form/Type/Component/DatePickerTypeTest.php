<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\Form\Type\Component\DatePickerType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class DatePickerTypeTest
 */
class DatePickerTypeTest extends AbstractBaseTestCase
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
