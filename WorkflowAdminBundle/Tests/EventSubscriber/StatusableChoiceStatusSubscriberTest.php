<?php

namespace OpenOrchestra\Backoffice\Tests\EventSubscriber;

use OpenOrchestra\WorkflowAdminBundle\EventSubscriber\StatusableChoiceStatusSubscriber;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Phake;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class StatusableChoiceStatusSubscriberTest
 */
class StatusableChoiceStatusSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var StatusableChoiceStatusSubscriber
     */
    protected $subscriber;

    protected $statusRepository;
    protected $authorizationChecker;
    protected $event;
    protected $object;
    protected $form;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->object = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');

        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');

        Phake::when($this->event)->getData()->thenReturn($this->object);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new StatusableChoiceStatusSubscriber($this->statusRepository, $this->authorizationChecker, array(
            'label' => 'open_orchestra_backoffice.form.node.status',
            'group_id' => 'properties',
            'sub_group_id' => 'publication',
        ));
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array($this->subscriber, 'preSetData')));
    }

    /**
     * Test pre set data with new node
     */
    public function testPreSetDataWithoutId()
    {
        Phake::when($this->object)->getId()->thenReturn(null);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::never())->add(Phake::anyParameters());
    }

    /**
     * Test pre set data
     *
     * @param StatusInterface $status
     * @param bool            $isGranted
     * @param array           $expectedStatus
     *
     * @dataProvider provideStatus
     */
    public function testPreSetData($status, $isGranted, array $expectedStatus)
    {
        Phake::when($this->authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn($isGranted);
        Phake::when($this->object)->getId()->thenReturn('fakeId');
        Phake::when($this->statusRepository)->findAll()->thenReturn($expectedStatus);
        Phake::when($this->object)->getStatus()->thenReturn($status);

        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form, Phake::times(2))->add(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideStatus()
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->getId()->thenReturn('status');
        $status2 = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status2)->getId()->thenReturn('status2');

        return array(
            array($status, true , array($status, $status2)),
            array($status, false, array($status)),
        );
    }
}
