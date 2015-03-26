<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraColorChoiceType;

/**
 * Class OrchestraColorChoiceTypeTest
 */
class OrchestraColorChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraColorChoiceType
     */
    protected $orchestraColorChoiceType;

    protected $translator;
    protected $translation = 'string';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->translation);

        $this->orchestraColorChoiceType = new OrchestraColorChoiceType($this->translator);
    }

    /**
     * test default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->orchestraColorChoiceType->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(
            array('choices' => array(
                'red' => $this->translation,
                'orange' => $this->translation,
                'green' => $this->translation,
            ))
        );
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('choice', $this->orchestraColorChoiceType->getParent());
    }

    /**
     * test Name
     */
    public function testGetName()
    {
        $this->assertEquals('orchestra_color_choice', $this->orchestraColorChoiceType->getName());
    }
}
