<?php

namespace OpenOrchestra\WorkflowAdminBundle\Tests\Transformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\WorkflowAdminBundle\Transformer\WorkflowProfileTransformer;
use Phake;

/**
 * Class WorkflowProfileTransformerTest
 */
class WorkflowProfileTransformerTest extends AbstractBaseTestCase
{
    protected $facadeClass = 'OpenOrchestra\WorkflowAdminBundle\Facade\WorkflowProfileFacade';
    protected $transformerManager;
    protected $transformer;
    protected $workflowProfile;
    protected $workflowProfileRepository;
    protected $multiLanguagesChoiceManager;
    protected $workflowProfileId;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->multiLanguagesChoiceManager = Phake::mock('OpenOrchestra\ModelInterface\Manager\MultiLanguagesChoiceManagerInterface');
        $this->workflowProfile = Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface');

        $transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        $this->workflowProfileRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface');

        $this->workflowProfileId = 'workflowProfileIde';

        Phake::when($this->workflowProfile)->getId()->thenReturn($this->workflowProfileId);
        Phake::when($this->workflowProfileRepository)->find(Phake::anyParameters())->thenReturn($this->workflowProfile);
        $authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        Phake::when($authorizationChecker)->isGranted(Phake::anyParameters())->thenReturn(true);
        $groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');
        Phake::when($transformerManager)->getGroupContext()->thenReturn($groupContext);
        Phake::when($groupContext)->hasGroup(Phake::anyParameters())->thenReturn(true);

        $this->transformer = new WorkflowProfileTransformer(
            $this->facadeClass,
            $authorizationChecker,
            $this->multiLanguagesChoiceManager,
            $this->workflowProfileRepository
        );
        $this->transformer->setContext($transformerManager);
    }

    /**
     * Test transform
     */
    public function testTransform()
    {
        $fakeString = 'fakeString';
        Phake::when($this->multiLanguagesChoiceManager)->choose(Phake::anyParameters())->thenReturn($fakeString);
        Phake::when($this->workflowProfile)->getLabels()->thenReturn(array());
        Phake::when($this->workflowProfile)->getDescriptions()->thenReturn(array());

        $facade = $this->transformer->transform($this->workflowProfile);

        $this->assertInstanceOf('OpenOrchestra\BaseApi\Facade\FacadeInterface', $facade);
        $this->assertSame($this->workflowProfileId, $facade->id);
        $this->assertSame($fakeString, $facade->label);
        $this->assertSame($fakeString, $facade->description);
    }

    /**
     * Test Exception transform with wrong object a parameters
     */
    public function testExceptionTransform()
    {
        $this->expectException('OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException');
        $this->transformer->transform(Phake::mock('stdClass'));
    }

    /**
     * Test reverse transform
     */
    public function testReverseTransform()
    {
        $facade = Phake::mock('OpenOrchestra\BaseApi\Facade\FacadeInterface');
        $facade->id = $this->workflowProfileId;
        $this->transformer->reverseTransform($facade);

        Phake::verify($this->workflowProfileRepository)->find(Phake::anyParameters());
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('workflow_profile', $this->transformer->getName());
    }
}
