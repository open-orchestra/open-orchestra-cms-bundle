<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\WorkflowAdminBundle\Form\DataTransformer\GroupWorkflowProfileCollectionTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GroupWorkflowProfileCollectionTransformerTest
 */
class GroupWorkflowProfileCollectionTransformerTest extends AbstractBaseTestCase
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $workflowProfile = Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface');
        Phake::when($workflowProfile)->getLabel()->thenReturn('fakeWorkflowProfileLabel');
        Phake::when($workflowProfile)->getId()->thenReturn('fakeId');
        $workflowProfileRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\WorkflowProfileRepository');
        Phake::when($workflowProfileRepository)->findAll()->thenReturn(array($workflowProfile));

        $contenType = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contenType)->getContentTypeId()->thenReturn('fakeContentTypeId');
        Phake::when($contenType)->getName(Phake::anyParameters())->thenReturn('fakeName');
        $contentTypeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface');
        Phake::when($contentTypeRepository)->findAllNotDeletedInLastVersion()->thenReturn(array($contenType));

        $contextManager = Phake::mock('OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface');

        $this->transformer = new GroupWorkflowProfileCollectionTransformer(
            $workflowProfileRepository,
            $contentTypeRepository,
            $contextManager
        );
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
        $workflowProfile = Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface');
        Phake::when($workflowProfile)->getId()->thenReturn('fakeId');
        $workflowProfileCollection = Phake::mock('OpenOrchestra\ModelBundle\Document\WorkflowProfileCollection');
        Phake::when($workflowProfileCollection)->getProfiles()->thenReturn(array($workflowProfile));

        $value = new ArrayCollection();
        $value->set('node', $workflowProfileCollection);

        $result = $this->transformer->transform($value);

        $this->assertEquals(array('node' => array('fakeId' => true), 'fakeContentTypeId' => array('fakeId' => false)), $result);
    }

    /**
     * Test reverseTransform
     */
    public function testReverseTransform()
    {
        $value = array('node' => array('fakeId' => true), 'fakeContentTypeId' => array('fakeId' => false));
        $result = $this->transformer->reverseTransform($value);

        $this->assertArrayHasKey('node', $result);
        $this->assertArrayHasKey('fakeContentTypeId', $result);
    }
}
