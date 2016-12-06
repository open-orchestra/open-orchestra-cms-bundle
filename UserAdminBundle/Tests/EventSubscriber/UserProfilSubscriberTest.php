<?php

namespace OpenOrchestra\UserAdminBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserAdminBundle\EventSubscriber\UserProfilSubscriber;
use OpenOrchestra\UserBundle\Model\UserInterface;
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

    /**
     * Set up common test part
     */
    public function setUp()
    {
        $this->user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');

        Phake::when($this->event)->getData()->thenReturn($this->user);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new UserProfilSubscriber($this->user, $this->objectManager);
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
     * @param UserInterface $user
     * @param integer       $nbrAdd
     *
     * @dataProvider provideUser
     */
    public function testPreSetData(UserInterface $user, $nbrAdd)
    {
        $subscriber = new UserProfilSubscriber($user, $this->objectManager);

        $subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::times($nbrAdd))->add(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideUser()
    {
        $developper = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($developper)->hasRole(ContributionRoleInterface::DEVELOPER)->thenReturn(true);

        $admin = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($admin)->hasRole(ContributionRoleInterface::PLATFORM_ADMIN)->thenReturn(true);

        return array(
            array($developper, 2),
            array($admin, 1),
        );
    }


    /**
     * Test pre submit
     *
     * @param UserInterface $user
     * @param array         $data
     * @param integer       $nbrAdd
     *
     * @dataProvider provideSubmit
     */
    public function testPreSubmit(UserInterface $user, $data, $nbrAdd)
    {
        $fakeUser = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($this->event)->getData()->thenReturn($data);
        Phake::when($this->form)->getData()->thenReturn($fakeUser);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $subscriber = new UserProfilSubscriber($user, $this->objectManager);

        $subscriber->preSubmit($this->event);

        Phake::verify($fakeUser, Phake::times($nbrAdd))->addRole(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideSubmit()
    {
        $developper = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($developper)->hasRole(ContributionRoleInterface::DEVELOPER)->thenReturn(true);

        $admin = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($admin)->hasRole(ContributionRoleInterface::PLATFORM_ADMIN)->thenReturn(true);

        return array(
            array($developper, array('platform_admin' => true, 'developer' => true), 2),
            array($admin, array('platform_admin' => true, 'developer' => true), 1),
        );
    }


}
