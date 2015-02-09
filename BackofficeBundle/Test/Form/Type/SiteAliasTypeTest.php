<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\SiteAliasType;

/**
 * Class SiteAliasTypeTest
 */
class SiteAliasTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SiteAliasType
     */
    protected $form;

    protected $siteAliasClass = 'site_alias';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new SiteAliasType($this->siteAliasClass);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('site_alias', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(3))->add(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testSetDefaultOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->siteAliasClass
        ));
    }
}
