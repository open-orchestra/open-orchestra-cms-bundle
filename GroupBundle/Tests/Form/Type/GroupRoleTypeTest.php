<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\Form\Type\GroupRoleType;

/**
 * Class GroupRoleTypeTest
 */
class GroupRoleTypeTest extends AbstractBaseTestCase
{
    /**
     * @var GroupRoleType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($translator)->trans(\Phake::anyParameters())->thenReturn('test');
        $configuration = array(
            'firstpackage' => array(
                'page' => array(
                    'EDITORIAL_NODE_CONTRIBUTOR' => array(
                        'label' => 'open_orchestra_backoffice.role.contributor'),
                    'EDITORIAL_NODE_SUPER_EDITOR' => array(
                        'label' => 'open_orchestra_backoffice.role.editor'),
                    'EDITORIAL_NODE_SUPER_SUPRESSOR' => array(
                        'label' => 'open_orchestra_backoffice.role.suppresor'),
                ),
            ),
            'secondpackage' => array(
                'trash' => array(
                    'EDITORIAL_TRASH_RESTORER' => array(
                        'label' => 'open_orchestra_backoffice.role.restorer'),
                    'EDITORIAL_TRASH_SUPRESSOR' => array(
                        'label' => 'open_orchestra_backoffice.role.trash_suppresor'),
                ),
            ),
            'thirdpackage' => array(
                'configuration' => array(
                    'ROLE_SITE_ADMIN' => array(
                        'label' => 'open_orchestra_backoffice.role.administrator'),
                ),
            ),
        );
        $this->form = new GroupRoleType($translator, $configuration);
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
        $this->assertSame('oo_group_role', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(1))->add('roles_collections', 'collection', array(
                'entry_type' => 'oo_check_list_collection',
                'label' => false,
                'entry_options' => array(
                    'configuration' => array(
                        'firstpackage' => array(
                            'row' => array ('test', 'test', 'test'),
                            'column' => array('page' => 'test'),
                         ),
                        'secondpackage' => array(
                            'row' => array ('test', 'test'),
                            'column' => array('trash' => 'test'),
                         ),
                        'thirdpackage' => array(
                            'row' => array ('test'),
                            'column' => array('configuration' => 'test'),
                         ),
                    )
                )
         ));
    }

    /**
     * Test buildView
     */
    public function testBuildView()
    {
        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');

        $this->form->buildView($formView, $formInterface, array());
        $this->assertEquals($formView->vars['configuration'], array('firstpackage', 'secondpackage', 'thirdpackage'));
    }
}
