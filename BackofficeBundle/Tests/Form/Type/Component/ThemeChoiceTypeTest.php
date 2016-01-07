<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type\Component;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Form\Type\Component\ThemeChoiceType;

/**
 * Class ThemeChoiceTypeTest
 */
class ThemeChoiceTypeTest extends AbstractBaseTestCase
{
    /**
     * @var ThemeChoiceType
     */
    protected $form;

    protected $theme;
    protected $themeName;
    protected $themeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->themeName = 'name';
        $this->theme = Phake::mock('OpenOrchestra\ModelInterface\Model\ThemeInterface');
        Phake::when($this->theme)->getName()->thenReturn($this->themeName);

        $themeCollection = new ArrayCollection();
        $themeCollection->add($this->theme);
        $this->themeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ThemeRepositoryInterface');
        Phake::when($this->themeRepository)->findAll()->thenReturn($themeCollection);

        $this->form = new ThemeChoiceType($this->themeRepository);
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
        $this->assertSame('oo_theme_choice', $this->form->getName());
    }

    /**
     * Test parent
     */
    public function testParent()
    {
        $this->assertSame('choice', $this->form->getParent());
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

        Phake::verify($builder, Phake::never())->add(Phake::anyParameters());
        Phake::verify($builder, Phake::never())->addEventSubscriber(Phake::anyParameters());
        Phake::verify($builder, Phake::never())->addEventListener(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'choices' => array($this->themeName => $this->themeName)
        ));
    }
}
