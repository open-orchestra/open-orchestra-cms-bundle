<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\SiteType;

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
    protected $formEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn('foo');

        $this->formEvent = Phake::mock('Symfony\Component\Form\FormEvent');

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

        Phake::verify($builder, Phake::times(11))->add(Phake::anyParameters());
        Phake::verify($builder)->addEventListener(Phake::anyParameters());
        Phake::verify($this->translator, Phake::times(3))->trans(Phake::anyParameters());
        Phake::verify($builder)->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * @param string $siteId
     * @param array $options
     *
     * @dataProvider generateOptions
     */
    public function testOnPreSetData($siteId, $options)
    {
        $form = Phake::mock('Symfony\Component\Form\Form');
        $site = Phake::mock('PHPOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->formEvent)->getForm()->thenReturn($form);
        Phake::when($this->formEvent)->getData()->thenReturn($site);
        Phake::when($site)->getSiteId()->thenReturn($siteId);

        $this->form->onPreSetData($this->formEvent);

        Phake::verify($form)->add('siteId', 'text', $options);
    }

    /**
     * @return array
     */
    public function generateOptions()
    {
        return array(
            array(null, array(
                'label' => 'php_orchestra_backoffice.form.website.site_id',
                'attr' => array('class' => 'site-id-dest')
            )),
            array('siteId', array(
                'label' => 'php_orchestra_backoffice.form.website.site_id',
                'attr' => array('class' => 'site-id-dest'),
                'disabled' => true
            ))
        );
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
