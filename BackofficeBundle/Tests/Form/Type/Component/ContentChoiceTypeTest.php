<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type\Component;

use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\Component\ContentChoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Repository\ReadContentRepositoryInterface;

/**
 * Class ContentChoiceTypeTest
 */
class ContentChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $form;
    protected $formTypeName = 'fakeFormTypeName';
    protected $contentType = 'fakeContentType';
    protected $operator = 'fakeOperator';
    protected $keyword = 'fakeKeyword';
    protected $language = 'fakeLanguage';
    protected $contentId0 = 'fakeContentId0';
    protected $contentName0 = 'fakeContentName0';
    protected $contentId1 = 'fakeContentId1';
    protected $contentName1 = 'fakeContentName1';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $content0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content0)->getId()->thenReturn($this->contentId0);
        Phake::when($content0)->getName()->thenReturn($this->contentName0);

        $content1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($content1)->getId()->thenReturn($this->contentId1);
        Phake::when($content1)->getName()->thenReturn($this->contentName1);

        $contents = new ArrayCollection();
        $contents->add($content0);
        $contents->add($content1);

        $contentRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        Phake::when($contentRepository)->findByContentTypeAndKeywords($this->language, $this->contentType, $this->operator, $this->keyword)->thenReturn($contents);

        $contextManager = Phake::mock('OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface');
        Phake::when($contextManager)->getCurrentSiteDefaultLanguage()->thenReturn($this->language);

        $referenceToEmbedTransformer = Phake::mock('OpenOrchestra\BackofficeBundle\Form\DataTransformer\ReferenceToEmbedTransformer');

        $this->form = new ContentChoiceType($contentRepository, $contextManager, $referenceToEmbedTransformer, $this->formTypeName);
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
        $this->assertEquals('oo_content_choice', $this->form->getName());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(
            array(
                'content_type' => '',
                'operator' => ReadContentRepositoryInterface::CHOICE_AND,
                'keyword' => null,
            )
        );
    }

    /**
     * Test buildForm
     */
    public function testBuildForm()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilderInterface');

        $this->form->buildForm($builder, array(
            'content_type' => $this->contentType,
            'operator' => $this->operator,
            'keyword' => $this->keyword
        ));

        Phake::verify($builder)->add($this->formTypeName, 'choice', array(
                'label' => false,
                'choices' => array(
                    $this->contentId0 => $this->contentName0,
                    $this->contentId1 => $this->contentName1,
                ),
            )
        );
    }

}
