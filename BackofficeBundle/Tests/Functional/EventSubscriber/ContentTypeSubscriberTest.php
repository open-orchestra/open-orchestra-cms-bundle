<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Functional\EventSubscriber;

use OpenOrchestra\ModelBundle\Document\Content;
use OpenOrchestra\ModelBundle\Document\ContentAttribute;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class ContentTypeSubscriberTest
 */
class ContentTypeSubscriberTest extends KernelTestCase
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Set up the test
     */
    public function setUp()
    {
        static::bootKernel();
        $this->formFactory = static::$kernel->getContainer()->get('form.factory');
    }

    /**
     * Test post set data form content
     *
     * @param mixed $attributeValue
     * @param mixed $fieldValue
     * @param int   $countError
     *
     * @dataProvider provideFormAttributeValue
     */
    public function testFormFieldTransformation($attributeValue, $fieldValue, $countError)
    {
        $content = new Content();
        $content->setContentType("customer");

        $attribute = new ContentAttribute();
        $attribute->setName("identifier");
        $attribute->setValue($attributeValue);
        $attribute->setType('integer');
        $content->addAttribute($attribute);

        $form = $this->formFactory->create('orchestra_content', $content, array('csrf_protection' => false));
        $this->assertSame(count($form->get('identifier')->getErrors()), $countError);
        $this->assertSame($form->get('identifier')->getData(), $fieldValue);
    }

    /**
     * @return array
     */
    public function provideFormAttributeValue()
    {
        return array(
            array(1, 1, 0),
            array(null, null, 0),
            array("not integer", null, 1),
            array(array("not integer"), null, 1),
        );
    }

}
