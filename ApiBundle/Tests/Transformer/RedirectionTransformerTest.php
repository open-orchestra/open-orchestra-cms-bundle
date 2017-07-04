<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ApiBundle\Transformer\ContentTransformer;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Transformer\RedirectionTransformer;

/**
 * Class RedirectionTransformerTest
 */
class RedirectionTransformerTest extends AbstractBaseTestCase
{
    /**
     * @var ContentTransformer
     */
    protected $transformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\RedirectionFacade';
    protected $redirectionRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $this->redirectionRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface');
        Phake::when($this->redirectionRepository)->find(Phake::anyParameters())->thenReturn('ok');

        $this->transformer = new RedirectionTransformer(
            $this->facadeClass,
            $authorizationChecker,
            $this->redirectionRepository
        );
    }

    /**
     * test reverseTransform
     *
     * @param FacadeInterface  $facade
     * @param ContentInterface $source
     * @param int              $searchCount
     * @param int              $setCount
     *
     * @dataProvider facadeProvider
     */
    public function testReverseTransform($facade, $searchCount, $nullReturn)
    {
        $redirection = $this->transformer->reverseTransform($facade);

        Phake::verify($this->redirectionRepository, Phake::times($searchCount))->find(Phake::anyParameters());
        if ($nullReturn) {
            $this->assertNull($redirection);
        } else {
            $this->assertNotNull($redirection);
        }
    }

    /**
     * @return array
     */
    public function facadeProvider()
    {
        $facade1 = Phake::mock('OpenOrchestra\ApiBundle\Facade\RedirectionFacade');
        $facade2 = Phake::mock('OpenOrchestra\ApiBundle\Facade\ContentFacade');
        $facade2->id = 'id';

        return array(
            array($facade1, 0, true),
            array($facade2, 1, false)
        );
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('redirection', $this->transformer->getName());
    }
}
