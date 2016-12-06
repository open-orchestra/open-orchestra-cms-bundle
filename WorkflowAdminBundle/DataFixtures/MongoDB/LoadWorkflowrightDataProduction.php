<?php

namespace OpenOrchestra\WorkflowAdminBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\DataFixtures\OrchestraProductionFixturesInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowRightInterface;
use OpenOrchestra\ModelBundle\Document\Authorization;
use OpenOrchestra\ModelBundle\Document\WorkflowRight;

/**
 * Class LoadWorkflowRightDataProduction
 */
class LoadWorkflowRightDataProduction extends AbstractFixture implements OrderedFixtureInterface, OrchestraProductionFixturesInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (true == $this->hasReference('workflow-function-validator-production')) {
            $workflowRight = new WorkflowRight();
            $workflowRight->setUserId($this->getReference('user-admin')->getId());

            $authorization = new Authorization();
            $authorization->setReferenceId(WorkflowRightInterface::NODE);
            $authorization->addWorkflowFunction($this->getReference('workflow-function-validator-production'));

            $workflowRight->addAuthorization($authorization);

            $manager->persist($workflowRight);
            $manager->flush();
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1010;
    }
}
