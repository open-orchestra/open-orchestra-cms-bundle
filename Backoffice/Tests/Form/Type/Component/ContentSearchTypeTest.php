<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\Component\ContentSearchType;
use OpenOrchestra\Transformer\ConditionFromBooleanToBddTransformerInterface;

/**
 * Class ContentSearchTypeTest
 */
class ContentSearchTypeTest extends AbstractBaseTestCase
{
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new ContentSearchType('OpenOrchestra\Backoffice\Tests\Form\Type\Component\PhakeClass');
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test Name
     */
    public function testGetName()
    {
        $this->assertEquals('oo_content_search', $this->form->getName());
    }

    /**
     * Test buildForm
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(2))->add(Phake::anyParameters());
    }

}

class PhakeClass implements ConditionFromBooleanToBddTransformerInterface {

   protected $field;

   public function setField($field){
       $this->field = $field;
   }

   public function transform($value){
   }

   public function reverseTransform($value){
   }
}
