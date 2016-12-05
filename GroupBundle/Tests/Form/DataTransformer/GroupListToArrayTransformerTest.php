<?php

namespace OpenOrchestra\Backoffice\Tests\Form\DataTransformer;

use OpenOrchestra\GroupBundle\Form\DataTransformer\GroupListToArrayTransformer;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class GroupListToArrayTransformerTest
 */
class GroupListToArrayTransformerTest extends AbstractBaseTestCase
{
    protected $suppressSpecialCharacterHelper;
    protected $groupRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->groupRepository = Phake::mock('OpenOrchestra\GroupBundle\Repository\GroupRepository');
        $this->transformer = new GroupListToArrayTransformer($this->groupRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\DataTransformerInterface', $this->transformer);
    }

    /**
     * Test with null data
     */
    public function testTransformWithNullData()
    {
        $this->assertSame(array('groups_collection' => array()), $this->transformer->transform(null));
    }

    /**
     * @param array $groups
     * @param array $transformData
     *
     * @dataProvider providerGroups
     */
    public function testTransformWithGroups($groups, $transformData)
    {
        $this->assertSame($transformData, $this->transformer->transform($groups));
    }

    /**
     * @return array
     */
    public function providerGroups()
    {
        $fakeId = 'fakeId';

        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getId()->thenReturn($fakeId);

        return array(
            array(array(), array('groups_collection' => array())),
            array(array($group), array('groups_collection' => array($fakeId=> array('group' => true)))),
            array(array($group, $group), array('groups_collection' => array($fakeId => array('group' => true)))),
        );
    }

    /**
     * @param $data
     * Test with null data
     *
     * @dataProvider providerDifferentEmptyData
     */
    public function testReverseTransformWithNullAndEmptyData($data)
    {
        $result = $this->transformer->reverseTransform($data);
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $result);
        $this->assertEquals(0, count($result));
    }

    /**
     * @return array
     */
    public function providerDifferentEmptyData()
    {
        return array(
            array(null),
            array(''),
            array('  '),
        );
    }

    /**
     * @param array   $data
     * @param integer $numberOfFind
     *
     * @dataProvider providerArrayGroups
     */
    public function testReverseTransformWithGroups($data, $numberOfFind)
    {
        $this->transformer->reverseTransform($data);
        Phake::verify($this->groupRepository, Phake::times($numberOfFind))->find(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function providerArrayGroups()
    {
        return array(
            array(
                array(
                    'groups_collection' => array(
                        'fakeId0' => array('group' => true)
                    )
                ), 1
            ),
            array(
                array(
                    'groups_collection' => array(
                        'fakeId0' => array('group' => true),
                        'fakeId1' => array('group' => true),
                    )
                ), 2
            ),
            array(
                array(
                    'groups_collection' => array(
                        'fakeId0' => array('group' => false),
                        'fakeId1' => array('group' => true),
                    )
                ), 1
            ),
        );
    }
}
