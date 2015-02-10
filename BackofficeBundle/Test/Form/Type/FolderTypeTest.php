<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\FolderType;

/**
 * Class FolderTypeTest
 */
class FolderTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    protected $class = 'PHPOrchestra\MediaBundle\Document\MediaFolder';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = new FolderType($this->class);
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('folder', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(2))->add(Phake::anyParameters());
        Phake::verify($builder)->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->class,
        ));
    }
}
