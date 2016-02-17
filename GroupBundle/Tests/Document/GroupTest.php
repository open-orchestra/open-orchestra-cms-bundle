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
        $label = Phake::mock('OpenOrchestra\ModelInterface\Model\TranslatedValueInterface');
        Phake::when($label)->getLanguage()->thenReturn($language);
        Phake::when($label)->getValue()->thenReturn($value);
        $this->group->addLabel($label);

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
     * test getTranslatedProperties
     */
    public function testGetTranslatedProperties()
    {
        $this->assertSame(array('getLabels'), $this->group->getTranslatedProperties());
    }

    /**
     * @param array  $datas
     * @param string $type
     * @param string $id
     * @param string $role
     *
     * @dataProvider provideDatasAndNode
     */
    public function getDocumentRoleByTypeAndIdAndRole(array $datas, $type, $id, $role)
    {
        foreach ($datas as $data) {
            $documentGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\DocumentGroupRoleInterface');
            Phake::when($documentGroupRole)->getType()->thenReturn($data['type']);
            Phake::when($documentGroupRole)->getId()->thenReturn($data['id']);
            Phake::when($documentGroupRole)->getRole()->thenReturn($data['role']);
            $this->group->addDocumentRole($documentGroupRole);
        }

        $documentGroupRole = $this->group->getDocumentRoleByTypeAndIdAndRole($type, $id, $role);

        $this->assertSame($type, $documentGroupRole->getType());
        $this->assertSame($id, $documentGroupRole->getId());
        $this->assertSame($role, $documentGroupRole->getRole());
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
    public function addDocumentRole()
    {
        $documentGroupRole = Phake::mock('OpenOrchestra\Backoffice\Model\DocumentGroupRoleInterface');
        $this->group->addDocumentRole($documentGroupRole);
        $this->group->addDocumentRole($documentGroupRole);

        $this->assertCount(1, $this->group->getDocumentRoles());
    }
}
