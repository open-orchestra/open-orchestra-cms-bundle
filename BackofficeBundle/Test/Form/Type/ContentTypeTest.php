<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\ContentType;

/**
 * Class ContentTypeTest
 */
class ContentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentType
     */
    protected $form;

    protected $contentTypeRepository;
    protected $transaltionChoiceManager;
    protected $contentClass = 'content';
    protected $contentAttributeClass = 'attribute';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\ContentTypeRepository');

        $this->transaltionChoiceManager = Phake::mock('PHPOrchestra\Backoffice\Manager\TranslationChoiceManager');

        $this->form = new ContentType(
            $this->contentTypeRepository,
            $this->contentClass,
            $this->contentAttributeClass,
            $this->transaltionChoiceManager
        );
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
        $this->assertSame('orchestra_content', $this->form->getName());
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

        Phake::verify($builder)->add(Phake::anyParameters());
        Phake::verify($builder, Phake::times(2))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testSetDefaultOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->contentClass,
        ));
    }

}
