<?php

namespace PHPOrchestra\BackofficeBundle\Test\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Phake;
use PHPOrchestra\BackofficeBundle\Form\DataTransformer\EmbedStatusToStatusTransformer;

/**
 * Class EmbedStatusToStatusTransformerTest
 */
class EmbedStatusToStatusTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmbedStatusToStatusTransformer
     */
    protected $transformer;

    protected $status;
    protected $statusId;
    protected $embedStatus;
    protected $statusRepository;
    protected $embedStatusClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->embedStatusClass = 'PHPOrchestra\ModelBundle\Document\EmbedStatus';
        $this->statusId = 'statusId';

        $this->status = Phake::mock('PHPOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($this->status)->getId()->thenReturn($this->statusId);
        Phake::when($this->status)->getToRoles()->thenReturn(new ArrayCollection());
        Phake::when($this->status)->getFromRoles()->thenReturn(new ArrayCollection());

        $this->embedStatus = Phake::mock('PHPOrchestra\ModelBundle\Document\EmbedStatus');
        Phake::when($this->embedStatus)->getId()->thenReturn($this->statusId);

        $this->statusRepository = Phake::mock('PHPOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        Phake::when($this->statusRepository)->find(Phake::anyParameters())->thenReturn($this->status);

        $this->transformer = new EmbedStatusToStatusTransformer($this->statusRepository, $this->embedStatusClass);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * Test transform
     */
    public function testTransform()
    {
        $status = $this->transformer->transform($this->embedStatus);

        $this->assertSame($this->status, $status);
    }

    /**
     * Test reverse transform
     */
    public function testReverseTransform()
    {
        $embedStatus = $this->transformer->reverseTransform($this->status);

        $this->assertInstanceOf('PHPOrchestra\ModelInterface\Model\EmbedStatusInterface', $embedStatus);
        $this->assertSame($this->statusId, $embedStatus->getId());
    }
}
