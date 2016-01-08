<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type\Component;

use OpenOrchestra\BackofficeBundle\Form\Type\Component\DateWidgetOptionType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class DateWidgetOptionTypeTest
 */
class DateWidgetOptionTypeTest extends AbstractBaseTestCase
{
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new DateWidgetOptionType();
    }

    /**
     * test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(
            array('choices' => array(
                'choice' => 'open_orchestra_backoffice.form.orchestra_fields.widget_type.choice',
                'text' => 'open_orchestra_backoffice.form.orchestra_fields.widget_type.text',
                'single_text' => 'open_orchestra_backoffice.form.orchestra_fields.widget_type.single_text',
            ))
        );
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('choice', $this->form->getParent());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_date_widget_option', $this->form->getName());
    }
}
