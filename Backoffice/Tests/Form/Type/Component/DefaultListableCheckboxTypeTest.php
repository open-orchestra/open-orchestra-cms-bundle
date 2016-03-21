<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\Backoffice\Form\Type\Component\DefaultListableCheckboxType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Phake;

/**
 * Class DefaultListableCheckboxTypeTest
 */
class DefaultListableCheckboxTypeTest extends AbstractBaseTestCase
{
    /**
     * @var DefaultListableCheckboxType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new DefaultListableCheckboxType();
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals(CheckboxType::class, $this->form->getParent());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_default_listable_checkbox', $this->form->getName());
    }

    /**
     * @param $name
     * @param $label
     *
     * @dataProvider provideNameAndExpectedLabel
     */
    public function testBuildView($name, $label)
    {
        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $formView->vars['name'] = $name;
        $formView->vars['label'] = null;
        $options = array();

        $this->form->buildView($formView, $formInterface, $options);
        $this->assertEquals($formView->vars['label'], $label);
    }

    /**
     * @return array
     */
    public function provideNameAndExpectedLabel()
    {
        return array(
            "Basic label" => array('name', 'open_orchestra_backoffice.form.content_type.default_listable_label.name'),
            "Other label" => array('label2', 'open_orchestra_backoffice.form.content_type.default_listable_label.label2'),
            "null name" => array(null, null),
            "empty name" => array('', null),
        );
    }
}
