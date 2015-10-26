<?php

namespace OpenOrchestra\BackofficeBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraFunctionalFixturesInterface;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadNodeRouteDocumentData
 */
class LoadNodeRouteDocumentData extends AbstractFixture implements OrderedFixtureInterface, OrchestraFunctionalFixturesInterface, ContainerAwareInterface, OrchestraProductionFixturesInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $nodeRepository = $this->container->get('open_orchestra_model.repository.node');
        $nodes = $nodeRepository->findBy(array('nodeType' => NodeInterface::TYPE_DEFAULT));
        $updateRoute = $this->container->get('open_orchestra_backoffice.manager.route_document');

        foreach ($nodes as $node) {
            $routes = $updateRoute->createForNode($node);
            foreach ($routes as $route) {
                $manager->persist($route);
            }
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1200;
    }
}
