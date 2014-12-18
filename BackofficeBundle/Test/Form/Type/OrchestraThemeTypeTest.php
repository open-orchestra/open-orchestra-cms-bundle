<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\OrchestraThemeType;

/**
 * Class OrchestraStatusTypeTest
 */
class OrchestraThemeTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrchestraThemeType
     */
    protected $form;

    protected $builder;
    protected $themeClass = 'themeClass';

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');

        $this->form = new OrchestraThemeType($this->themeClass);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('orchestra_theme', $this->form->getName());
    }

    /**
     * Test Parent
     */
    public function testParent()
    {
        $this->assertSame('document', $this->form->getParent());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'class' => $this->themeClass,
            'property' => 'name',
        ));
    }
}
