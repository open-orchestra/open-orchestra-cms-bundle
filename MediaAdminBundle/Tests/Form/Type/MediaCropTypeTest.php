<?php

namespace OpenOrchestra\MediaAdminBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\MediaAdminBundle\Form\Type\MediaCropType;

/**
 * Class MediaCropTypeTest
 */
class MediaCropTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MediaCropType
     */
    protected $form;

    protected $builder;
    protected $translator;
    protected $thumbnailConfig;
    protected $translatedString;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translatedString = 'translated';
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->translatedString);

        $this->builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($this->builder)->add(Phake::anyParameters())->thenReturn($this->builder);
        Phake::when($this->builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($this->builder);

        $this->thumbnailConfig = array(
            'rectangle' => array(
                'width' => 100,
                'height' => 100,
            ),
            'max_width' => array(
                'max_width' => 100,
            ),
        );

        $this->form = new MediaCropType($this->thumbnailConfig, $this->translator);
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
        $this->assertSame('media_crop', $this->form->getName());
    }

    public function testBuildForm()
    {
        $this->form->buildForm($this->builder, array());

        Phake::verify($this->builder)->add('x', 'hidden');
        Phake::verify($this->builder)->add('y', 'hidden');
        Phake::verify($this->builder)->add('h', 'hidden');
        Phake::verify($this->builder)->add('w', 'hidden');
        Phake::verify($this->translator)->trans('open_orchestra_media_admin.form.media.rectangle');
        Phake::verify($this->translator)->trans('open_orchestra_media_admin.form.media.max_width');
        Phake::verify($this->builder)->add('format', 'choice', array(
            'choices' => array(
                'rectangle' => $this->translatedString,
                'max_width' => $this->translatedString,
            ),
            'label' => 'open_orchestra_media_admin.form.media.format',
            'empty_value' => 'open_orchestra_media_admin.form.media.original_image',
            'required' => false,
        ));
    }
}
