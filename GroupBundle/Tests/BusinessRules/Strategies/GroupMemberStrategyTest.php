<?php
namespace OpenOrchestra\GroupBundle\Tests\BusinessRules\Strategies;

use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\GroupBundle\BusinessRules\Strategies\GroupMemberStrategy;

/**
 * Class GroupMemberStrategyTest
 */
class GroupMemberStrategyTest extends AbstractBaseTestCase
{
    protected $userRepository;
    protected $idGroup0 = 'fake_id_group0';
    protected $idGroup1 = 'fake_id_group1';

    /**
     * setUp
     */
    public function setUp()
    {
        $this->userRepository = Phake::mock('OpenOrchestra\UserBundle\Repository\UserRepositoryInterface');
        Phake::when($this->userRepository)->getCountsUsersByGroups(array($this->idGroup0))->thenReturn(array($this->idGroup0 => 0));
        Phake::when($this->userRepository)->getCountsUsersByGroups(array($this->idGroup1))->thenReturn(array($this->idGroup1 => 1));

        $this->strategy = new GroupMemberStrategy($this->userRepository);
    }

    /**
     * @param mixed   $entity
     * @param boolean $isSupported
     *
     * @dataProvider provideEntity
     */
    public function testSupportObject($entity, $isSupported)
    {
        $this->assertSame($isSupported, $this->strategy->supportEntity($entity));
    }

    /**
     * provide entity
     *
     * @return array
     */
    public function provideEntity()
    {
        return array(
            array(Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface'), false),
            array(Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface'), true),
        );
    }

    /**
     * @param GroupInterface $group
     * @param array          $parameters
     * @param boolean        $isGranted
     *
     * @dataProvider provideGroupAndParameters
     */
    public function testCanDelete(GroupInterface $group, array $parameters, $isGranted)
    {
        $this->assertSame($isGranted, $this->strategy->canDelete($group, $parameters));
    }

    /**
     * provide group and parameters
     *
     * @return array
     */
    public function provideGroupAndParameters()
    {
        $group0 = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group0)->getId()->thenReturn($this->idGroup0);

        $group1 = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group1)->getId()->thenReturn($this->idGroup1);

        return array(
            array($group0, array(), true),
            array($group1, array(), false),
            array($group0, array($this->idGroup0 => 1), false),
            array($group1, array($this->idGroup1 => 0), true),
        );
    }
}
