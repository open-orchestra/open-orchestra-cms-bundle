<?php

namespace PHPOrchestra\BackofficeBundle\Test\Form\Type;

use Phake;
use PHPOrchestra\BackofficeBundle\Form\Type\StatusType;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class StatusTypeTest
 */
class StatusTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatusType
     */
    protected $form;

    protected $statusClass = 'site';
    protected $translateValueInitializer;
    protected $contentTypeRepository;
    protected $translationChoiceManager;
    protected $translator;
    
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translateValueInitializer = Phake::mock('PHPOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener');
        $this->contentTypeRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\ContentTypeRepository');
        $this->translationChoiceManager = Phake::mock('PHPOrchestra\Backoffice\Manager\TranslationChoiceManager');
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $this->form = new StatusType($this->statusClass, $this->translateValueInitializer, $this->contentTypeRepository, $this->translationChoiceManager, $this->translator);
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
        $this->assertSame('status', $this->form->getName());
    }

    /**
     * Test builder
     * @param array $contentTypes
     * @param array $expectedResult
     * 
     * @dataProvider getContentType
     */
    public function testBuilder($contentTypes, $expectedResult)
    {

        Phake::when($this->contentTypeRepository)->findByDeleted(Phake::anyParameters())->thenReturn($contentTypes);
        foreach($contentTypes as $contentType){
            Phake::when($this->translationChoiceManager)->choose($contentType->getNames())->thenReturn($contentType->getNames()->first());
        }
        Phake::when($this->translator)->trans('php_orchestra_backoffice.left_menu.editorial.nodes')->thenReturn('node');
        
        
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder)->add('name');
        Phake::verify($builder)->add('published', null, array('required' => false));
        Phake::verify($builder)->add('role', null, array('required' => false));
        Phake::verify($builder)->add('labels', 'translated_value_collection');
        
        Phake::verify($builder)->add('initial', 'choice', array(
	        'choices' => $expectedResult,
	        'multiple' => true,
	        'expanded' => true
	    ));
                
        Phake::verify($builder)->addEventSubscriber(Phake::anyParameters());
        Phake::verify($builder)->addEventListener(
            FormEvents::PRE_SET_DATA,
            array($this->translateValueInitializer, 'preSetData')
        );
    }

    /**
     * Test resolver
     */
    public function testSetDefaultOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolverInterface');

        $this->form->setDefaultOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'data_class' => $this->statusClass
        ));
    }
    /**
     * CotentType provider
     *
     * @return array
     */
    public function getContentType()
    {
        $id0 = 'fakeId0';
        $name0 = 'fakeName0';
        $id1 = 'fakeId1';
        $name1 = 'fakeName1';
        $name2 = 'node';

        $sons0 = new ArrayCollection();
        $sons0->add($name0);
        $sons1 = new ArrayCollection();
        $sons1->add($name1);

        $contentType0 = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentTypeInterface');
        Phake::when($contentType0)->getContentTypeId()->thenReturn($id0);
        Phake::when($contentType0)->getNames()->thenReturn($sons0);

        $contentType1 = Phake::mock('PHPOrchestra\ModelBundle\Model\ContentTypeInterface');
        Phake::when($contentType1)->getContentTypeId()->thenReturn($id1);
        Phake::when($contentType1)->getNames()->thenReturn($sons1);

        return array(
            array(
                array($contentType0, $contentType1), array($id0 => $name0, $id1 => $name1, $name2 => $name2)
            )
        );
    }

}
