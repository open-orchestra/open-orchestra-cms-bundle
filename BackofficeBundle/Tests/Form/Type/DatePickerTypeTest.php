<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\DatePickerType;

/**
 * Class DatePickerTypeTest
 */
class DatePickerTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DatePickerType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new DatePickerType();
    }

    /**
     * test convert date format
     *
     * @param $dateFormat
     * @param $result
     *
     * @dataProvider provideDateFormat
     */
    public function testConvertDateFormat($dateFormat, $result)
    {
        $this->assertEquals($result, $this->form->convertDateFormat($dateFormat));
    }

    /**
     * @return array
     */
    public function provideDateFormat()
    {
        return array(
            array('dd-MM-yyyy', 'dd-mm-yy'),
            array('yyyy-MM-dd', 'yy-mm-dd'),
            array('yyyy', 'yy'),
            array('yy', 'y'),
            array('MMMM', 'MM'),
            array('MMM', 'M'),
            array('MM', 'mm'),
            array('M', 'm'),
            array('D', 'o'),
        );
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('date_picker', $this->form->getName());
    }
}
