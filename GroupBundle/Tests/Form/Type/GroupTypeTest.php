<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\GroupBundle\Form\Type\GroupType;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class GroupTypeTest
 */
class GroupTypeTest extends Phake
{
    /**
     * @var GroupType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $groupRepository = Phake::mock('OpenOrchestra\GroupBundle\Repository\GroupRepository');
        $multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        $this->form = new GroupType($groupRepository, $multiLanguagesChoiceManager);
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
        $this->assertSame('oo_group', $this->form->getName());
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

        Phake::verify($builder, Phake::times(1))->add(Phake::anyParameters());
    }

    /**
     * Test resolver
     */
    public function testConfigureOptions()
    {
        $resolver = Phake::mock('Symfony\Component\OptionsResolver\OptionsResolver');

        $this->form->configureOptions($resolver);

        Phake::verify($resolver)->setDefaults(array(
            'allowed_sites' => null,
        ));
    }

    /**
     * @param GroupInterface|null $group
     * @param array               $allowedSites
     * @param array               $expectedParameters
     *
     * @dataProvider provideGroup
     */
    public function testBuildView($group, array $allowedSites, array $expectedParameters)
    {
        $groupRepository = Phake::mock('OpenOrchestra\GroupBundle\Repository\GroupRepository');
        Phake::when($groupRepository)->find(Phake::anyParameters())->thenReturn($group);
        $multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        Phake::when($multiLanguagesChoiceManager)->choose(Phake::anyParameters())->thenReturn('fakeLabel');
        $form = new GroupType($groupRepository, $multiLanguagesChoiceManager);

        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $formView->vars['name'] = 'fakeName';
        $formView->vars['label'] = null;
        $options = array($allowedSites);

        $form->buildView($formView, $formInterface, $options);
        $this->assertEquals($formView->vars['parameters'], $expectedParameters);
    }

    /**
     * @return array
     */
    public function provideGroup()
    {
        $allowedSiteId = 'allowedSiteId';
        $disallowedSiteId = 'disallowedSiteId';

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getName()->thenReturn('fakeName');
        Phake::when($site)->getId()->thenReturn($disallowedSiteId);

        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getSite()->thenReturn($site);
        Phake::when($group)->getLabels()->thenReturn(array());

        return array(
            "prototype" => array(null, array(), array('groupName' => '__label__', 'siteName' => '__site.name__', 'disabled' => false)),
            "allowed" => array($group, array($allowedSiteId), array('groupName' => 'fakeLabel', 'siteName' => 'fakeName', 'disabled' => false)),
            "disallowed" => array($group, array($disallowedSiteId), array('groupName' => 'fakeLabel', 'siteName' => 'fakeName', 'disabled' => true)),
        );
    }
}
