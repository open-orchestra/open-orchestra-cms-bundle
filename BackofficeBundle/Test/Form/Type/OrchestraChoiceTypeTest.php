<?php

namespace OpenOrchestra\BackofficeBundle\Test\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraChoiceType;
use Phake;

/**
 * Description of OrchestraChoiceTypeTest
 */
class OrchestraChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $name;
    protected $choices;
    protected $orchestraChoiceType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->name = 'name';

        $this->choices = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        $this->orchestraChoiceType = new OrchestraChoiceType($this->choices, $this->name);
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->orchestraChoiceType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(
            array('choices' => $this->choices)
        );
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('choice', $this->orchestraChoiceType->getParent());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals($this->name, $this->orchestraChoiceType->getName());
    }
}
