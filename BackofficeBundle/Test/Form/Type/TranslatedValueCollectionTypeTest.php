<?php

namespace OpenOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\TranslatedValueCollectionType;

/**
 * Class TranslatedValueCollectionTypeTest
 */
class TranslatedValueCollectionTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $form;

    /**
     * Set up the text
     */
    public function setUp()
    {
        $this->form = new TranslatedValueCollectionType();
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('translated_value_collection', $this->form->getName());
    }

    /**
     * Test Parent
     */
    public function testParent()
    {
        $this->assertSame('collection', $this->form->getParent());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'type' => 'translated_value',
            'allow_add' => false,
            'allow_delete' => false,
            'required' => false,
            'label_attr' => array('class' => 'translated-value'),
        ));
    }
}
