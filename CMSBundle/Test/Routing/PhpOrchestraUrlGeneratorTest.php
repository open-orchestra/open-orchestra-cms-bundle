<?php

namespace PHPOrchestra\CMSBundle\Test\Routing;

use Phake;
use PHPOrchestra\CMSBundle\Routing\PhpOrchestraUrlGenerator;

/**
 * Tests of PhpOrchestraUrlGenerator
 *
 * @author Noël GILAIN <oel.gilain@businessdecision.com>
 */
class PhpOrchestraUrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $node;
    protected $context;
    protected $generator;
    protected $nodeRepsitory;
    protected $httpPort = 80;
    protected $httpsPort = 444;
    protected $host = 'some-site.com';

    /**
     * Set up the test
     */
    public function setUp()
    {
        $routes = Phake::mock('Symfony\Component\Routing\RouteCollection');
        Phake::when($routes)->get(Phake::anyParameters())->thenReturn(null);

        $this->context = Phake::mock('Symfony\Component\Routing\RequestContext');
        Phake::when($this->context)->getHttpPort(Phake::anyParameters())->thenReturn($this->httpPort);
        Phake::when($this->context)->getHttpsPort(Phake::anyParameters())->thenReturn($this->httpsPort);
        Phake::when($this->context)->getHost(Phake::anyParameters())->thenReturn($this->host);

        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        $this->nodeRepsitory = Phake::mock('PHPOrchestra\ModelBundle\Repository\NodeRepository');
        Phake::when($this->nodeRepsitory)->findOneByNodeId(Phake::anyParameters())->thenReturn($this->node);

        $this->generator = new PhpOrchestraUrlGenerator(
            $routes,
            $this->context,
            $this->nodeRepsitory
        );
    }

    /**
     * @dataProvider generateDataProvider
     */
    public function testGenerate($scheme, $nodeId, $refType, $expected)
    {
        Phake::when($this->context)->getScheme()->thenReturn($scheme);
        Phake::when($this->node)->getAlias()->thenReturn($nodeId);
        Phake::when($this->node)->getParentId()->thenReturn('root');

        $uriGenerated = $this->generator->generate($nodeId, array(), $refType);

        $this->assertEquals($expected, $uriGenerated);
        Phake::verify($this->nodeRepsitory)->findOneByNodeId($nodeId);
        Phake::verify($this->node)->getParentId();
        Phake::verify($this->node)->getAlias();
    }

    /**
     * @return array
     */
    public function generateDataProvider()
    {
        return array(
            array(
                'http',
                'page2',
                PhpOrchestraUrlGenerator::RELATIVE_PATH,
                'page2'
            ),
            array(
                'https',
                'page',
                PhpOrchestraUrlGenerator::ABSOLUTE_URL,
                'https://some-site.com:444/page'
            ),
            array(
                'http',
                'page1',
                PhpOrchestraUrlGenerator::NETWORK_PATH,
                '//some-site.com/page1'
            ),
        );
    }

    /**
     * test with parent
     *
     * @param string $alias
     *
     * @dataProvider provideAlias
     */
    public function testGenerateWithParent($alias)
    {
        $parentId = 'parent';
        $nodeId = 'node';
        $rootId = 'root';

        $nodeParent = Phake::mock('PHPOrchestra\ModelBundle\Model\NodeInterface');
        Phake::when($nodeParent)->getParentId()->thenReturn($rootId);
        Phake::when($nodeParent)->getAlias()->thenReturn($alias);
        Phake::when($this->nodeRepsitory)->findOneByNodeId($parentId)->thenReturn($nodeParent);

        Phake::when($this->node)->getAlias()->thenReturn($alias);
        Phake::when($this->node)->getParentId()->thenReturn($parentId);

        $uriGenerated = $this->generator->generate($nodeId, array());

        $this->assertEquals('/' . $alias . '/'. $alias, $uriGenerated);
        Phake::verify($this->nodeRepsitory)->findOneByNodeId($nodeId);
        Phake::verify($this->node)->getParentId();
        Phake::verify($this->node)->getAlias();
    }

    /**
     * @return array
     */
    public function provideAlias()
    {
        return array(
            array('test'),
            array('alias'),
            array('other'),
        );
    }
}
