<?php

namespace OpenOrchestra\UserBundle\Tests\Functional\Repository;

use OpenOrchestra\GroupBundle\Repository\GroupRepository;
use OpenOrchestra\ModelInterface\Repository\Configuration\FinderConfiguration;
use OpenOrchestra\ModelInterface\Repository\Configuration\PaginateFinderConfiguration;

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
     * @param int    $skip
     * @param int    $limit
     * @param int    $count
     *
     * @dataProvider providePaginate
     */
    public function testFindForPaginate($descriptionEntity, $columns, $search, $skip, $limit, $count)
    {
        $configuration = new PaginateFinderConfiguration();
        $configuration->setColumns($columns);
        $configuration->setLimit($limit);
        $configuration->setDescriptionEntity($descriptionEntity);
        $configuration->setSearch($search);
        $configuration->setSkip($skip);

        $groups = $this->repository->findForPaginate($configuration);
        $this->assertCount($count, $groups);
    }

    /**
     * @return array
     */
    public function providePaginate()
    {
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array($descriptionEntity, $this->generateColumnsProvider(), null, 0 ,5 , 5),
            array($descriptionEntity, $this->generateColumnsProvider('group'), null, 0 ,5 , 5),
            array($descriptionEntity, $this->generateColumnsProvider(), 'group', 0 ,5 , 5),
            array($descriptionEntity, $this->generateColumnsProvider(), 'fakeGroup', 0 ,5 , 0),
            array($descriptionEntity, $this->generateColumnsProvider(), 'First', 0 ,5 , 1),
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
    public function testCountWithFilter($descriptionEntity, $columns, $search, $count)
    {
        $configuration = new FinderConfiguration();
        $configuration->setDescriptionEntity($descriptionEntity);
        $configuration->setSearch($search);
        $configuration->setColumns($columns);
        $groups = $this->repository->countWithFilter($configuration);
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
