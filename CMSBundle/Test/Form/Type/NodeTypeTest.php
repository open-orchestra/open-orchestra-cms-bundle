<?php

/*
 * Business & Decision - Commercial License
 *
 * Copyright 2014 Business & Decision.
 *
 * All rights reserved. You CANNOT use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell this Software or any parts of this
 * Software, without the written authorization of Business & Decision.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * See LICENSE.txt file for the full LICENSE text.
 */

namespace PHPOrchestra\CMSBundle\Test\Form\Type;

use Phake;
use \PHPOrchestra\CMSBundle\Form\Type\NodeType;

/**
 * Description of NodeTypeTest
 */
class NodeTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $router = Phake::mock('Symfony\Component\Routing\Router');
        Phake::when($router)->generate(Phake::anyParameters())->thenReturn('/dummy/url');

        $container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($container)->get('router')->thenReturn($router);

        $this->nodeType = new NodeType($container);
    }
    
/*    public function testBuildForm()
    {
        $formBuilderMock =
            $this->getMock('\\Symfony\\Component\\Form\\FormBuilderInterface');
        
        // TODO Improves this test, check some specific added types
        $formBuilderMock
            ->expects($this->exactly(15))
            ->method('add')
            ->will($this->returnSelf());
        
        $this->nodeType->buildForm($formBuilderMock, array());
    }*/
    
    /*public function testSetDefaultOptions()
    {
        $resolverMock =
            $this->getMock('\\Symfony\\Component\\OptionsResolver\\OptionsResolverInterface');
        
        $resolverMock
            ->expects($this->once())
            ->method('setDefaults')
            ->with(
                $this->equalTo(
                    array(
                        'showDialog' => true,
                        'js' => array(
                            'script' => 'local/node.js',
                            'parameter' => array(
                                'name' => 'node',
                                'urlTemplate' => '/dummy/url'
                            )
                        ),
                        'objects' => array()
                    )
                )
            );
        
        $this->nodeType->setDefaultOptions($resolverMock);
    }*/
    
    /*public function testBuildView()
    {
        $viewMock =
            $this->getMock('\\Symfony\\Component\\Form\\FormView');
        
        $formMock =
            $this->getMock('\\Symfony\\Component\\Form\\FormInterface');
        
        $options = array(
            'showDialog'   => 'value1',
            'js'           => 'value2',
            'objects'      => array('object1', 'object2'),
            'unusedOption' => 'useless'
        );
        
        $this->nodeType->buildView($viewMock, $formMock, $options);
        
        $expectedViewVars = $options;
        $expectedViewVars['value'] = null;
        $expectedViewVars['attr'] = array();
        unset($expectedViewVars['unusedOption']);
        
        $this->assertEquals($expectedViewVars, $viewMock->vars);
    }*/

    public function testGetName()
    {
        $this->assertEquals('node', $this->nodeType->getName());
    }
}
