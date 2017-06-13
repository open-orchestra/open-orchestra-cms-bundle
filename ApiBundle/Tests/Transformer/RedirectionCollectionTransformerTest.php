<?php

namespace OpenOrchestra\ApiBundle\Tests\Transformer;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Transformer\RedirectionCollectionTransformer;
use OpenOrchestra\ApiBundle\Facade\RedirectionCollectionFacade;

/**
 * Class RedirectionCollectionTransformerTest
 */
class RedirectionCollectionTransformerTest extends AbstractBaseTestCase
{
    protected $transformer;

    protected $facadeClass = 'OpenOrchestra\ApiBundle\Facade\RedirectionCollectionFacade';
    protected $redirectionTransformer;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');

        $this->redirectionTransformer = Phake::mock('OpenOrchestra\ApiBundle\Transformer\RedirectionTransformer');
        $transformerManager = Phake::mock('OpenOrchestra\BaseApi\Transformer\TransformerManager');
        Phake::when($transformerManager)->get('redirection')->thenReturn($this->redirectionTransformer);

        $this->transformer = new RedirectionCollectionTransformer(
            $this->facadeClass,
            $authorizationChecker
        );
        $this->transformer->setContext($transformerManager);
    }

    /**
     * test reverseTransform
     *
     * @param FacadeInterface  $facade
     * @param ContentInterface $source
     * @param int              $searchCount
     * @param int              $setCount
     *
     * @dataProvider facadesProvider
     */
    public function testReverseTransform($facadeCollection, $withFirstTransfoNull, $transformationCount, $expectedSize)
    {
        foreach ($facadeCollection->getRedirections() as $key => $facade) {
            if (0 == $key && $withFirstTransfoNull) {
                Phake::when($this->redirectionTransformer)->reverseTransform($facade)->thenReturn(null);
            } else {
                Phake::when($this->redirectionTransformer)->reverseTransform($facade)->thenReturn('ok');
            }
        }

        $collection = $this->transformer->reverseTransform($facadeCollection, null);

        Phake::verify($this->redirectionTransformer, Phake::times($transformationCount))->reverseTransform(Phake::anyParameters());
        $this->assertCount($expectedSize, $collection);
    }

   /**
     * @return array
     */
    public function facadesProvider()
    {
        $facade1 = Phake::mock('OpenOrchestra\ApiBundle\Facade\RedirectionFacade');
        $facade2 = Phake::mock('OpenOrchestra\ApiBundle\Facade\RedirectionFacade');
        $facade3 = Phake::mock('OpenOrchestra\ApiBundle\Facade\RedirectionFacade');

        $facadeCollection1 =  Phake::mock('OpenOrchestra\ApiBundle\Facade\RedirectionCollectionFacade');
        Phake::when($facadeCollection1)->getRedirections()->thenReturn(array());

        $facadeCollection2 =  Phake::mock('OpenOrchestra\ApiBundle\Facade\RedirectionCollectionFacade');
        Phake::when($facadeCollection2)->getRedirections()->thenReturn(array($facade1));

        $facadeCollection3 =  Phake::mock('OpenOrchestra\ApiBundle\Facade\RedirectionCollectionFacade');
        Phake::when($facadeCollection3)->getRedirections()->thenReturn(array($facade1, $facade2, $facade3));

        return array(
            '0 item'  => array($facadeCollection1, false, 0, 0),
            '1 item'  => array($facadeCollection2, false, 1, 1),
            '3 items' => array($facadeCollection3, true , 3, 2)
        );
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertSame('redirection_collection', $this->transformer->getName());
    }
}
