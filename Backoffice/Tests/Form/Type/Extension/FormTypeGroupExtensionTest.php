<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\extension;

use OpenOrchestra\Backoffice\Form\Type\Extension\FormTypeGroupExtension;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class UserTypeTest
 */
class FormTypeGroupExtensionTest extends AbstractBaseTestCase
{
    /**
     * @var FormTypeGroupExtension
     */
    protected $formExtension;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->formExtension = new FormTypeGroupExtension();
    }

    /**
     * Test buildForm
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilderInterface');
        $this->formExtension->buildForm($builder, array(
            'group_enabled' => 'group_enabled',
            'group_id' => 'group_id',
            'sub_group_id' => 'sub_group_id',
            'group_render' => 'group_render',
            'sub_group_render' => 'sub_group_render',
        ));
        Phake::verify($builder)->setAttribute('group_enabled', 'group_enabled');
        Phake::verify($builder)->setAttribute('group_id', 'group_id');
        Phake::verify($builder)->setAttribute('sub_group_id', 'sub_group_id');
        Phake::verify($builder)->setAttribute('group_render', 'group_render');
        Phake::verify($builder)->setAttribute('sub_group_render', 'sub_group_render');
    }

    /**
     * Test buildView
     *
     * @param bool  $groupEnabled
     * @param array $groupIds
     * @param array $subGroupIds
     * @param array $groupRender
     * @param array $subGroupRender
     * @param array $expectedResult
     *
     * @dataProvider provideOptions
     */
    public function testBuildView($groupEnabled, array $groupIds, array $subGroupIds, array $groupRender, array $subGroupRender, array $expectedResult)
    {
        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $config = Phake::mock('Symfony\Component\Form\FormConfigInterface');

        Phake::when($config)->getAttribute('group_enabled')->thenReturn($groupEnabled);
        Phake::when($config)->getAttribute('group_render')->thenReturn($groupRender);
        Phake::when($config)->getAttribute('sub_group_render')->thenReturn($subGroupRender);
        Phake::when($formInterface)->getConfig()->thenReturn($config);

        $children = array();

        foreach ($groupIds as $key => $groupId) {
            $childFormInterface = Phake::mock('Symfony\Component\Form\FormInterface');
            Phake::when($childFormInterface)->getName()->thenReturn($key);
            $childConfig = Phake::mock('Symfony\Component\Form\FormConfigInterface');
            Phake::when($childConfig)->getAttribute('group_id')->thenReturn($groupId);
            Phake::when($childConfig)->getAttribute('sub_group_id')->thenReturn($subGroupIds[$key]);
            Phake::when($childFormInterface)->getConfig()->thenReturn($childConfig);
            $children[] = $childFormInterface;
        }

        Phake::when($formInterface)->all()->thenReturn($children);
        $this->formExtension->buildView($formView, $formInterface, array());

        if ($groupEnabled) {
            $this->assertEquals($formView->vars['group'], $expectedResult);
        }
        $this->assertEquals($formView->vars['group_enabled'], $groupEnabled);

    }

    /**
     * @return array
     */
    public function provideOptions()
    {
        return array(
            array(false, array(), array(), array(), array(), array()),
            array(true, array(), array(), array(), array(), array()),
            array(
                true,
                array('tabulation0', 'tabulation0', 'tabulation0', 'tabulation1', 'tabulation1', 'noTabulationDefinition', 'noTabulationDefinition'),
                array('fieldset0', 'fieldset0', 'tabulation0fieldset1', 'tabulation1fieldset1', 'tabulation1fieldset0', 'fieldset0', 'noFieldsetDefinition'),
                array(
                    'tabulation0' => array(
                        'rank' => 0,
                        'label' => 'Tabulation0',
                    ),
                    'tabulation1' => array(
                        'rank' => 1,
                        'label' => 'Tabulation1',
                    ),
                ),
                array(
                    'fieldset0' => array(
                        'rank' => 0,
                        'label' => 'Fieldset0',
                    ),
                    'tabulation0fieldset1' => array(
                        'rank' => 1,
                        'label' => 'Fieldset1',
                    ),
                    'tabulation1fieldset0' => array(
                        'rank' => 0,
                        'label' => 'Fieldset0',
                    ),
                    'tabulation1fieldset1' => array(
                        'rank' => 1,
                        'label' => 'Fieldset1',
                    ),
                ),
                array (
                    0 =>
                        array (
                            array (
                                'children' =>
                                    array (
                                        0,
                                        1,
                                    ),
                                'group_label' => 'Tabulation0',
                                'group_name' => 'tabulation0',
                                'sub_group_label' => 'Fieldset0',
                            ),
                            array (
                                'children' =>
                                    array (
                                        2,
                                    ),
                                 'group_label' => 'Tabulation0',
                                 'group_name' => 'tabulation0',
                                 'sub_group_label' => 'Fieldset1',
                            ),
                        ),
                    1 =>
                        array (
                            array (
                                'children' =>
                                  array (
                                      4,
                                  ),
                                  'group_label' => 'Tabulation1',
                                  'group_name' => 'tabulation1',
                                  'sub_group_label' => 'Fieldset0',
                            ),
                            array (
                                'children' =>
                                array (
                                    3,
                                ),
                                'group_label' => 'Tabulation1',
                                'group_name' => 'tabulation1',
                                'sub_group_label' => 'Fieldset1',
                            ),
                        ),
                    '_default_group' =>
                        array (
                            0 =>
                                array (
                                    'children' =>
                                        array (
                                            5,
                                        ),
                                    'group_label' => '_default_group',
                                    'group_name' => 'noTabulationDefinition',
                                    'sub_group_label' => 'Fieldset0',
                                ),
                            '_default_sub_group' =>
                                array (
                                    'children' =>
                                        array (
                                            6,
                                        ),
                                    'group_label' => '_default_group',
                                    'group_name' => 'noTabulationDefinition',
                                    'sub_group_label' => '_default_sub_group',
                                ),
                        ),
                )
            ),
        );
    }

    /**
     * Test getExtendedType
     */
    public function testGetExtendedType()
    {
        $this->assertSame('form', $this->formExtension->getExtendedType());
    }

    /**
     * Test setDefaultOptions
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $this->formExtension->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'group_enabled' => false,
            'group_id' => FormTypeGroupExtension::DEFAULT_GROUP,
            'sub_group_id' => FormTypeGroupExtension::DEFAULT_SUB_GROUP,
            'group_render' => array(),
            'sub_group_render' => array(),
        ));
    }
}
