<?php

namespace OpenOrchestra\BackofficeBundle\Test\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\OnOffType;
use Phake;

/**
 * Test OnOffTypeTest
 */
class OnOffTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OnOffType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new OnOffType();
    }

    /**
     * Test resolver
     */
    public function testSetDefaultOption()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'required' => false
        ));
    }

    /**
     * Test parent
     */
    public function testParent()
    {
        $this->assertSame('checkbox', $this->form->getParent());
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('on_off', $this->form->getName());
    }
}
