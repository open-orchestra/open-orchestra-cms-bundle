<?php

namespace PHPOrchestra\CMSBundle\Test\Helper;

use \PHPOrchestra\CMSBundle\Helper\TreeHelper;

/**
 * Unit tests of NodesHelper
 *
 * @author Nicolas BOUQUET <nicolas.bouquet@businessdecision.com>
 */
class NodesHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $expectedResult
     * @param array $nodes
     *
     * @dataProvider createTreeData
     */
    public function testCreateTree($expectedResult, $nodes)
    {
        $result = TreeHelper::createTree($nodes);
        $this->assertEquals($expectedResult, $result);
    }
    
    public function createTreeData()
    {
        return array(
            array(
                array(
                    array(
                        'id' => 'root',
                        'class' => '',
                        'text' => 'Home page',
                        'sublinks' => array(
                            array(
                                'id' => '2',
                                'class' => 'deleted',
                                'text' => 'Home child'
                            )
                        )
                    )
                ),
                array(
                    array(
                        '_id'       => 'root',
                        'parentId'  => '0',
                        'name'      => 'Home page',
                        'deleted'   => false
                    ),
                    array(
                        '_id'       => '2',
                        'parentId'  => 'root',
                        'name'      => 'Home child',
                        'deleted'   => true
                    ),
                )
            ),
            array(
                array(
                    array(
                        'id' => 'root',
                        'class' => '',
                        'text' => 'Home page',
                    )
                ),
                array(
                    array(
                        '_id'       => 'root',
                        'parentId'  => '0',
                        'name'      => 'Home page',
                        'deleted'   => false
                    ),
                )
            ),
            array(
                array(
                    array(
                        'id' => 'root',
                        'class' => '',
                        'text' => 'Home page',
                    ),
                    array(
                        'id' => '2',
                        'class' => '',
                        'text' => 'Second page',
                    ),
                ),
                array(
                    array(
                        '_id'       => 'root',
                        'parentId'  => '0',
                        'name'      => 'Home page',
                        'deleted'   => false
                    ),
                    array(
                        '_id'       => '2',
                        'parentId'  => '0',
                        'name'      => 'Second page',
                        'deleted'   => false
                    ),
                )
            ),
        );
    }
}
