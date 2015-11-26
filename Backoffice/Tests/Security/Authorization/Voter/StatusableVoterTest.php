<?php

namespace OpenOrchestra\Backoffice\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\StatusableVoter;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test StatusableVoterTest
 */
class StatusableVoterTest extends AbstractBaseTestCase
{
    /**
     * @var StatusableVoter
     */
    protected $voter;

    protected $token;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->voter = new StatusableVoter();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface', $this->voter);
    }

    /**
     * @param bool   $supports
     * @param string $role
     *
     * @dataProvider provideRoleAndSupports
     */
    public function testSupportAttributes($supports, $role)
    {
        $this->assertSame($supports, $this->voter->supportsAttribute($role));
    }

    /**
     * @return array
     */
    public function provideRoleAndSupports()
    {
        return array(
            array(true, 'ROLE_USER'),
            array(true, 'ROLE_ACCESS'),
            array(false, 'ROLE_ACCESS_TREE_NODE'),
            array(false, 'ROLE_ACCESS_CREATE_NODE'),
            array(false, '24051'),
            array(false, 'foo'),
        );
    }

    /**
     * @param bool   $supports
     * @param string $class
     * @param bool   $status
     *
     * @dataProvider provideSupportsAnsClass
     */
    public function testSuportClass($supports, $class, $status = false)
    {
        $object = Phake::mock($class);
        if ($status) {
            $statusObject = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
            Phake::when($object)->getStatus()->thenReturn($statusObject);
        }

        $this->assertSame($supports, $this->voter->supportsClass($object));
    }

    /**
     * @return array
     */
    public function provideSupportsAnsClass()
    {
        return array(
            array(true, 'OpenOrchestra\ModelInterface\Model\NodeInterface', true),
            array(true, 'OpenOrchestra\ModelInterface\Model\ContentInterface', true),
            array(false, 'OpenOrchestra\ModelInterface\Model\NodeInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\ContentInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\TemplateInterface'),
            array(false, 'OpenOrchestra\ModelInterface\Model\BlockInterface'),
            array(false, 'stdClass'),
        );
    }

    /**
     * @param bool   $published
     * @param string $response
     *
     * @dataProvider provideStatusAndResponse
     */
    public function testVoteWithStatusableElement($published, $response)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isPublished()->thenReturn($published);
        $object = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($object)->getStatus()->thenReturn($status);

        $this->assertSame($response, $this->voter->vote($this->token, $object, array()));
    }

    /**
     * @return array
     */
    public function provideStatusAndResponse()
    {
        return array(
            array(true, VoterInterface::ACCESS_DENIED),
            array(false, VoterInterface::ACCESS_GRANTED),
        );
    }

    /**
     * Test with object not statusable
     */
    public function testVoteWithNoStatusable()
    {
        $object = Phake::mock('stdClass');

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($this->token, $object, array()));
    }
}
