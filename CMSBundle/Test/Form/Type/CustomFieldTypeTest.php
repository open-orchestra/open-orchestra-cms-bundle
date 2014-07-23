<?php

namespace PHPOrchestra\CMSBundle\Test\Form\Type;

use Phake;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Type;
use PHPOrchestra\CMSBundle\Form\Type\CustomFieldType;

/**
 * Description of ContentTypeTest
 *
 * @author Noël GILAIN <noel.gilain@businessdecision.com>
 */
class CustomFieldTypeTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $formBuilderMock;
    protected $customField;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $availableFields = array(
            'orchestra_missconf' => 'fakeData',
            'orchestra_notdescribed' => array(
                'type' => 'notdescribed'
            ),
            'orchestra_text' => array(
                'type' => 'text',
                'options' => array('required' => true)
            ),
        );
        
        $this->customField = new CustomFieldType($availableFields);
        
        $this->formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
    }

    /**
     * @dataProvider getOptions
     * 
     * @param array  $options
     * @param int  $expectedAddCount
     */
    public function testBuildForm($options, $expectedAddCount)
    {
        Phake::when($this->formBuilderMock)->add(Phake::anyParameters())->thenReturn($this->formBuilderMock);

        $this->customField->buildForm($this->formBuilderMock, $options);

        Phake::verify($this->formBuilderMock, Phake::times(7))->add(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $dataWithNoOptions = (object) array(
            'type' => 'orchestra_text',
            'symfonyType' => 'text'
        );
        $dataWithOptions = (object) array(
            'type' => 'orchestra_text',
            'symfonyType' => 'text',
            'options' => (object) array('required' => false)
        );
        
        return array(
            array(array('data' =>  $dataWithNoOptions), 7),
            array(array('data' =>  $dataWithOptions), 7)
        );
    }

    /**
     * @dataProvider getExceptionsData
     * 
     * @param array  $options
     */
    public function testException($options)
    {
        $this->setExpectedException('\\PHPOrchestra\\CMSBundle\\Exception\\UnknownFieldTypeException');
        
        $this->customField->buildForm($this->formBuilderMock, $options);
    }

    /**
     * @return array
     */
    public function getExceptionsData()
    {
        $unknownFieldType = (object) array(
            'type' => 'orchestra_hidden',
            'symfonyType' => 'hidden'
        );
        $missConfiguration = (object) array(
            'type' => 'orchestra_missconf',
            'symfonyType' => 'missconf'
        );
        $fieldtypeNoDesc = (object) array(
            'type' => 'orchestra_notdescribed',
            'symfonyType' => 'notdescribed'
        );
        
        return array(
            array(array()), // No data
            array(array('data' =>  $unknownFieldType)), // Unknown field type
            array(array('data' =>  $missConfiguration)), // Missconfiguration
            array(array('data' =>  $fieldtypeNoDesc)), // Field type not described
        );
    }

    /**
     * @dataProvider getConstraintsData
     *
     * @param string $fieldType
     * @param mixed  $expectedConstraints
     */
    public function testGetConstraints($fieldType, $expectedConstraints)
    {
        $this->assertEquals($this->customField->getConstraints($fieldType), $expectedConstraints);
    }

    /**
     * @return array
     */
    public function getConstraintsData()
    {
        return array(
            array('unkonwnFieldType', array()),
            array('integer', array(new Type(array('type' => 'numeric')))),
            array('email', array(new Email()))
        );
    }

    /**
     * Test name
     */
    public function testGetName()
    {
        $this->assertEquals('orchestra_customField', $this->customField->getName());
    }
}
