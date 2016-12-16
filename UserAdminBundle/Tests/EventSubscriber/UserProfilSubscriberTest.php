<?php

namespace OpenOrchestra\UserAdminBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserAdminBundle\EventSubscriber\UserProfilSubscriber;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Form\FormEvents;
use Phake;

/**
 * Class UserProfilSubscriberTest
 */
class UserProfilSubscriberTest extends AbstractBaseTestCase
{

    protected $event;
    protected $user;
    protected $form;
    protected $subscriber;
    protected $authorizationChecker;

    /**
     * Set up common test part
     */
    public function setUp()
    {
        $this->user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($this->event)->getData()->thenReturn($this->user);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new UserProfilSubscriber($this->authorizationChecker);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test pre set data
     *
     * @param bool    $developer
     * @param bool    $adminPlatform
     * @param integer $nbrAdd
     *
     * @dataProvider provideUser
     */
    public function testPreSetData($developer, $adminPlatform, $nbrAdd)
    {
        Phake::when($this->authorizationChecker)->isGranted(ContributionRoleInterface::PLATFORM_ADMIN)->thenReturn($developer);
        Phake::when($this->authorizationChecker)->isGranted(ContributionRoleInterface::DEVELOPER)->thenReturn($adminPlatform);
        $subscriber = new UserProfilSubscriber($this->authorizationChecker);
        $subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::times($nbrAdd))->add(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideUser()
    {
        return array(
            array(true, true, 2),
            array(false, true, 1),
        );
    }


    /**
     * Test pre submit
     *
     * @param bool    $developer
     * @param bool    $adminPlatform
     * @param array   $data
     * @param integer $nbrAdd
     *
     * @dataProvider provideSubmit
     */
    public function testPreSubmit($developer, $adminPlatform, $data, $nbrAdd)
    {
        Phake::when($this->authorizationChecker)->isGranted(ContributionRoleInterface::PLATFORM_ADMIN)->thenReturn($developer);
        Phake::when($this->authorizationChecker)->isGranted(ContributionRoleInterface::DEVELOPER)->thenReturn($adminPlatform);
        $subscriber = new UserProfilSubscriber($this->authorizationChecker);

        $fakeUser = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->form)->getData()->thenReturn($fakeUser);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $subscriber->preSubmit($this->event);

        Phake::verify($fakeUser, Phake::times($nbrAdd))->addRole(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideSubmit()
    {
        return array(
            array(true, true, array('platform_admin' => true, 'developer' => true), 2),
            array(false, true, array('platform_admin' => true, 'developer' => true), 1),
        );
    }


}
