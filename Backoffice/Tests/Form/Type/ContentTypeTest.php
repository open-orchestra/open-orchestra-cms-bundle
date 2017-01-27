<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\ContentType;

/**
 * Class ContentTypeTest
 */
class ContentTypeTest extends AbstractBaseTestCase
{
    /**
     * @var ContentType
     */
    protected $form;

    protected $contentTypeSubscriber;
    protected $contentClass = 'content';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeSubscriber = Phake::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $this->form = new ContentType($this->contentTypeSubscriber, $this->contentClass);
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
        $this->assertSame('oo_content', $this->form->getName());
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

        Phake::verify($builder, Phake::times(2))->add(Phake::anyParameters());
        Phake::verify($builder, Phake::times(1))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->contentClass,
            'delete_button' => false,
            'new_button' => false,
        ));
    }

}
