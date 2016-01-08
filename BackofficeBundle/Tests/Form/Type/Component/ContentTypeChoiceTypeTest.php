<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type\Component;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\Component\ContentTypeChoiceType;

/**
 * Class ContentTypeChoiceTypeTest
 */
class ContentTypeChoiceTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $context;
    protected $contentType1;
    protected $contentType2;
    protected $locale = 'en';
    protected $contentTypeName1;
    protected $contentTypeName2;
    protected $contentTypeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->context = Phake::mock('OpenOrchestra\Backoffice\Context\ContextManager');
        Phake::when($this->context)->getCurrentLocale()->thenReturn($this->locale);

        $this->contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        $this->contentType1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contentType1)->getName($this->locale)->thenReturn($this->contentTypeName1);
        $this->contentType2 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($this->contentType2)->getName($this->locale)->thenReturn($this->contentTypeName2);

        $this->form = new ContentTypeChoiceType($this->contentTypeRepository, $this->context);
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
        $this->assertEquals('oo_content_type_choice', $this->form->getName());
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
