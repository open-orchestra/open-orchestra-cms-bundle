<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\AreaFlexType;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class AreaFlexTypeTest
 */
class AreaFlexTypeTest extends AbstractBaseTestCase
{
    protected $areaClass = 'OpenOrchestra\ModelBundle\Document\AreaFlex';
    protected $translator;
    /** @var  AreaFlexType */
    protected $areaType;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $areaFlexManager = Phake::mock('OpenOrchestra\BackofficeBundle\Manager\AreaFlexManager');
        $this->areaType = new AreaFlexType($this->areaClass, $areaFlexManager, $this->translator);
    }

    /**
     * test the build form
     */
    public function testBuildForm()
    {
        $formBuilderMock = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($formBuilderMock)->add(Phake::anyParameters())->thenReturn($formBuilderMock);

        $this->areaType->buildForm($formBuilderMock, array());

        Phake::verify($formBuilderMock, Phake::times(2))->add(Phake::anyParameters());

        Phake::verify($formBuilderMock, Phake::times(1))->addEventSubscriber(Phake::anyParameters());
    }

    /**
     * Test the default options
     */
    public function testConfigureOptions()
    {
        $resolverMock = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');
        $translation = 'fakeTranslation';
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($translation);

        $this->areaType->configureOptions($resolverMock);

        Phake::verify($this->translator, Phake::times(1))->trans(Phake::anyParameters());
        Phake::verify($resolverMock)->setDefaults(array(
            'data_class' => $this->areaClass,
            'attr' => array('data-title' => $translation),
        ));
    }
}
