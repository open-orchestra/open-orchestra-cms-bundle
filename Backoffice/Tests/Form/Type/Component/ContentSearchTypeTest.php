<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\Component\ContentSearchType;

/**
 * Class ContentSearchTypeTest
 */
class ContentSearchTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $contentRepository;
    protected $contextManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        $this->contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');

        $this->form = new ContentSearchType(
            $this->contentRepository,
            $this->contextManager
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

        $this->form->buildForm($builder, array(
            'search_engine' => true,
            'required' => true,
            'attr' => array(),
            'authorize_new' => null,
        ));

        Phake::verify($builder, Phake::times(3))->add(Phake::anyParameters());
        Phake::verify($builder)->addEventSubscriber(Phake::anyParameters());
    }

}
