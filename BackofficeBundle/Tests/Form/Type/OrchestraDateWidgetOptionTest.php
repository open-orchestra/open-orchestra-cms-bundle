<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraDateWidgetOption;
use Phake;

/**
 * Class OrchestraDateWidgetOptionTest
 */
class OrchestraDateWidgetOptionTest extends \PHPUnit_Framework_TestCase
{
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new OrchestraDateWidgetOption();
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

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
        $this->assertEquals('orchestra_date_widget_option', $this->form->getName());
    }
}
