<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraChoiceType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Description of OrchestraChoiceTypeTest
 */
class OrchestraChoiceTypeTest extends AbstractBaseTestCase
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
     * test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->orchestraChoiceType->configureOptions($resolverMock);

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
