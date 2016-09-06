<?php

namespace OpenOrchestra\GroupBundle\Tests\Document;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\Document\Group;
use Phake;

/**
 * Class GroupTest
 */
class GroupTest extends AbstractBaseTestCase
{
    /**
     * @var Group
     */
    protected $group;

    /**
     * set Up
     */
    public function setUp()
    {
        $this->group = new Group();
    }

    /**
     * test site
     */
    public function testSite()
    {
        $this->assertNull($this->group->getSite());

        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $this->group->setSite($site);
        $this->assertSame($site, $this->group->getSite());
    }

    /**
     * @param string $language
     * @param string $value
     *
     * @dataProvider provideLabelLanguageAndValue
     */
    public function testLabels($language, $value)
    {
        $this->group->addLabel($language, $value);

        $this->assertSame($value, $this->group->getLabel($language));
    }

    /**
     * @return array
     */
    public function provideLabelLanguageAndValue()
    {
        return array(
            array('en', 'foo'),
            array('fr', 'bar'),
        );
    }


    /**
     * @param string $name
     * @param string $expectedName
     * @param array  $labels
     * @param array  $expectedLabels
     *
     * @dataProvider provideLabelLanguageAndName
     */
    public function testClone($name, $expectedName, array $labels, array $expectedLabels)
    {
        $this->group->setName($name);
        $this->group->setLabels($labels);

        $cloneGroup = clone $this->group;

        $this->assertNull($cloneGroup->getId());
        $this->assertSame($cloneGroup->getName(), $expectedName);
        $this->assertSame($cloneGroup->getLabels(), $expectedLabels);
    }

    /**
     * @return array
     */
    public function provideLabelLanguageAndName()
    {
        return array(
            array('name', 'name_2', array('en' => 'labelen'), array('en' => 'labelen_2')),
            array('name_2', 'name_3', array('en' => 'labelen_2'), array('en' => 'labelen_3')),
            array('name_2_55', 'name_2_56', array('en' => 'labelen2_5'), array('en' => 'labelen2_6')),
        );
    }

    /**
     * @param array  $datas
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @dataProvider provideDatasAndNode
     */
    public function getModelGroupRoleByTypeAndIdAndRole(array $datas, $type, $id, $role)
    {
        foreach ($datas as $data) {
            $modelGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface');
            Phake::when($modelGroupRole)->getType()->thenReturn($data['type']);
            Phake::when($modelGroupRole)->getId()->thenReturn($data['id']);
            Phake::when($modelGroupRole)->getRole()->thenReturn($data['role']);
            $this->group->addModelGroupRole($modelGroupRole);
        }

        $modelGroupRole = $this->group->getModelGroupRoleByTypeAndIdAndRole($type, $id, $role);

        $this->assertSame($type, $modelGroupRole->getType());
        $this->assertSame($id, $modelGroupRole->getId());
        $this->assertSame($role, $modelGroupRole->getRole());
    }

    /**
     * @return array
     */
    public function provideDatasAndNode()
    {
        return array(
            array(array(array('type' => 'foo', 'id' => 'bar', 'role' => 'baz')), 'foo', 'bar', 'baz'),
            array(array(array('type' => 'foo', 'id' => 'bar', 'role' => 'baz'), array('type' => 'bar', 'id' => 'baz', 'role' => 'qux')), 'type', 'foo', 'bar'),
            array(array(array('type' => 'foo', 'id' => 'bar', 'role' => 'baz'), array('type' => 'bar', 'id' => 'baz', 'role' => 'qux')), 'bar', 'baz', 'qux'),
        );
    }

    /**
     * Test add node roles
     */
    public function addModelGroupRole()
    {
        $modelGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\ModelGroupRoleInterface');

        $this->group->addModelGroupRole($modelGroupRole);
        $this->group->addModelGroupRole($modelGroupRole);

        $this->assertCount(1, $this->group->getModelGroupRoles());
    }
}
