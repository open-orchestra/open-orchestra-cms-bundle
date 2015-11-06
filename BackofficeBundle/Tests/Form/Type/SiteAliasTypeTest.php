<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\SiteAliasType;

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
        $this->assertSame('oo_site_alias', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(5))->add(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->siteAliasClass
        ));
    }
}
