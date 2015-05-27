<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\ContentTypeType;
use Symfony\Component\Form\FormEvents;

/**
 * Class ContentTypeTypeTest
 */
class ContentTypeTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    protected $translator;
    protected $class = 'content_type';
    protected $translateValueInitializer;
    protected $translatedLabel = 'existing option';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translateValueInitializer = Phake::mock('OpenOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener');

        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->translatedLabel);

        $this->form = new ContentTypeType($this->class, $this->translator, $this->translateValueInitializer);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('content_type', $this->form->getName());
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

        Phake::verify($builder, Phake::times(4))->add(Phake::anyParameters());
        Phake::verify($builder, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.add');
        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.new');
        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.delete');
        Phake::verify($builder)->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this->translateValueInitializer, 'preSetData')
        );
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->class,
        ));
    }
}
