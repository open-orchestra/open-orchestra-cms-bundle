<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\NodeVersionVoter;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test NodeVersionVoterTest
 */
class NodeVersionVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeVersionVoter
     */
    protected $voter;

    protected $token;
    protected $lastVersionNode;
    protected $repository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->lastVersionNode = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');
        Phake::when($this->repository)
            ->findInLastVersion(Phake::anyParameters())
            ->thenReturn($this->lastVersionNode);
        $this->token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->voter = new NodeVersionVoter($this->repository);
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
            array(false, '24051'),
            array(false, 'foo'),
        );
    }

    /**
     * @param string $document
     * @param bool   $support
     *
     * @dataProvider provideDocumentTypeAndSupport
     */
    public function testSupportsClass($document, $type, $support)
    {
        $document = Phake::mock($document);
        if ($type) {
            Phake::when($document)->getNodeType()->thenReturn($type);
        }

        $this->assertSame($support, $this->voter->supportsClass($document));
    }

    /**
     * @return array
     */
    public function provideDocumentTypeAndSupport()
    {
        return array(
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false, false),
            array('OpenOrchestra\ModelInterface\Model\ContentInterface', false, false),
            array('stdClass', false, false),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_DEFAULT, true),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_TRANSVERSE, false),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', NodeInterface::TYPE_ERROR, true),
        );
    }

    /**
     * @param int $nodeVersion
     * @param int $lastNodeVersion
     * @param int $response
     *
     * @dataProvider provideVersionAndEditable
     */
    public function testVote($nodeVersion, $lastNodeVersion, $response)
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getVersion()->thenReturn($nodeVersion);

        Phake::when($this->lastVersionNode)->getVersion()->thenReturn($lastNodeVersion);

        $this->assertSame($response, $this->voter->vote($this->token, $node, array()));
    }

    /**
     * @return array
     */
    public function provideVersionAndEditable()
    {
        return array(
            array(1, 2, VoterInterface::ACCESS_DENIED),
            array(2, 2, VoterInterface::ACCESS_GRANTED),
            array(3, 2, VoterInterface::ACCESS_GRANTED),
        );
    }

    /**
     * Test with no node
     */
    public function testVoteWithNoNode()
    {
        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->repository)->findInLastVersion(Phake::anyParameters())->thenReturn(null);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, $node, array()));
    }

    /**
     * Test with not a node passed
     */
    public function testVoteWithNotNodeObject()
    {
        $object = Phake::mock('stdClass');

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($this->token, $object, array()));
    }
}
