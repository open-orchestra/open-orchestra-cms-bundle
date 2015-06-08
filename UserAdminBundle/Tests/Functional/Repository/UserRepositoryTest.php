<?php

namespace OpenOrchestra\UserAdminBundle\Tests\Functional\Repository;

use OpenOrchestra\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserRepositoryTest
 */
class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * Set up test
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->repository = static::$kernel->getContainer()->get('open_orchestra_user.repository.user');
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
        $users = $this->repository->findForPaginateAndSearch($descriptionEntity, $columns, $search, $order, $skip, $limit);
        $this->assertCount($count, $users);
    }

    /**
     * @return array
     */
    public function providePaginateAndSearch()
    {
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array($descriptionEntity, $this->generateColumnsProvider(), null, null, 0 ,5 , 4),
            array($descriptionEntity, $this->generateColumnsProvider('admin'), null, null, 0 ,5 , 1),
            array($descriptionEntity, $this->generateColumnsProvider('fakeUsername'), null, null, 0 ,5 , 0),
            array($descriptionEntity, $this->generateColumnsProvider(), 'user', null, 0 ,5 , 3),
        );
    }

    /**
     * test count all user
     */
    public function testCount()
    {
        $users = $this->repository->count();
        $this->assertEquals(4, $users);
    }

    /**
     * @param array  $columns
     * @param array  $descriptionEntity
     * @param string $search
     * @param int    $count
     *
     * @dataProvider provideColumnsAndSearchAndCount
     */
    public function testCountFilterSearch($descriptionEntity, $columns, $search, $count)
    {
        $users = $this->repository->countWithSearchFilter($descriptionEntity, $columns, $search);
        $this->assertEquals($count, $users);
    }

    /**
     * @return array
     */
    public function provideColumnsAndSearchAndCount()
    {
        $descriptionEntity = $this->getDescriptionColumnEntity();

        return array(
            array($descriptionEntity, $this->generateColumnsProvider(), null, 4),
            array($descriptionEntity, $this->generateColumnsProvider('admin'), null, 1),
            array($descriptionEntity, $this->generateColumnsProvider('user'), null, 3),
            array($descriptionEntity, $this->generateColumnsProvider(), 'admin', 1),
        );
    }

    /**
     * Generate columns of content with search value
     *
     * @param string $searchUsername
     *
     * @return array
     */
    protected function generateColumnsProvider($searchUsername = '')
    {
        return array(
            array('name' => 'username', 'searchable' => true, 'orderable' => true, 'search' => array('value' => $searchUsername)),
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
            'username' => array('key' => 'username')
        );
    }

}
