<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

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
     * Test configureOptions
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'type' => 'translated_value',
            'allow_add' => false,
            'allow_delete' => false,
            'label_attr' => array('class' => 'translated-value'),
        ));
    }
}
