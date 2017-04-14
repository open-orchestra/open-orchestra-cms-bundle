<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\GroupBundle\Form\DataTransformer\GroupPerimeterTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class GroupPerimeterTransformerTest
 */
class GroupPerimeterTransformerTest extends AbstractBaseTestCase
{
    protected $generatePerimeterManager;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->generatePerimeterManager = Phake::mock('OpenOrchestra\Backoffice\GeneratePerimeter\GeneratePerimeterManager');
        Phake::when($this->generatePerimeterManager)->generatePerimeters(Phake::anyParameters())->thenReturn(array(
            'node' => array(
                'root',
                'root/fixture_page_community',
                'root/fixture_page_news',
                'root/fixture_page_contact',
                'root/fixture_page_legal_mentions',
                'root/fixture_auto_unpublish',
            )
        ));
        $this->transformer = new GroupPerimeterTransformer($this->generatePerimeterManager);
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
     *
     * @param array $items
     * @param array $expectedResults
     *
     * @dataProvider provideItems
     */
    public function testTransform(array $items, array $expectedResults)
    {
        $perimeter = Phake::mock('OpenOrchestra\GroupBundle\Document\Perimeter');
        Phake::when($perimeter)->getType()->thenReturn('node');
        Phake::when($perimeter)->getItems()->thenReturn($items);

        $site = Phake::mock('OpenOrchestra\ModelBundle\Document\Site');
        Phake::when($site)->getSiteId()->thenReturn('siteId');

        $group = Phake::mock('OpenOrchestra\GroupBundle\Document\Group');
        Phake::when($group)->getSite()->thenReturn($site);

        $value = Phake::mock('Doctrine\ODM\MongoDB\PersistentCollection');
        Phake::when($value)->toArray()->thenReturn(array('node' => $perimeter));
        Phake::when($value)->getOwner()->thenReturn($group);

        $result = $this->transformer->transform($value);

        $this->assertEquals($expectedResults, $result);
    }

    /**
     * Test reverseTransform
     *
     * @param array $expectedResults
     * @param array $items
     *
     * @dataProvider provideItems
     */
    public function testReverseTransform(array $expectedResults, array $items)
    {
        $result = $this->transformer->reverseTransform($items);

        $this->assertArrayHasKey('node', $result);
        $this->assertInstanceOf('OpenOrchestra\GroupBundle\Document\Perimeter', $result['node']);
        $this->assertEquals($expectedResults, $result['node']->getItems());

    }

    /**
     * @return array
     */
    public function provideItems()
    {
        return array(
            array(array('root/fixture_page_news'), array(
                'node' => array(
                    'root' => false,
                    'root__fixture_page_community' => false,
                    'root__fixture_page_news' => true,
                    'root__fixture_page_contact' => false,
                    'root__fixture_page_legal_mentions' => false,
                    'root__fixture_auto_unpublish' => false,
                )
            )),
            array(array('root'), array(
                'node' => array(
                    'root' => true,
                    'root__fixture_page_community' => true,
                    'root__fixture_page_news' => true,
                    'root__fixture_page_contact' => true,
                    'root__fixture_page_legal_mentions' => true,
                    'root__fixture_auto_unpublish' => true,
                )
            )),
        );
    }

}
