<?php

namespace OpenOrchestra\Workflow\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Workflow\Form\Type\StatusType;

/**
 * Class StatusTypeTest
 */
class StatusTypeTest extends AbstractBaseTestCase
{
    /**
     * @var StatusType
     */
    protected $form;

    protected $statusClass = 'status';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new StatusType($this->statusClass, array());
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
        $this->assertSame('oo_status', $this->form->getName());
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

        Phake::verify($builder, Phake::times(5))->add(Phake::anyParameters());
    }

    /**
     * test buildView
     *
     * @param boolean $isInitial
     * @param boolean $isTranslation
     * @param boolean $isPublished
     * @param boolean $isautoPublish
     * @param boolean $isAutounpublish
     * @param array   $expectedProperties
     *
     * @dataProvider getStatusSpecificities
     */
    public function testBuildView($isInitial, $isTranslation, $isPublished, $isautoPublish, $isAutounpublish, array $expectedProperties)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isInitialState()->thenReturn($isInitial);
        Phake::when($status)->isTranslationState()->thenReturn($isTranslation);
        Phake::when($status)->isPublishedState()->thenReturn($isPublished);
        Phake::when($status)->isAutoPublishFromState()->thenReturn($isautoPublish);
        Phake::when($status)->isAutoUnpublishToState()->thenReturn($isAutounpublish);

        $form = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($form)->getData()->thenReturn($status);

        $view = Phake::mock('Symfony\Component\Form\FormView');

        $options = array(
            'delete_button' => true,
            'new_button' => true,
            'delete_business_rules' => true,
            'business_rules_help_text' => 'test',
        );
        $this->form->buildView($view, $form, $options);
        $this->assertTrue($view->vars['delete_button']);
        $this->assertTrue($view->vars['new_button']);
        $this->assertSame(true, isset($view->vars['properties']));
        $this->assertSame($expectedProperties, $view->vars['properties']);
        $this->assertTrue($view->vars['delete_business_rules']);
        $this->assertSame('test', $view->vars['business_rules_help_text']);
    }

    /**
     * Provide status specificities
     *
     * @return array
     */
    public function getStatusSpecificities()
    {
        $initial       = 'open_orchestra_workflow_admin.form.status.properties.initial_state';
        $translation   = 'open_orchestra_workflow_admin.form.status.properties.translation_state';
        $published     = 'open_orchestra_workflow_admin.form.status.properties.published_state';
        $autoPublish   = 'open_orchestra_workflow_admin.form.status.properties.auto_publish_from_state';
        $autoUnpublish = 'open_orchestra_workflow_admin.form.status.properties.auto_unpublish_to_state';

        return array(
            array(false, false, false, false, false, array()),
            array(true , false, false, false, false, array($initial)),
            array(false, true , false, false, false, array($translation)),
            array(false, false, true , false, false, array($published)),
            array(false, false, false, true , false, array($autoPublish)),
            array(false, false, false, false, true , array($autoUnpublish)),
            array(true , false, true , false, true , array($initial, $published, $autoUnpublish)),
            array(false, true , false, true , false, array($translation, $autoPublish)),
            array(true , true , true , true , true , array($initial, $translation, $published, $autoPublish, $autoUnpublish))
        );
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class'    => $this->statusClass,
            'delete_button' => false,
            'new_button' => false,
            'delete_business_rules' => false,
            'business_rules_help_text' => 'open_orchestra_workflow_admin.form.status.business_rules_help_text',
            'group_enabled' => true,
            'group_render'  => array(
                'properties' => array(
                    'rank'  => 0,
                    'label' => 'open_orchestra_workflow_admin.form.status.group.properties',
                ),
            ),
        ));
    }
}
