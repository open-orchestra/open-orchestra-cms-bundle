<?php


namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\OrchestraContentTypeChoiceType;

/**
 * Class OrchestraContentTypeChoiceTypeTest
 */
class OrchestraContentTypeChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    protected $contentTypeRepository;
    protected $contentType1;
    protected $contentType2;
    protected $contentTypeName1;
    protected $contentTypeName2;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        $this->contentType1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contentType1)->getName()->thenReturn($this->contentTypeName1);
        $this->contentType2 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contentType2)->getName()->thenReturn($this->contentTypeName2);

        $this->form = new OrchestraContentTypeChoiceType($this->contentTypeRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\AbstractType', $this->form);
    }

    /**
     * Test parent
     */
    public function testGetParent()
    {
        $this->assertEquals('choice', $this->form->getParent());
    }

    /**
     * Test Name
     */
    public function testGetName()
    {
        $this->assertEquals('orchestra_content_type_choice', $this->form->getName());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        Phake::when($this->contentTypeRepository)->findAllNotDeletedInLastVersion()->thenReturn(
            array(
                $this->contentType1,
                $this->contentType2
            )
        );

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(
            array(
                'choices' => array(
                    $this->contentTypeName1,
                    $this->contentTypeName2,
                )
            )
        );
    }
}
