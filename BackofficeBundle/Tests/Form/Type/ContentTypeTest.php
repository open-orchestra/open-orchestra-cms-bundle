<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\ContentType;

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
    protected $fieldTypesConfiguration;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->fieldTypesConfiguration = array(
            'text' => 
                array(
                    'type' => 'text',
                    'options' => array(
                        'max_length' => 12,
                        'required' => 'false'
                    )
                )
        );

        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');

        $this->transaltionChoiceManager = Phake::mock('OpenOrchestra\Backoffice\Manager\TranslationChoiceManager');

        $this->form = new ContentType(
            $this->contentTypeRepository,
            $this->contentClass,
            $this->contentAttributeClass,
            $this->transaltionChoiceManager,
            $this->fieldTypesConfiguration
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

        Phake::verify($builder, Phake::times(2))->add(Phake::anyParameters());
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
