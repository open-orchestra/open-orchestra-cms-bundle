<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\Component\MultiLanguagesType;

/**
 * Class MultiLanguagesTypeTest
 */
class MultiLanguagesTypeTest extends AbstractBaseTestCase
{
    /**
     * @var MultiLanguagesType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new MultiLanguagesType();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test name
     */
    public function testName()
    {
        $this->assertSame('oo_multi_languages', $this->form->getName());
    }

    /**
     * Test builder
     *
     * @dataProvider provideLanguages
     */
    public function testBuilder($languages, $expectedBuildCount)
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array('type' => 'text', 'languages' => $languages, 'notBlank' => true));

        Phake::verify($builder, Phake::times($expectedBuildCount))->add(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideLanguages()
    {
        return array(
            array(array(), 0),
            array(array('fr', 'ch'), 2),
            array(array('fr', 'de', 'en'), 3),
        );
    }
}
