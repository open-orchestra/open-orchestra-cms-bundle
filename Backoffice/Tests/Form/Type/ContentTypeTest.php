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
    protected $statusableChoiceStatusSubscriber;
    protected $contentClass = 'content';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeSubscriber = Phake::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $this->statusableChoiceStatusSubscriber = Phake::mock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $this->form = new ContentType($this->contentTypeSubscriber, $this->statusableChoiceStatusSubscriber, $this->contentClass);
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

        $this->form->buildForm($builder, array(
            'is_blocked_edition' => true,
            'need_link_to_site_defintion' => true,
            'is_statusable' => true,
        ));

        Phake::verify($builder, Phake::times(5))->add(Phake::anyParameters());
        Phake::verify($builder, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test builder with content no statusable
     */
    public function testBuilderNoStatusable()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array(
            'is_blocked_edition' => true,
            'need_link_to_site_defintion' => true,
            'is_statusable' => false,
        ));

        Phake::verify($builder, Phake::times(5))->add(Phake::anyParameters());
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
            'is_blocked_edition' => false,
            'need_link_to_site_defintion' => false,
            'is_statusable' => false,
            'delete_button' => false,
            'new_button' => false,
                'group_enabled' => true,
                'group_render' => array(
                    'property' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content.group.property',
                    ),
                    'data' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.content.group.data',
                    ),
                ),
                'sub_group_render' => array(
                    'information' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content.sub_group.information',
                    ),
                    'publication' => array(
                        'rank' => 1,
                        'label' => 'open_orchestra_backoffice.form.content.sub_group.publication',
                    ),
                    'data' => array(
                        'rank' => 0,
                        'label' => 'open_orchestra_backoffice.form.content.sub_group.data',
                    ),
                ),
        ));
    }

}
