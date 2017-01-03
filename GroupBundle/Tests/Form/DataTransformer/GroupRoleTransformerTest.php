<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\GroupBundle\Form\DataTransformer\GroupRoleTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class GroupRoleTransformerTest
 */
class GroupRoleTransformerTest extends AbstractBaseTestCase
{
    protected $generatePerimeterManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $configuration = array(
            'firstpackage' => array(
                'page' => array(
                    'EDITORIAL_NODE_CONTRIBUTOR' => 'open_orchestra_backoffice.role.contributor',
                    'EDITORIAL_NODE_SUPER_EDITOR' => 'open_orchestra_backoffice.role.editor',
                    'EDITORIAL_NODE_SUPER_SUPRESSOR' => 'open_orchestra_backoffice.role.suppresor',
                ),
            ),
            'secondpackage' => array(
                'trash' => array(
                    'EDITORIAL_TRASH_RESTORER' => 'open_orchestra_backoffice.role.restorer',
                    'EDITORIAL_TRASH_SUPRESSOR' => 'open_orchestra_backoffice.role.contributor',
                ),
            ),
            'thirdpackage' => array(
                'configuration' => array(
                    'ROLE_SITE_ADMIN' => 'open_orchestra_backoffice.role.administrator',
                ),
            ),
        );

        $this->transformer = new GroupRoleTransformer($configuration);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * Test transform
     *
     * @param array $values
     * @param array $expectedResults
     *
     * @dataProvider provideValues
     */
    public function testTransform(array $values, array $expectedResults)
    {
        $result = $this->transformer->transform($values);
        $this->assertEquals($expectedResults, $result);
    }

    /**
     * Test reverseTransform
     *
     * @param array $expectedResults
     * @param array $values
     *
     * @dataProvider provideValues
     */
    public function testReverseTransform(array $expectedResults, array $values)
    {
        $result = $this->transformer->reverseTransform($values);
        $this->assertEquals($expectedResults, $result);
    }

    /**
     * @return array
     */
    public function provideValues()
    {
        return array(
            array(array('EDITORIAL_NODE_CONTRIBUTOR', 'EDITORIAL_TRASH_SUPRESSOR', 'ROLE_SITE_ADMIN'), array('roles_collections' => array(
                    'firstpackage' => array(
                        'page' => array(
                            'EDITORIAL_NODE_CONTRIBUTOR' => true,
                            'EDITORIAL_NODE_SUPER_EDITOR' => false,
                            'EDITORIAL_NODE_SUPER_SUPRESSOR' => false,
                        ),
                    ),
                    'secondpackage' => array(
                        'trash' => array(
                            'EDITORIAL_TRASH_RESTORER' => false,
                            'EDITORIAL_TRASH_SUPRESSOR' => true,
                        ),
                    ),
                    'thirdpackage' => array(
                        'configuration' => array(
                            'ROLE_SITE_ADMIN' => true,
                        ),
                    ),
                ))),
        );
    }
}
