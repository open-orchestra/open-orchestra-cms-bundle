<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\Backoffice\EventSubscriber\RoleTypeSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Form\FormEvents;

/**
 * Test RoleTypeSubscriberTest
 */
class RoleTypeSubscriberTest extends AbstractBaseTestCase
{
    protected $fromStatus = 'fakeFromStatus';
    protected $toStatus = 'fakeToStatus';
    protected $message = 'fakeMessage';

    protected $statusRepository;
    protected $translator;

    protected $event;
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        Phake::when($this->event)->getForm()->thenReturn($this->form);
        Phake::when($this->event)->getData()->thenReturn(array(
            'fromStatus' => $this->fromStatus,
            'toStatus' => $this->toStatus
        ));
        $fromStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $toStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find($this->fromStatus)->thenReturn($fromStatus);
        Phake::when($this->statusRepository)->find($this->toStatus)->thenReturn($toStatus);
        $this->translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        Phake::when($this->translator)->trans(Phake::anyParameters())->thenReturn($this->message);
    }

    /**
     * @param RoleInterface|null $existingRole
     * @param int                $nbrError
     *
     * @dataProvider provideRoleRepository
     */
    public function testPreSubmit($existingRole, $nbrError)
    {
        $roleRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface');
        Phake::when($roleRepository)->findOneByFromStatusAndToStatus(Phake::anyParameters())->thenReturn($existingRole);

        $subscriber = new RoleTypeSubscriber($roleRepository, $this->statusRepository, $this->translator);
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $subscriber->getSubscribedEvents());
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $subscriber);

        $subscriber->preSubmit($this->event);

        Phake::verify($this->form, Phake::times($nbrError))->addError(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideRoleRepository()
    {
        return array(
            array(Phake::mock('OpenOrchestra\ModelInterface\Model\RoleInterface'), 1),
            array(null, 0),
        );
    }
}
