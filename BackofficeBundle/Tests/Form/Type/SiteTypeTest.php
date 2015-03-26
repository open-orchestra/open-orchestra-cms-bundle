<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\SiteType;

/**
 * Class SiteTypeTest
 */
class SiteTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SiteType
     */
    protected $form;

    protected $siteClass = 'site';
    protected $translator;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn('foo');

        $this->form = new SiteType($this->siteClass, $this->translator);
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
        $this->assertSame('site', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(12))->add(Phake::anyParameters());
        Phake::verify($this->translator, Phake::times(3))->trans(Phake::anyParameters());
        Phake::verify($builder, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testSetDefaultOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->siteClass
        ));
    }
}
