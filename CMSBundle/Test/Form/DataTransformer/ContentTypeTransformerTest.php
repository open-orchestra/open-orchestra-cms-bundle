<?php

namespace PHPOrchestra\CMSBundle\Test\Form\DataTransformer;

use PHPOrchestra\CMSBundle\Test\Mock\Serializer;
use \PHPOrchestra\CMSBundle\Form\DataTransformer\ContentTypeTransformer;
use PHPOrchestra\CMSBundle\Test\Mock\TestContentType;

/**
 * Description of ContentTypeTransformerTest
 *
 * @author Noël GILAIN <noel.gilain@businessdecision.com>
 */
class ContentTypeTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $serializer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        
        $customFields = array(
            'fakeField' => array(
                'type' => 'fakeType',
                'options' => array(
                    'option1' => array('default_value' => 'value1'),
                    'option2' => array('default_value' => 'value2')
                )
            )
        );
        
        $this->transformer = new ContentTypeTransformer(new Serializer(), $customFields);
    }
    
    /**
     * @dataProvider getTransformData
     * @param type $contentType
     * @param type $expectedReturn
     */
    public function testTransform($contentType, $expectedReturn)
    {
        $result = $this->transformer->transform($contentType);
        
        $this->assertEquals($expectedReturn, $result);
    }
    
    /**
     * @dataProvider getReverseTransformData
     * @param type $datas
     * @param type $expectedReturn
     */
    public function testReverseTransform($datas, $expectedReturn)
    {
        $result = $this->transformer->reverseTransform($datas);
        
        $this->assertEquals($expectedReturn, $result);
    }
    
    /**
     * Data provider
     */
    public function getTransformData()
    {
        $fields = array(
            'customField1',
            'customField2',
            'customField3',
        );
        
        return array(
            array(new TestContentType(), new TestContentType()),
            array(new TestContentType($fields), new TestContentType($fields))
        );
    }
    
    /**
     * Data provider
     */
    public function getReverseTransformData()
    {
        $fields = array(
            'customField1',
            'customField2',
            'customField3',
        );
        
        /************ DATASET 1 ************************/
        
        $datas = new TestContentType(
            $fields
        );
        
        $result = clone $datas;
        
        /************ DATASET 2 ************************/
        
        $datasFull = new TestContentType(
            $fields,
            'fakeField'
        );
        
        $resultFull = clone $datasFull;
        $resultFull->setFields(
            json_encode(
                array(
                    'customField1',
                    'customField2',
                    'customField3',
                    (object) array(
                        'fieldId' => '',
                        'label' => '',
                        'defaultValue' => '',
                        'searchable' => false,
                        'type' => 'fakeField',
                        'symfonyType' => 'fakeType',
                        'options' => (object) array(
                            'option1' => 'value1',
                            'option2' => 'value2'
                        )
                    )
                )
            )
        );
        
        /************ TEST DATAS  *************************/
        
        return array(
            array($datas, $result),
            array($datasFull, $resultFull)
        );
    }
}
