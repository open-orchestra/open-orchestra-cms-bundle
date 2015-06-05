<?php

namespace OpenOrchestra\UserBundle\Tests\Functional\Repository;

use OpenOrchestra\UserBundle\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class GroupRepositoryTest
 */
class GroupRepositoryTest extends KernelTestCase
{
    /**
     * @var GroupRepository
     */
    protected $repository;

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('open_orchestra_user.repository.group');
    }

    /**
     * @param array  $descriptionEntity
     * @param array  $columns
     * @param string $search
     * @param array  $order
     * @param int    $skip
     * @param int    $limit
     * @param int    $count
     *
     * @dataProvider providePaginateAndSearch
     */
    public function testFindForPaginateAndSearch($descriptionEntity, $columns, $search, $order, $skip, $limit, $count)
    {
        $groups = $this->repository->findForPaginateAndSearch($descriptionEntity, $columns, $search, $order, $skip, $limit);
        $this->assertCount($count, $groups);
    }

    /**
     * @return array
     */
    public function providePaginateAndSearch()
    {
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array($descriptionEntity, $this->generateColumnsProvider(), null, null, 0 ,5 , 5),
            array($descriptionEntity, $this->generateColumnsProvider('group'), null, null, 0 ,5 , 5),
            array($descriptionEntity, $this->generateColumnsProvider(), 'group', null, 0 ,5 , 5),
            array($descriptionEntity, $this->generateColumnsProvider(), 'fakeGroup', null, 0 ,5 , 0),
            array($descriptionEntity, $this->generateColumnsProvider(), 'First', null, 0 ,5 , 1),
        );
    }

    /**
     * test count all user
     */
    public function testCount()
    {
        $groups = $this->repository->count();
        $this->assertEquals(5, $groups);
    }

    /**
     * @param array  $columns
     * @param array  $descriptionEntity
     * @param string $search
     * @param int    $count
     *
     * @dataProvider provideColumnsAndSearchAndCount
     */
    public function testCountWithSearchFilter($descriptionEntity, $columns, $search, $count)
    {
        $groups = $this->repository->countWithSearchFilter($descriptionEntity, $columns, $search);
        $this->assertEquals($count, $groups);
    }

    /**
     * @return array
     */
    public function provideColumnsAndSearchAndCount(){
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array($descriptionEntity, $this->generateColumnsProvider(), null, 5),
            array($descriptionEntity, $this->generateColumnsProvider('group'), null, 5),
            array($descriptionEntity, $this->generateColumnsProvider('first'), null, 1),
            array($descriptionEntity, $this->generateColumnsProvider(), 'fakeName', 0),
        );
    }

    /**
     * Generate columns of content with search value
     *
     * @param string $searchName
     *
     * @return array
     */
    protected function generateColumnsProvider($searchName = '')
    {
        return array(
            array('name' => 'name', 'searchable' => true, 'orderable' => true, 'search' => array('value' => $searchName)),
        );
    }

    /**
     * Generate relation between columns names and entities attributes
     *
     * @return array
     */
    protected function getDescriptionColumnEntity()
    {
        return array(
            'name' => array('key' => 'name')
        );
    }

}
