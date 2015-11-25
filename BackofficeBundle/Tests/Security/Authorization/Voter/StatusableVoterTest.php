<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\StatusableVoter;
use Phake;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Test StatusableVoterTest
 */
class StatusableVoterTest extends \PHPUnit_Framework_TestCase
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
     * Test support attributes
     */
    public function testSupportAttributes()
    {
        $this->assertTrue($this->voter->supportsAttribute('foo'));
    }

    /**
     * @param bool   $supports
     * @param string $class
     *
     * @dataProvider provideSupportsAnsClass
     */
    public function testSuuportClass($supports, $class)
    {
        $this->assertSame($supports, $this->voter->supportsClass($class));
    }

    /**
     * @return array
     */
    public function provideSupportsAnsClass()
    {
        return array(
            array(true, 'OpenOrchestra\ModelInterface\Model\NodeInterface'),
            array(true, 'OpenOrchestra\ModelInterface\Model\ContentInterface'),
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
