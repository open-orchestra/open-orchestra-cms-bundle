<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use OpenOrchestra\GroupBundle\Form\Type\OrchestraGroupType;
use Phake;

/**
 * Test OrchestraGroupTypeTest
 */
class OrchestraGroupTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraGroupType
     */
    protected $type;

    protected $groupClass = 'groupClass';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->type = new OrchestraGroupType($this->groupClass);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('OpenOrchestra\BackofficeBundle\Form\Type\AbstractOrchestraGroupType', $this->type);
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->type);
    }

    /**
     * Test configure options
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->type->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'class' => $this->groupClass,
            'property' => 'name'
        ));
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertSame('document', $this->type->getParent());
    }

    /**
     * Test name
     */
    public function testGetName()
    {
        $this->assertSame('orchestra_group', $this->type->getName());
    }
}
