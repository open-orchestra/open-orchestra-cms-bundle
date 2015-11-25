<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\ContentVersionVoter;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test ContentVersionVoterTest
 */
class ContentVersionVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentVersionVoter
     */
    protected $voter;

    protected $token;
    protected $lastVersionContent;
    protected $repository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->lastVersionContent = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface');
        Phake::when($this->repository)
            ->findOneByLanguage(Phake::anyParameters())
            ->thenReturn($this->lastVersionContent);
        $this->token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->voter = new ContentVersionVoter($this->repository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface', $this->voter);
    }

    /**
     * Test support attributes
     */
    public function testSupportAttributes()
    {
        $this->assertTrue($this->voter->supportsAttribute('foo'));
    }

    /**
     * @param string $document
     * @param bool   $support
     *
     * @dataProvider provideDocumentTypeAndSupport
     */
    public function testSupportsClass($document, $support)
    {
        $document = Phake::mock($document);

        $this->assertSame($support, $this->voter->supportsClass($document));
    }

    /**
     * @return array
     */
    public function provideDocumentTypeAndSupport()
    {
        return array(
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false),
            array('OpenOrchestra\ModelInterface\Model\ContentInterface', true),
            array('stdClass', false),
            array('OpenOrchestra\ModelInterface\Model\NodeInterface', false),
        );
    }

    /**
     * @param int $ContentVersion
     * @param int $lastContentVersion
     * @param int $response
     *
     * @dataProvider provideVersionAndEditable
     */
    public function testVote($ContentVersion, $lastContentVersion, $response)
    {
        $Content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($Content)->getVersion()->thenReturn($ContentVersion);

        Phake::when($this->lastVersionContent)->getVersion()->thenReturn($lastContentVersion);

        $this->assertSame($response, $this->voter->vote($this->token, $Content, array()));
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
     * Test with no Content
     */
    public function testVoteWithNoContent()
    {
        $Content = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentInterface');
        Phake::when($this->repository)->findOneByLanguage(Phake::anyParameters())->thenReturn(null);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token, $Content, array()));
    }

    /**
     * Test with not a Content passed
     */
    public function testVoteWithNotContentObject()
    {
        $object = Phake::mock('stdClass');

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($this->token, $object, array()));
    }
}
