<?php

namespace OpenOrchestra\BackofficeBundle\Tests\StrategyManager;

use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeStatusChangeManager;
use OpenOrchestra\BaseApi\Model\TokenInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * Test AuthorizeStatusChangeManagerTest
 */
class AuthorizeStatusChangeManagerTest extends AbstractBaseTestCase
{
    /**
     * @var AuthorizeStatusChangeManager
     */
    protected $manager;

    protected $strategy1;
    protected $strategy2;
    protected $tokenStorage;
    protected $user;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->tokenStorage = Phake::mock(TokenStorageInterface::class);
        $this->strategy1 = Phake::mock('OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface');
        Phake::when($this->strategy1)->getName()->thenReturn('strategy1');
        $this->strategy2 = Phake::mock('OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface');
        Phake::when($this->strategy2)->getName()->thenReturn('strategy2');

        $this->user  = Phake::mock(UserInterface::class);

        $token = Phake::mock(TokenInterface::class);
        Phake::when($token)->getUser()->thenReturn($this->user);
        Phake::when($this->tokenStorage)->getToken()->thenReturn($token);

        $this->manager = new AuthorizeStatusChangeManager($this->tokenStorage);
        $this->manager->addStrategy($this->strategy1);
        $this->manager->addStrategy($this->strategy2);
    }

    /**
     * @param bool $isGranted1
     * @param bool $isGranted2
     * @param bool $response
     *
     * @dataProvider provideGrantedAndResponse
     */
    public function testIsGranted($isGranted1, $isGranted2, $response)
    {
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $toStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->user)->isSuperAdmin()->thenReturn(false);

        Phake::when($this->strategy1)->isGranted(Phake::anyParameters())->thenReturn($isGranted1);
        Phake::when($this->strategy2)->isGranted(Phake::anyParameters())->thenReturn($isGranted2);

        $this->assertSame($response, $this->manager->isGranted($document, $toStatus));

        Phake::verify($this->strategy1, Phake::atMost(1))->isGranted($document, $toStatus);
        Phake::verify($this->strategy2, Phake::atMost(1))->isGranted($document, $toStatus);
        Phake::verify($document, Phake::never())->setStatus(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideGrantedAndResponse()
    {
        return array(
            array(true, true, true),
            array(true, false, false),
            array(false, true, false),
            array(false, false, false)
        );
    }

    /**
     * Test is granted with an user super admin
     */
    public function testIsGrantedSuperAdmin()
    {
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $toStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        Phake::when($this->user)->isSuperAdmin()->thenReturn(true);

        $this->assertTrue($this->manager->isGranted($document, $toStatus));
    }

    /**
     * Test exception is granted
     */
    public function testIsGrantedException()
    {
        $document = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        $toStatus = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');

        Phake::when($this->tokenStorage)->getToken()->thenReturn(null);


        $this->setExpectedException(AuthenticationCredentialsNotFoundException::class);

        $this->assertTrue($this->manager->isGranted($document, $toStatus));
    }
}
