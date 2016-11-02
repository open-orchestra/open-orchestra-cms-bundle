<?php

namespace OpenOrchestra\Backoffice\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Form\Type\ContentTypeType;

/**
 * Class ContentTypeTypeTest
 */
class ContentTypeTypeTest extends AbstractBaseTestCase
{
    protected $form;
    protected $translator;
    protected $class = 'content_type';
    protected $translatedLabel = 'existing option';
    protected $contentTypeTypeSubscriber;
    protected $contentTypeStatusableSubscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->translatedLabel);
        $contentTypeOrderFieldTransformer = Phake::mock('Symfony\Component\Form\DataTransformerInterface');
        $this->contentTypeTypeSubscriber = Phake::mock('OpenOrchestra\Backoffice\EventSubscriber\ContentTypeTypeSubscriber');
        $this->contentTypeStatusableSubscriber = Phake::mock('OpenOrchestra\Backoffice\EventSubscriber\ContentTypeStatusableSubscriber');

        $this->form = new ContentTypeType(
            $this->class,
            $this->translator,
            array(),
            $contentTypeOrderFieldTransformer,
            $this->contentTypeTypeSubscriber,
            $this->contentTypeStatusableSubscriber
        );
    }

    /**
     * Test Name
     */
    public function testName()
    {
        $this->assertSame('oo_content_type', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addModelTransformer(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->get(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array());

        Phake::verify($builder, Phake::times(8))->add(Phake::anyParameters());
        Phake::verify($builder)->addEventSubscriber($this->contentTypeTypeSubscriber);
        Phake::verify($builder)->addEventSubscriber($this->contentTypeStatusableSubscriber);

        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.add');
        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.new');
        Phake::verify($this->translator)->trans('open_orchestra_backoffice.form.field_type.delete');
    }

    /**
     * Test the default options
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolverMock);

        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->class,
        ));
    }
}
