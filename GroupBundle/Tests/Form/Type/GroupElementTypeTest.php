<?php

namespace OpenOrchestra\GroupBundle\Tests\Form\Type;

use Phake;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\Form\Type\GroupElementType;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\GroupBundle\Repository\GroupRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class GroupElementTypeTest
 */
class GroupElementTypeTest extends AbstractBaseTestCase
{
    /**
     * @var GroupElementType
     */
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $groupRepository = Phake::mock('OpenOrchestra\GroupBundle\Repository\GroupRepository');
        $multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        $authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->form = new GroupElementType($multiLanguagesChoiceManager, $groupRepository, $authorizationChecker);
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
        $this->assertSame('oo_group_element', $this->form->getName());
    }

    /**
     * Test builder
     */
    public function testBuilder()
    {
        $builder = Phake::mock('Symfony\Component\Form\FormBuilder');
        Phake::when($builder)->add(Phake::anyParameters())->thenReturn($builder);
        Phake::when($builder)->addEventSubscriber(Phake::anyParameters())->thenReturn($builder);

        $this->form->buildForm($builder, array('property_path' => 'fakePropertyPath'));

        Phake::verify($builder, Phake::times(1))->add(Phake::anyParameters());
    }

    /**
     * @param GroupRepository               $groupRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param array                         $expectedParameters
     *
     * @dataProvider provideGroup
     */
    public function testBuildView(GroupRepository $groupRepository, AuthorizationCheckerInterface $authorizationChecker, array $expectedParameters)
    {
        $multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        Phake::when($multiLanguagesChoiceManager)->choose(Phake::anyParameters())->thenReturn('fakeLabel');
        $form = new GroupElementType($multiLanguagesChoiceManager, $groupRepository, $authorizationChecker);

        $formInterface = Phake::mock('Symfony\Component\Form\FormInterface');
        $formView = Phake::mock('Symfony\Component\Form\FormView');
        $formView->vars['name'] = 'fakeName';
        $formView->vars['label'] = null;

        $form->buildView($formView, $formInterface, array());
        $this->assertEquals($formView->vars['parameters'], $expectedParameters);
    }

    /**
     * @return array
     */
    public function provideGroup()
    {
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getName()->thenReturn('fakeName');

        $groupRepository = Phake::mock('OpenOrchestra\GroupBundle\Repository\GroupRepository');
        Phake::when($groupRepository)->find(Phake::anyParameters())->thenReturn(null);

        $group0 = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group0)->getSite()->thenReturn($site);
        Phake::when($group0)->getLabels()->thenReturn(array());
        Phake::when($group0)->isDeleted()->thenReturn(false);
        $groupRepository0 = Phake::mock('OpenOrchestra\GroupBundle\Repository\GroupRepository');
        Phake::when($groupRepository0)->find(Phake::anyParameters())->thenReturn($group0);

        $group1 = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group1)->getSite()->thenReturn($site);
        Phake::when($group1)->getLabels()->thenReturn(array());
        Phake::when($group1)->isDeleted()->thenReturn(true);
        $groupRepository1 = Phake::mock('OpenOrchestra\GroupBundle\Repository\GroupRepository');
        Phake::when($groupRepository1)->find(Phake::anyParameters())->thenReturn($group1);

        $authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($authorizationChecker)->isGranted(ContributionActionInterface::READ, $group0)->thenReturn(true);
        Phake::when($authorizationChecker)->isGranted(ContributionActionInterface::READ, $group1)->thenReturn(false);

        return array(
            "prototype" => array($groupRepository, $authorizationChecker, array('groupName' => '__label__', 'siteName' => '__site.name__', 'deleted' => false, 'disabled' => false)),
            "allowed" => array($groupRepository0, $authorizationChecker, array('groupName' => 'fakeLabel', 'siteName' => 'fakeName', 'deleted' => false, 'disabled' => false)),
            "disallowed" => array($groupRepository1, $authorizationChecker, array('groupName' => 'fakeLabel', 'siteName' => 'fakeName', 'deleted' => true, 'disabled' => true)),
        );
    }
}
